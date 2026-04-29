<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('mobile.rehab_reports') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] min-h-screen font-sans pb-10">
    
    <div class="bg-white px-5 py-4 flex items-center sticky top-0 z-10 shadow-sm">
        <a href="{{ route('mobile.dashboard') }}" class="text-gray-600 p-1 -ml-1">
            <x-heroicon-o-chevron-left class="w-6 h-6" />
        </a>
        <h1 class="text-lg font-medium text-gray-800 ml-4">{{ __('mobile.rehab_reports') }}</h1>
    </div>

    <div class="px-5 mt-6 space-y-4">
        @forelse($reports as $report)
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
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                    <div class="text-xs text-gray-400">{{ __('mobile.assessment_no') }}: {{ $report->assessment_no }}</div>
                    <button class="text-sm font-medium text-blue-600 flex items-center">
                        {{ __('mobile.view_detail') }}
                        <x-heroicon-m-chevron-right class="w-4 h-4 ml-1" />
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-document-magnifying-glass class="w-10 h-10 text-gray-300" />
                </div>
                <p class="text-gray-500 text-sm">{{ __('mobile.no_reports') }}</p>
            </div>
        @endforelse
    </div>
</body>
</html>
