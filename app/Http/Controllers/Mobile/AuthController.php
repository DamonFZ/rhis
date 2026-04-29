<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function bindConfirm(Request $request)
    {
        // 校验数据库中的 bind_token 是否匹配 (不再使用 hasValidSignature)
        $patient = PatientProfile::where('id', $request->patient_id)
            ->where('bind_token', $request->token)
            ->firstOrFail();

        // 2. 微信授权逻辑
        if (app()->isLocal()) {
            session(['easywechat.oauth_user.default' => 'mock_openid_local_dev_123']);
        } else {
            // 兼容 v7 版本的实例获取方式
            $app = app(\EasyWeChat\OfficialAccount\Application::class);
            $oauth = $app->getOAuth();

            // 修复点 1：必须优先拦截并处理带有 code 的回调请求
            if ($request->has('code')) {
                $user = $oauth->userFromCode($request->code);
                // 修复点 2：统一使用全局规范的 Session 键名，并将整个 user 对象存入（兼容对象的 getId 调用）
                session(['easywechat.oauth_user.default' => $user]);
                
                // 剔除一次性的 code 和 state 参数，重定向回干净的当前页面
                return redirect()->to($request->url() . '?' . http_build_query($request->except(['code', 'state'])));
            }

            // 修复点 3：如果没有 code，且没找到 session，才发起微信授权请求
            if (!session()->has('easywechat.oauth_user.default')) {
                return $oauth->scopes(['snsapi_base'])->redirect($request->fullUrl());
            }
        }

        // 3. 渲染视图，传入数据
        $wechatUser = session('easywechat.oauth_user.default');
        $openid = app()->isLocal() ? $wechatUser : ($wechatUser['id'] ?? ($wechatUser ? $wechatUser->getId() : null));
        
        return view('mobile.auth.bind-confirm', [
            'patient' => $patient,
            'openid' => $openid
        ]);
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
