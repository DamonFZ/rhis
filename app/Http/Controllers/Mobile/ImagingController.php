<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ImagingRecord;
use App\Models\PatientProfile;
use Illuminate\Http\Request;

class ImagingController extends Controller
{
    public function index(Request $request)
    {
        $openid = $this->getOpenId();
        $patient = PatientProfile::where('wechat_openid', $openid)->firstOrFail();

        $records = ImagingRecord::where('patient_profile_id', $patient->id)
            ->orderByDesc('treatment_date')
            ->orderByDesc('created_at')
            ->get();

        return view('mobile.imaging.index', compact('records'));
    }

    public function compare(Request $request)
    {
        $request->validate([
            'id1' => 'required|exists:imaging_records,id',
            'id2' => 'required|exists:imaging_records,id',
        ]);

        $openid = $this->getOpenId();
        $patient = PatientProfile::where('wechat_openid', $openid)->firstOrFail();

        $recordA = ImagingRecord::where('id', $request->id1)->where('patient_profile_id', $patient->id)->firstOrFail();
        $recordB = ImagingRecord::where('id', $request->id2)->where('patient_profile_id', $patient->id)->firstOrFail();

        // 按部位配对重组
        $angles = [
            'front' => '站姿正面',
            'forward_bending' => '前屈位',
            'back' => '站姿背面',
            'left_side' => '左侧位',
            'right_side' => '右侧位',
            'back_sitting' => '坐姿背面',
        ];

        $pairs = [];
        foreach ($angles as $key => $label) {
            $urlA = ($recordA->photo_urls[$key] ?? null) ? asset('storage/' . $recordA->photo_urls[$key]) : null;
            $urlB = ($recordB->photo_urls[$key] ?? null) ? asset('storage/' . $recordB->photo_urls[$key]) : null;

            if ($urlA && $urlB) {
                $pairs[$key] = [
                    'label' => $label,
                    'before' => $urlA,
                    'after' => $urlB,
                    'date_before' => $recordA->treatment_date,
                    'date_after' => $recordB->treatment_date,
                ];
            }
        }

        return view('mobile.imaging.compare', compact('pairs', 'recordA', 'recordB'));
    }

    private function getOpenId(): string
    {
        $wechatUser = session('easywechat.oauth_user.default');

        return app()->isLocal() ? 'mock_openid_local_dev_123' : ($wechatUser['id'] ?? ($wechatUser ? $wechatUser->getId() : null));
    }
}
