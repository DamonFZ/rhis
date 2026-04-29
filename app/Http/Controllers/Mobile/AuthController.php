<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function bindConfirm(Request $request)
    {
        // 1. 校验数据库中的 bind_token 是否匹配
        $patient = PatientProfile::where('id', $request->patient_id)
            ->where('bind_token', $request->token)
            ->firstOrFail();

        // 2. 微信授权逻辑
        if (app()->isLocal()) {
            session(['easywechat.oauth_user.default' => 'mock_openid_local_dev_123']);
        } else {
            $app = app(\EasyWeChat\OfficialAccount\Application::class);
            $oauth = $app->getOAuth();

            // 优先拦截并处理带有 code 的回调请求
            if ($request->has('code')) {
                $user = $oauth->userFromCode($request->code);
                session(['easywechat.oauth_user.default' => $user]);

                return redirect()->to($request->url() . '?' . http_build_query($request->except(['code', 'state'])));
            }

            // 如果没有 session，发起微信授权请求 (修复 v7 返回字符串的问题)
            if (!session()->has('easywechat.oauth_user.default')) {
                $authUrl = $oauth->scopes(['snsapi_base'])->redirect($request->fullUrl());
                return redirect()->away($authUrl);
            }
        }

        // 3. 自动执行绑定逻辑
        $wechatUser = session('easywechat.oauth_user.default');
        $openid = app()->isLocal() ? $wechatUser : (is_array($wechatUser) ? ($wechatUser['id'] ?? null) : ($wechatUser ? $wechatUser->getId() : null));

        if ($openid) {
            // 写入 OpenID，并清空 token 使二维码立刻失效防复用
            $patient->update([
                'wechat_openid' => $openid,
                'bind_token' => null
            ]);
        }

        // 4. 绑定成功，直接重定向到大本营！(绝对不要 forget session，否则大本营会报 403)
        return redirect()->route('mobile.dashboard');
    }

    public function bindStore(Request $request)
    {
        $request->validate(['patient_id' => 'required|exists:patient_profiles,id']);
        $wechatUser = session('easywechat.oauth_user.default');
        $openid = app()->isLocal() ? $wechatUser : ($wechatUser['id'] ?? ($wechatUser ? $wechatUser->getId() : null));

        if (!$openid) {
            return back()->with('error', '未获取到微信授权信息。');
        }

        $patient = PatientProfile::findOrFail($request->patient_id);
        $patient->update([
            'wechat_openid' => $openid,
            'bind_token' => null // 绑定成功后立即使二维码失效
        ]);

        session()->forget('easywechat.oauth_user.default');

        // 绑定成功后，直接重定向到 Dashboard 首页
        return redirect()->route('mobile.dashboard');
    }
}
