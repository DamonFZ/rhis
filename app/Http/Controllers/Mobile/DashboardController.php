<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class DashboardController extends Controller
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

        // 1. 计算有效套餐的剩余总课时
        $totalRemainingSessions = $patient->patientPackages()
            ->where('status', 'active')
            ->sum('remaining_sessions');

        // 2. 获取最近一次体态评估的日期
        $lastAssessment = $patient->physicalAssessments()
            ->orderBy('assessment_date', 'desc')
            ->first();
            
        $lastAssessmentDate = $lastAssessment ? \Carbon\Carbon::parse($lastAssessment->assessment_date)->format('Y-m-d') : '暂无评估';

        // 将数据传递给视图
        return view('mobile.dashboard.index', compact('patient', 'totalRemainingSessions', 'lastAssessmentDate'));
    }
}
