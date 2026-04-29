<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use App\Models\ConsumptionRecord;
use Illuminate\Http\Request;

class UsageHistoryController extends Controller
{
    public function index(Request $request)
    {
        $wechatUser = session('easywechat.oauth_user.default');
        $openid = app()->isLocal() ? 'mock_openid_local_dev_123' : ($wechatUser['id'] ?? ($wechatUser ? $wechatUser->getId() : null));

        if (!$openid) { abort(403, 'Unauthorized'); }
        $patient = PatientProfile::where('wechat_openid', $openid)->firstOrFail();

        $records = ConsumptionRecord::where('patient_profile_id', $patient->id)
            ->orderByDesc('treatment_date')->orderByDesc('created_at')->get();

        return view('mobile.usage.index', compact('records'));
    }
}
