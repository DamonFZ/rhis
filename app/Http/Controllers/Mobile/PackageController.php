<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $wechatUser = session('easywechat.oauth_user.default');
        
        if (app()->isLocal()) {
            $openid = 'mock_openid_local_dev_123';
        } else {
            $openid = is_array($wechatUser) ? ($wechatUser['id'] ?? null) : ($wechatUser ? $wechatUser->getId() : null);
        }

        if (!$openid) {
            abort(403, '无法获取微信授权信息，请在微信客户端打开。');
        }

        $patient = PatientProfile::where('wechat_openid', $openid)->first();

        if (!$patient) {
            return view('mobile.dashboard.unbound');
        }

        $packages = $patient->patientPackages()
            ->orderByRaw("CASE status WHEN 'active' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mobile.packages.index', compact('patient', 'packages'));
    }
}
