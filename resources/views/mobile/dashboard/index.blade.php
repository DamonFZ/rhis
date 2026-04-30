<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>康复大本营</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] min-h-screen font-sans">
    
    <div class="bg-[#1E4D7B] rounded-b-3xl pt-10 pb-20 px-6 shadow-md relative">
        <div class="absolute top-4 right-5 z-10">
            @if(app()->getLocale() == 'zh_CN')
                <a href="{{ route('mobile.lang.switch', 'zh_HK') }}" class="text-sm font-medium text-white/80 hover:text-white">繁體</a>
            @else
                <a href="{{ route('mobile.lang.switch', 'zh_CN') }}" class="text-sm font-medium text-white/80 hover:text-white">简体</a>
            @endif
        </div>
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-gray-200 rounded-full border-2 border-white flex items-center justify-center overflow-hidden">
                <x-heroicon-s-user class="w-10 h-10 text-gray-400 mt-2" />
            </div>
            <div class="text-white">
                <h1 class="text-xl font-medium tracking-wide">{{ $patient->name }}</h1>
                <p class="text-[#8FB3D9] text-sm mt-1 tracking-wider">{{ $patient->patient_id }}</p>
            </div>
        </div>
    </div>

    <div class="px-5 -mt-12 grid grid-cols-2 gap-4 relative z-20">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center aspect-[4/5]">
            <div class="mb-3">
                <x-heroicon-o-rectangle-stack class="w-10 h-10 text-gray-600" />
            </div>
            <div class="text-sm font-medium text-gray-800 leading-snug">{{ __('mobile.remaining_sessions') }}<br>{{ $totalRemainingSessions }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center aspect-[4/5]">
            <div class="mb-3">
                <x-heroicon-o-clipboard-document-check class="w-10 h-10 text-gray-600" />
            </div>
            <div class="text-sm font-medium text-gray-800 leading-snug">{{ __('mobile.last_assessment') }}<br>{{ $lastAssessmentDate }}</div>
        </div>
    </div>

    <div class="px-8 mt-10 grid grid-cols-2 gap-y-10 gap-x-6">
        <a href="{{ route('mobile.packages') }}" class="flex flex-col items-center cursor-pointer">
            <div class="w-14 h-14 mb-2 flex items-center justify-center bg-[#E8F1F9] rounded-full">
                <x-heroicon-o-archive-box class="w-10 h-10 text-gray-700" />
            </div>
            <span class="text-sm font-medium text-gray-800">{{ __('mobile.my_packages') }}</span>
        </a>
        <a href="{{ route('mobile.usage_history') }}" class="flex flex-col items-center cursor-pointer block">
            <div class="w-14 h-14 mb-2 flex items-center justify-center bg-[#F0F8F4] rounded-full">
                <x-heroicon-o-clock class="w-10 h-10 text-gray-700" />
            </div>
            <span class="text-sm font-medium text-gray-800">{{ __('mobile.usage_history') }}</span>
        </a>
        <a href="{{ route('mobile.reports') }}" class="flex flex-col items-center cursor-pointer block">
            <div class="w-14 h-14 mb-2 flex items-center justify-center bg-[#FFF5E8] rounded-full">
                <x-heroicon-o-document-chart-bar class="w-10 h-10 text-gray-700" />
            </div>
            <span class="text-sm font-medium text-gray-800">{{ __('mobile.rehab_reports') }}</span>
        </a>
        <div class="flex flex-col items-center cursor-pointer">
            <div class="w-14 h-14 mb-2 flex items-center justify-center bg-[#F3E8FA] rounded-full">
                <x-heroicon-o-calendar-days class="w-10 h-10 text-gray-700" />
            </div>
            <span class="text-sm font-medium text-gray-800">{{ __('mobile.my_appointments') }}</span>
        </div>
    </div>
</body>
</html>
