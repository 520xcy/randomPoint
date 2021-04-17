<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Browser;

class LoginCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $wechat = $this->WeChatUser($request);

        if(!$wechat && Browser::isDesktop()){
            return redirect()->route('pcloginqr',['target_url' => urlencode($request->fullUrl())]);
        }

        if (Auth::guard($guard)->guest()) {

            if ($request->wantsJson()) {
                return response()->json(['message' => '抱歉出错啦！', 'errors' => ['错误代码' => '无权访问']], 403);
            }
            
            return redirect()->route('login',['target_url' => urlencode($request->fullUrl())]);
        }
        // 多点登录
        $user = Auth::guard($guard)->user();
        if (session('once_login') != $user->last_session) {
            if ($request->wantsJson()) {
                return response()->json(['message' => '抱歉出错啦！', 'errors' => ['错误代码' => '异地登录']], 403);
            }

            return redirect()->route('logout', ['target_url' => urlencode($request->fullUrl())]);
        }
        // 微信授权用户与当前登录用户不匹配
        if ($wechat['id'] != $user->openid) {
            if ($request->wantsJson()) {
                return response()->json(['message' => '抱歉出错啦！', 'errors' => ['错误代码' => '微信授权用户与当前登录用户不匹配']], 403);
            }
            return redirect()->route('logout', ['target_url' => urlencode($request->fullUrl())]);
        }

        return $next($request);
    }

    protected function WeChatUser($request)
    {
        $type = $request->input('wechat_type', 'service');
        $account = $request->input('wechat_account', 'default');
        $class = ('work' !== $type) ? 'wechat' : 'work';
        $sessionKey = \sprintf('%s.oauth_user.%s', $class, $account);

        // abort_unless(Session::has($sessionKey), 403, '微信授权出错');
        return session($sessionKey);
    }


}
