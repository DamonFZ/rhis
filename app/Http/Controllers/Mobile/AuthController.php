<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function bindConfirm(Request $request)
    {
        $patient = PatientProfile::findOrFail($request->patient_id);

        // 签名校验
        $expectedSign = hash_hmac('sha256', $patient->id, config('app.key'));
        if ($request->sign !== $expectedSign) {
            abort(403, '访问被拒绝：签名校验失败。');
        }

        // 获取扫码用户的微信 OpenID
        if (app()->isLocal()) {
            $openid = session('easywechat.oauth_user.default', 'mock_openid_local_dev_123');
        } else {
            $app = app(\EasyWeChat\OfficialAccount\Application::class);
            $oauth = $app->getOAuth();

            // 优先拦截并处理带有 code 的回调请求
            if ($request->has('code')) {
                $user = $oauth->userFromCode($request->code);
                session(['easywechat.oauth_user.default' => $user]);

                return redirect()->to($request->url().'?'.http_build_query($request->except(['code', 'state'])));
            }

            // 如果没有 session，发起微信授权请求
            if (! session()->has('easywechat.oauth_user.default')) {
                $authUrl = $oauth->scopes(['snsapi_base'])->redirect($request->fullUrl());

                return redirect()->away($authUrl);
            }

            $wechatUser = session('easywechat.oauth_user.default');
            $openid = is_array($wechatUser) ? ($wechatUser['id'] ?? null) : ($wechatUser ? $wechatUser->getId() : null);
        }

        if (! $openid) {
            return back()->with('error', '未获取到微信授权信息。');
        }

        // 状态机分流逻辑
        $currentOpenId = $openid;

        // 状态 A：档案尚未绑定微信
        if (empty($patient->wechat_openid)) {
            $patient->update(['wechat_openid' => $currentOpenId]);

            return redirect()->route('mobile.dashboard')->with('success', '绑定成功');
        }

        // 状态 B：档案已绑定，且扫码者就是本人
        if ($patient->wechat_openid === $currentOpenId) {
            return redirect()->route('mobile.dashboard');
        }

        // 状态 C：档案已被其他人绑定
        abort(403, '访问被拒绝：该档案已绑定其他微信号。请联系康复师解绑。');
    }
}
