<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;

class LocaleController extends Controller
{
    public function switch($locale)
    {
        if (in_array($locale, ['zh_CN', 'zh_HK'])) {
            session(['mobile_locale' => $locale]);
        }
        return back();
    }
}
