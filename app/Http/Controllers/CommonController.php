<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\CustomClass\ValidatorMessages;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use App\Models\User;

use Browser;

class CommonController extends Controller
{
    public function __construct()
    {
    }

    public function point(Request $request)
    {
        $user = $this->auth_user()->user();
        return view('index', ['user' => $user]);
        
    }

    protected function WeChat()
    {
        return app('wechat.official_account');
    }

    protected function updateWeChatUser(Object $user)
    {
        $data = [
            'openid' => $user['original']['openid'],
            'name' => $user['original']['nickname'],
            // 'sex' => $user['original']['sex'],
            // 'headimgurl' => $user['original']['headimgurl'],
            // 'language' => $user['original']['language'],
            // 'country' => $user['original']['country'],
            // 'city' => $user['original']['city'],
            // 'province' => $user['original']['province']
        ];

        return User::updateOrCreate(
            ['openid' => $data['openid']],
            $data
        );
    }

    protected function WeChatUser(Request $request)
    {
        $type = $request->input('wechat_type', 'service');
        $account = $request->input('wechat_account', 'default');
        $class = ('work' !== $type) ? 'wechat' : 'work';
        $sessionKey = \sprintf('%s.oauth_user.%s', $class, $account);

        abort_unless(Session::has($sessionKey), 403, '微信授权出错');

        return session($sessionKey);
    }

    protected function auth_user()
    {
        return Auth::guard('random');
    }

    protected function validator(array $data, array $rule)
    {

        $validator = Validator::make($data, $rule);
        if ($validator->fails()) {
            //   $validator->after(function ($validator) {
            //           // $PostToken = new PostToken;
            //           $validator->errors()->add('message', '请根据提示更改内容');
            //   });

        }

        return $validator;
    }

    protected function error(String $msg, Object $e, String $router)
    {
        $errorid = (string)Str::uuid();
        \Log::channel('error')->error(sprintf('[%s]%s Message:%s; File:%s:%s; Router:%s', $errorid, $msg, $e->getMessage(), $e->getFile(), $e->getLine(), $router));

        if (config('app.debug')) {
            throw $e;
            // $error = [
            //     'Message' => $e->getMessage(),
            //     'File' => $e->getFile() . ':' . $e->getLine()
            // ];
        } else {
            $error = ['错误代码' => sprintf('错误代码:<br>%s<br>%s<br>请联系管理员', $errorid, $e->getMessage())];
        }

        return $this->response_error($error);
    }

    protected function response_error(array $error)
    {
        abort_unless(\Request::wantsJson(), 422, implode(' | ', $error));
        return response()->json(['message' => '抱歉出错啦！', 'errors' => $error], 422);
    }

    protected function info(String $msg)
    {
        $msg = $msg . ' 来自:' . \Request::getClientIp() . '; 完整路径:' . \Request::fullUrl() . '; Referer:' . \Request::header('referer') . '; User-Agent:' . \Request::userAgent() . '; 方法:' . \Request::url() . ' ' . \Request::method() . '; 请求:' . json_encode(\Request::toArray()) . '; 路由:' . json_encode(\Request::route()->getAction());
        \Log::channel('info')->info($msg);
    }
}
