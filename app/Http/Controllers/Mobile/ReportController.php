<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use App\Models\PhysicalAssessment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $wechatUser = session('easywechat.oauth_user.default');
        $openid = app()->isLocal() ? 'mock_openid_local_dev_123' : ($wechatUser['id'] ?? ($wechatUser ? $wechatUser->getId() : null));

        if (!$openid) { abort(403, 'Unauthorized'); }
        
        $patient = PatientProfile::where('wechat_openid', $openid)->firstOrFail();

        // 查询已完成的体态评估报告，按时间倒序
        $reports = PhysicalAssessment::where('patient_profile_id', $patient->id)
            ->where('status', 1) // 1表示已完成
            ->orderByDesc('assessment_date')
            ->orderByDesc('created_at')
            ->get();

        return view('mobile.reports.index', compact('reports'));
    }
}
