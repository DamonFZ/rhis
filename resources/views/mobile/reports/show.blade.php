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

        @php $circumferences = is_string($report->circumference) ? json_decode($report->circumference, true) : $report->circumference; @endphp
        @if(!empty($circumferences))
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center">
                <x-heroicon-o-arrows-pointing-out class="w-5 h-5 text-indigo-500 mr-2"/>
                {{ __('mobile.circumference') }}
            </h3>
            <div class="grid grid-cols-3 gap-3">
                @foreach($circumferences as $key => $val)
                    <div class="bg-indigo-50/50 rounded-lg p-2 text-center border border-indigo-50">
                        <div class="text-xs text-gray-500">{{ $key }}</div>
                        <div class="font-bold text-gray-800 mt-0.5">{{ $val ?: '--' }} <span class="text-[10px] font-normal text-gray-400">cm</span></div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @php $flexibilities = is_string($report->flexibility) ? json_decode($report->flexibility, true) : $report->flexibility; @endphp
        @if(!empty($flexibilities))
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-3 flex items-center">
                <x-heroicon-o-face-smile class="w-5 h-5 text-emerald-500 mr-2"/>
                {{ __('mobile.flexibility') }}
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($flexibilities as $key => $val)
                    @if(!empty($val))
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs rounded border border-emerald-100">{{ $key }}: {{ is_array($val) ? implode(',', $val) : $val }}</span>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        @php $postureTags = is_string($report->posture_tags) ? json_decode($report->posture_tags, true) : $report->posture_tags; @endphp
        @if(!empty($postureTags))
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-3 flex items-center">
                <x-heroicon-o-user-circle class="w-5 h-5 text-orange-500 mr-2"/>
                {{ __('mobile.posture_analysis') }}
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($postureTags as $key => $val)
                    @if(!empty($val))
                        <span class="px-2.5 py-1 bg-orange-50 text-orange-600 text-xs rounded border border-orange-100">
                            {{ is_numeric($key) ? '' : $key . ': ' }}{{ is_array($val) ? implode(',', $val) : $val }}
                        </span>
                    @endif
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
