<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 修正点 1：将 wechat 改为 easywechat
        $wechatUser = session('easywechat.oauth_user.default');

        // 修正点 2：本地 mock 与真实环境的对象获取
        if (app()->isLocal()) {
            $openid = 'mock_openid_local_dev_123';
        } else {
            // 直接调用对象方法获取 OpenID
            $openid = $wechatUser ? $wechatUser->getId() : null;
        }

        if (!$openid) {
            abort(403, '无法获取微信授权信息，请在微信客户端打开。');
        }

        // 根据 OpenID 查找对应的客户档案
        $patient = PatientProfile::where('wechat_openid', $openid)->first();

        if (!$patient) {
            return view('mobile.dashboard.unbound');
        }

        return view('mobile.dashboard.index', compact('patient'));
    }
}
