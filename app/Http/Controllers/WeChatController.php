<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeChatUsers;
use Log;
use Illuminate\Support\Arr;
use Overtrue\Socialite\User as SocialiteUser;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

class WeChatController extends CommonController
{

    public function serve(Request $request)
    {
        
        if($request->filled('echostr')){
            return $request->input('echostr');
        }

        $app = $this->WeChat();

        $app->server->push(function ($message) {
            return config('app.url');
        });

        return $app->server->serve();
    }

    public function fakeUser(Faker $faker)
    {
        // $original = [
        //     'openid' => Str::random(40),
        //     'name' => $faker->name,
        //     'nickname' => $faker->name,
        //     'headimgurl' => $faker->url,
        //     'email' => null,
        //     'original' => [],
        //     'provider' => 'WeChat',
        //     'sex'=>'男', 
        //     'language' =>'zh_CN', 
        //     'country'=>$faker->country, 
        //     'city'=>$faker->city, 
        //     'province'=>''
        // ];
        $original = [
            'openid' => '0GqA48l0a8LCsN9Mt2Uyzenz2gZmN1Qcps9gxdxE',
            'name' => $faker->name,
            'nickname' => $faker->name,
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

        $user = new SocialiteUser($original);

        session(['wechat.oauth_user.default' => $user]);

        dump(session('wechat.oauth_user.default'));
    }

    public function qrcode()
    {
        $app = $this->WeChat();

        $result = $app->qrcode->temporary('xiang', 6 * 24 * 3600);

        dump($url = $app->qrcode->url($result['ticket']));
    }
}
