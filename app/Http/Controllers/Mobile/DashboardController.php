<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $wechatUser = session('wechat.oauth_user.default');
        $openid = app()->isLocal() ? 'mock_openid_local_dev_123' : $wechatUser?->getId();

        if (!$openid) {
            abort(403, '无法获取微信授权信息，请在微信客户端打开。');
        }

        $patient = PatientProfile::where('wechat_openid', $openid)->first();

        if (!$patient) {
            return view('mobile.dashboard.unbound');
        }

        return view('mobile.dashboard.index', compact('patient'));
    }
}
