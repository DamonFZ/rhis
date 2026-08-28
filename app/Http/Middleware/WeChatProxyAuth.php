<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeChatProxyAuth
{
    /**
     * Handle an incoming request for WeChat OAuth proxy authentication.
     */
    public function handle(Request $request, Closure $next): \Symfony\Component\HttpFoundation\Response
    {
        // 本地开发环境：使用 mock openid，跳过网关授权
        if (app()->isLocal()) {
            if (! session()->has('easywechat.oauth_user.default')) {
                session(['easywechat.oauth_user.default' => ['id' => 'mock_openid_local_dev_123']]);
                $request->session()->save();
            }

            return $next($request);
        }

        // Step 1: 已登录直接放行（web guard 或已有微信 session）
        if (auth('web')->check() || session()->has('easywechat.oauth_user.default')) {
            return $next($request);
        }

        $gateway = rtrim(config('wechat.gateway'), '/');

        // Step 2: 无 ticket 参数 → 重定向到网关发起授权
        if (! $request->has('ticket')) {
            $targetUrl = $request->fullUrl();

            return redirect()->away($gateway.'/auth/redirect?target_url='.urlencode($targetUrl));
        }

        // Step 3: 有 ticket → 向网关验证
        $ticket = $request->query('ticket');

        try {
            $response = Http::timeout(10)->get($gateway.'/api/auth/verify', [
                'ticket' => $ticket,
            ]);

            if (! $response->successful()) {
                Log::error('WeChatProxyAuth: 网关验证失败', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'ticket' => $ticket,
                ]);

                abort(500, 'OAuth 授权验证失败，请稍后重试');
            }

            $data = $response->json();

            $openid = $data['openid'] ?? ($data['data']['openid'] ?? null);

            if (! $openid) {
                Log::error('WeChatProxyAuth: 响应中缺少 openid', [
                    'response' => $data,
                    'ticket' => $ticket,
                ]);

                abort(500, 'OAuth 授权数据异常，请稍后重试');
            }
        } catch (\Throwable $e) {
            Log::error('WeChatProxyAuth: 网关请求异常', [
                'message' => $e->getMessage(),
                'ticket' => $ticket,
            ]);

            abort(500, 'OAuth 网关连接失败，请稍后重试');
        }

        // Step 4: 鉴权与落盘 — 写入 session 供业务控制器读取
        session(['easywechat.oauth_user.default' => ['id' => $openid]]);
        $request->session()->save();

        // Step 5: URL 净化 — 剥离 ticket 参数后重定向
        $params = $request->except(['ticket']);
        $cleanUrl = $request->url();

        if (! empty($params)) {
            $cleanUrl .= '?'.http_build_query($params);
        }

        return redirect()->to($cleanUrl);
    }
}
