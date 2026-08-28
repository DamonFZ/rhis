<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // 移动端 H5 页面：重定向到统一 OAuth 授权网关
        if ($request->is('mobile/*')) {
            $gateway = rtrim(config('wechat.gateway'), '/');

            return $gateway.'/auth/redirect?target_url='.urlencode($request->fullUrl());
        }

        return route('login');
    }
}
