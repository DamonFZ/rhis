<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('mobile.report_detail') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] min-h-screen font-sans pb-12">
    <div class="bg-white px-5 py-4 flex items-center sticky top-0 z-10 shadow-sm">
        <a href="{{ route('mobile.reports') }}" class="text-gray-600 p-1 -ml-1">
            <x-heroicon-o-chevron-left class="w-6 h-6" />
        </a>
        <h1 class="text-lg font-medium text-gray-800 ml-4">{{ __('mobile.report_detail') }}</h1>
    </div>

    <div class="px-4 mt-5 space-y-4">
        <div class="bg-blue-600 rounded-2xl p-5 shadow-md text-white">
            <div class="flex justify-between items-center mb-2">
                <span class="px-2 py-1 bg-white/20 rounded text-xs font-medium">
                    @if($report->assessment_type == 1) {{ __('mobile.type_initial') }}
                    @elseif($report->assessment_type == 2) {{ __('mobile.type_reassessment') }}
                    @else {{ __('mobile.type_final') }} @endif
                </span>
                <span class="text-sm opacity-80">{{ \Carbon\Carbon::parse($report->assessment_date)->format('Y-m-d') }}</span>
            </div>
            <div class="text-xs opacity-70 mt-4">{{ __('mobile.assessment_no') }}</div>
            <div class="text-sm font-mono tracking-wider">{{ $report->assessment_no }}</div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center">
                <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-blue-500 mr-2"/>
                {{ __('mobile.basic_metrics') }}
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="text-xs text-gray-500 mb-1">{{ __('mobile.height') }}</div>
                    <div class="font-bold text-gray-800 text-lg">{{ $report->height ?? '--' }} <span class="text-xs font-normal text-gray-400">cm</span></div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="text-xs text-gray-500 mb-1">{{ __('mobile.weight') }}</div>
                    <div class="font-bold text-gray-800 text-lg">{{ $report->weight ?? '--' }} <span class="text-xs font-normal text-gray-400">kg</span></div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="text-xs text-gray-500 mb-1">{{ __('mobile.bmi') }}</div>
                    <div class="font-bold text-gray-800 text-lg">{{ $report->bmi ?? '--' }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <div class="text-xs text-gray-500 mb-1">{{ __('mobile.body_fat_rate') }}</div>
                    <div class="font-bold text-gray-800 text-lg">{{ $report->body_fat_rate ?? '--' }} <span class="text-xs font-normal text-gray-400">%</span></div>
                </div>
            </div>
        </div>

        @php
            $dict = [
                'chest'=>'胸围', 'waist'=>'腰围', 'hip'=>'臀围', 'left_arm'=>'左臂围', 'right_arm'=>'右臂围', 'left_thigh'=>'左大腿围', 'right_thigh'=>'右大腿围',
                'trunk'=>'躯干', 'iliopsoas'=>'髂腰肌', 'hamstrings'=>'腘绳肌', 'quadriceps'=>'股四头肌', 'calf'=>'小腿肌群', 'shoulder_1'=>'肩部1', 'shoulder_2'=>'肩部2',
                'back_foot'=>'足弓', 'back_knee'=>'膝关节', 'back_pelvis'=>'骨盆', 'back_scapula'=>'肩胛骨', 'back_cervical'=>'颈椎', 
                'back_shoulder'=>'肩部', 'back_thoracolumbar'=>'胸腰椎',
                'side_head'=>'头部', 'side_knee'=>'膝关节', 'side_lumbar'=>'腰椎', 'side_pelvis'=>'骨盆', 
                'side_cervical'=>'颈椎', 'side_thoracic'=>'胸椎', 'side_scapula_'=>'肩胛骨'
            ];
            $t = function($k) use ($dict) { return $dict[$k] ?? $k; };

            $circumferences = is_string($report->circumference) ? json_decode($report->circumference, true) : $report->circumference;
            $flexibilities = is_string($report->flexibility) ? json_decode($report->flexibility, true) : $report->flexibility;
            $postureTags = is_string($report->posture_tags) ? json_decode($report->posture_tags, true) : $report->posture_tags;
            
            // 分组
            $backTags = [];
            $sideTags = [];
            foreach($postureTags as $key => $val) {
                if(!empty($val)) {
                    if(str_starts_with($key, 'back_')) {
                        $backTags[$key] = $val;
                    } elseif(str_starts_with($key, 'side_')) {
                        $sideTags[$key] = $val;
                    }
                }
            }
        @endphp

        @if(!empty($circumferences))
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center">
                <x-heroicon-o-arrows-pointing-out class="w-5 h-5 text-indigo-500 mr-2"/>
                {{ __('mobile.circumference') }}
            </h3>
            <div class="grid grid-cols-2 gap-3">
                @foreach($circumferences as $key => $val)
                    @if(!empty($val))
                    <div class="flex justify-between items-center bg-gray-50 rounded-xl p-3 border border-gray-100">
                        <span class="text-sm text-gray-500">{{ $t($key) }}</span>
                        <span class="font-bold text-gray-800">{{ $val }} <span class="text-xs font-normal text-gray-400">cm</span></span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($flexibilities))
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center">
                <x-heroicon-o-face-smile class="w-5 h-5 text-emerald-500 mr-2"/>
                {{ __('mobile.flexibility') }}
            </h3>
            <div class="flex flex-wrap gap-2.5">
                @foreach($flexibilities as $key => $val)
                    @if(!empty($val))
                        <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 text-sm rounded-lg whitespace-nowrap">
                            <span class="opacity-70 mr-1">{{ $t($key) }}:</span>
                            <span class="font-medium">{{ is_array($val) ? implode(',', $val) : $val }}</span>
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($backTags))
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center">
                <x-heroicon-o-user-circle class="w-5 h-5 text-orange-500 mr-2"/>
                体态评估-背面
            </h3>
            <div class="flex flex-wrap gap-2.5">
                @foreach($backTags as $key => $val)
                    <span class="px-3 py-1.5 bg-orange-50 text-orange-600 text-sm rounded-lg whitespace-nowrap">
                        <span class="opacity-70 mr-1">{{ $t($key) }}:</span>
                        <span class="font-medium">{{ is_array($val) ? implode(',', $val) : $val }}</span>
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($sideTags))
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center">
                <x-heroicon-o-user-circle class="w-5 h-5 text-purple-500 mr-2"/>
                体态评估-侧面
            </h3>
            <div class="flex flex-wrap gap-2.5">
                @foreach($sideTags as $key => $val)
                    <span class="px-3 py-1.5 bg-purple-50 text-purple-600 text-sm rounded-lg whitespace-nowrap">
                        <span class="opacity-70 mr-1">{{ $t($key) }}:</span>
                        <span class="font-medium">{{ is_array($val) ? implode(',', $val) : $val }}</span>
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-3 flex items-center">
                <x-heroicon-o-chat-bubble-bottom-center-text class="w-5 h-5 text-blue-500 mr-2"/>
                {{ __('mobile.therapist_remark') }}
            </h3>
            <div class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-xl">
                {!! nl2br(e($report->remark ?: __('mobile.no_data'))) !!}
            </div>
        </div>
    </div>
</body>
</html>
