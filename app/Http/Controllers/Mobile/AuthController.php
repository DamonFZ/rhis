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

        // 2. 微信授权逻辑 (加入本地开发绕过机制)
        if (app()->isLocal()) {
            // 本地环境：直接 Mock 一个 OpenID 存入 Session
            session(['wechat_oauth_user' => 'mock_openid_local_dev_123']);
        } else {
            // 生产环境：真实的微信 OAuth 流程
            $app = app('wechat.official_account');
            $oauth = $app->getOAuth();

            if (!session()->has('wechat_oauth_user')) {
                return $oauth->scopes(['snsapi_base'])->redirect($request->fullUrl());
            }
            if ($request->has('code')) {
                $user = $oauth->userFromCode($request->code);
                session(['wechat_oauth_user' => $user->getId()]);
                return redirect()->route('mobile.bind', $request->except(['code', 'state']));
            }
        }

        // 3. 渲染视图，传入数据
        return view('mobile.auth.bind-confirm', [
            'patient' => $patient,
            'openid' => session('wechat_oauth_user')
        ]);
    }

    public function bindStore(Request $request)
    {
        $request->validate(['patient_id' => 'required|exists:patient_profiles,id']);
        $openid = session('wechat_oauth_user');

        if (!$openid) {
            return back()->with('error', '未获取到微信授权信息。');
        }

        $patient = PatientProfile::findOrFail($request->patient_id);
        $patient->update([
            'wechat_openid' => $openid,
            'bind_token' => null // 绑定成功后立即使二维码失效
        ]);

        session()->forget('wechat_oauth_user');

        // TODO: 暂时返回成功提示，后续改为重定向到个人中心
        return "绑定成功！您的档案已关联微信 OpenID：" . $openid;
    }
}
