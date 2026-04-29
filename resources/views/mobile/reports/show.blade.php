<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('mobile.report_detail') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] min-h-screen font-sans pb-10">
    
    <div class="bg-white px-5 py-4 flex items-center sticky top-0 z-10 shadow-sm">
        <a href="{{ route('mobile.reports') }}" class="text-gray-600 p-1 -ml-1">
            <x-heroicon-o-chevron-left class="w-6 h-6" />
        </a>
        <h1 class="text-lg font-medium text-gray-800 ml-4">{{ __('mobile.report_detail') }}</h1>
    </div>

    <div class="px-5 mt-6 space-y-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span class="inline-block px-2 py-1 bg-green-50 text-green-600 text-xs font-medium rounded-md mb-2">
                        @if($report->assessment_type == 1) {{ __('mobile.type_initial') }}
                        @elseif($report->assessment_type == 2) {{ __('mobile.type_reassessment') }}
                        @else {{ __('mobile.type_final') }} @endif
                    </span>
                    <h3 class="text-base font-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($report->assessment_date)->format('Y-m-d') }} {{ __('mobile.assessment_date') }}
                    </h3>
                </div>
                <x-heroicon-o-clipboard-document-check class="w-8 h-8 text-blue-100" />
            </div>
            <div class="text-xs text-gray-400">{{ __('mobile.assessment_no') }}: {{ $report->assessment_no }}</div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-sm font-medium text-gray-800 mb-4">{{ __('mobile.basic_metrics') }}</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <div class="text-xs text-gray-500 mb-1">{{ __('mobile.height') }}</div>
                    <div class="text-lg font-bold text-gray-800">{{ $report->height ?? __('mobile.no_data') }}</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <div class="text-xs text-gray-500 mb-1">{{ __('mobile.weight') }}</div>
                    <div class="text-lg font-bold text-gray-800">{{ $report->weight ?? __('mobile.no_data') }}</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <div class="text-xs text-gray-500 mb-1">{{ __('mobile.bmi') }}</div>
                    <div class="text-lg font-bold text-gray-800">{{ $report->bmi ?? __('mobile.no_data') }}</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <div class="text-xs text-gray-500 mb-1">{{ __('mobile.body_fat_rate') }}</div>
                    <div class="text-lg font-bold text-gray-800">{{ $report->body_fat_rate ?? __('mobile.no_data') }}</div>
                </div>
            </div>
        </div>

        @if($report->posture_analysis)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-sm font-medium text-gray-800 mb-4">{{ __('mobile.posture_analysis') }}</h3>
            <div class="flex flex-wrap gap-2">
                @foreach(explode(',', $report->posture_analysis) as $tag)
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-medium rounded-full">{{ trim($tag) }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if($report->therapist_remark)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="text-sm font-medium text-gray-800 mb-4">{{ __('mobile.therapist_remark') }}</h3>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $report->therapist_remark }}</p>
        </div>
        @endif
    </div>
</body>
</html>
