<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetMobileLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = ['zh_CN', 'zh_HK', 'zh_TW'];
        $locale = $request->query('lang', $request->header('Accept-Language', 'zh_CN'));
        
        if (in_array($locale, $availableLocales)) {
            app()->setLocale($locale);
        } else {
            app()->setLocale('zh_CN');
        }

        return $next($request);
    }
}
