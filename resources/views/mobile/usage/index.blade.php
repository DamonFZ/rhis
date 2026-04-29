<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('mobile.usage_history') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] min-h-screen font-sans pb-10">
    <div class="bg-white px-5 py-4 flex items-center sticky top-0 z-10 shadow-sm">
        <a href="{{ route('mobile.dashboard') }}" class="text-gray-600 p-1 -ml-1">
            <x-heroicon-o-chevron-left class="w-6 h-6" />
        </a>
        <h1 class="text-lg font-medium text-gray-800 ml-4">{{ __('mobile.usage_history') }}</h1>
    </div>

    <div class="px-5 mt-6 space-y-4">
        @forelse($records as $record)
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-start space-x-4">
                <div class="bg-blue-50 rounded-xl p-3 flex flex-col items-center justify-center shrink-0 w-16">
                    <span class="text-xs text-blue-400 mb-1">{{ \Carbon\Carbon::parse($record->treatment_date)->format('Y.m') }}</span>
                    <span class="text-lg font-bold text-blue-600">{{ \Carbon\Carbon::parse($record->treatment_date)->format('d') }}</span>
                </div>
                <div class="flex-1 min-w-0 py-1">
                    <h3 class="text-sm font-bold text-gray-800 truncate">{{ $record->package_name ?? '通用服务' }}</h3>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $record->treatment_content ?: '常规康复训练' }}</p>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-gray-400">{{ __('mobile.remaining_sessions_count') }}: {{ $record->remaining_sessions }}</span>
                        <span class="font-medium text-red-500">-{{ $record->deducted_sessions }} {{ __('mobile.deducted_sessions') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <x-heroicon-o-document-text class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                <p class="text-gray-500 text-sm">{{ __('mobile.no_usage_history') }}</p>
            </div>
        @endforelse
    </div>
</body>
</html>
