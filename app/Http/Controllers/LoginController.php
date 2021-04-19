<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PcLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Validation\Rule;
use Overtrue\Socialite\User as SocialiteUser;
use Faker\Generator as Faker;

class LoginController extends CommonController
{

    public function __construct()
    {
    }

    public function pcloginCheck(Request $request, Faker $faker)
    {
        $checked = PcLogin::find($request->input('random_key'));
        if ($checked) {
            $user = User::find($checked->openid);
            if ($user) {
                $type = $request->input('wechat_type', 'service');
                $account = $request->input('wechat_account', 'default');
                $class = ('work' !== $type) ? 'wechat' : 'work';
                $sessionKey = \sprintf('%s.oauth_user.%s', $class, $account);

                $original = [
                    'openid' => $user->openid,
                    'name' => $user->name,
                    'nickname' => $user->name,
                    'headimgurl' => $faker->url,
                    'email' => null,
                    'original' => [],
                    'provider' => 'WeChat',
                    'sex' => '男',
                    'language' => 'zh_CN',
                    'country' => $faker->country,
                    'city' => $faker->city,
                    'province' => ''
                ];
                $original['original'] = $original;

                $original['id'] = $original['original']['openid'];

                $wechatuser = new SocialiteUser($original);

                session([$sessionKey => $wechatuser]);

                return response()->json(['state' => 200, 'message' => '登录成功']);
            }
        }
        return response()->json(['message' => '未登录'], 422);
    }

    public function login(Request $request)
    {
        $targetUrl = $request->input('target_url', route('index'));

        $wechatuser = $this->WeChatUser($request);

        $wechat = $this->updateWeChatUser($wechatuser);

        $this->loginId($wechat);

        return $this->redirect($targetUrl);
    }

    public function update(Request $request)
    {
        $wechatuser = $this->WeChatUser($request);

        $wechatuser = User::find($wechatuser['id']);

        $rule = [
            'name' => ['required', 'string', Rule::unique('users')->ignore($wechatuser)],
        ];

        $data = $request->except('_token');

        $this->validator($data, $rule)->validate();

        // $cookie = Cookie::make('once_login', $once_login, 24 * 60 * 365);
        DB::beginTransaction();
        try {


            $wechatuser->name = $data['name'];

            // Cookie::make('once_login', $once_login, 24 * 60 * 365);
            DB::commit();

            $this->info('登记完成');
            return response()->json(['message' => '登记完成']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('重置密码失败;', $e, json_encode($request->route()->getAction()));
        }
    }

    public function logout(Request $request)
    {
        $this->auth_user()->logout();

        session()->flush();

        $targetUrl = $request->input('target_url', route('index'));

        return $this->redirect($targetUrl);
    }


    protected function redirect(String $targetUrl)
    {
        return redirect()->away(urldecode($targetUrl));
    }

    protected function loginId(Object $user)
    {

        $once_login = bcrypt('xiang');

        session(['once_login' => $once_login]);

        $this->auth_user()->loginUsingId($user->openid, true);

        $user->last_session = $once_login;

        $user->save();

        return $once_login;
    }

    public function pcLogin(Request $request)
    {
        $wechatuser = $this->WeChatUser($request);

        $random_key = $request->input('random');

        $date = date('Y|m|d|H|i');

        $data = [
            'random_key' => $random_key,
            'openid' => $wechatuser->id,
            'is_active' => 1
        ];

        DB::beginTransaction();
        try {

            if (!Hash::check($date, $random_key)) {
                throw new \Exception('二维码已过期');
            }
            // Cookie::make('once_login', $once_login, 24 * 60 * 365);
            $this->updateWeChatUser($wechatuser);

            PcLogin::create($data);

            DB::commit();

            $this->info('PC扫码成功。openid:' . $wechatuser->id);

            return view('wxclose')->with(['message' => 'PC扫码成功']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('扫码失败;', $e, json_encode($request->route()->getAction()));
        }
    }

    public function pcLoginQR(Request $request)
    {
        $date = date('Y|m|d|H|i');

        $hashed = Hash::make($date);

        $redirect_url = route('pclogin') . '?random=' . $hashed;

        $redirect_url = urlencode($redirect_url);

        $appid = config('wechat.official_account.default.app_id');

        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_url}&response_type=code&scope=snsapi_userinfo#wechat_redirect";

        $target_url = $request->input('target_url', route('index'));
        return view('pclogin', ['url' => $url, 'hash' => $hashed, 'target_url' => urldecode($target_url)]);
    }
}
