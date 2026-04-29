<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetMobileLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('mobile_locale')) {
            app()->setLocale(session('mobile_locale'));
        } else {
            $language = $request->server('HTTP_ACCEPT_LANGUAGE');
            if ($language && (str_contains(strtolower($language), 'zh-hk') || str_contains(strtolower($language), 'zh-tw'))) {
                app()->setLocale('zh_HK');
            } else {
                app()->setLocale('zh_CN');
            }
        }
        return $next($request);
    }
}
