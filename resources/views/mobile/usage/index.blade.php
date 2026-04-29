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

    <div class="px-4 mt-5 space-y-4">
        @forelse($records as $record)
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-start space-x-4 relative">
                
                <div class="bg-blue-50/60 border border-blue-100 rounded-xl w-14 h-14 flex flex-col items-center justify-center shrink-0">
                    <span class="text-[10px] text-blue-500 font-medium tracking-wider">{{ \Carbon\Carbon::parse($record->treatment_date)->format('Y.m') }}</span>
                    <span class="text-xl font-bold text-blue-700 leading-none mt-0.5">{{ \Carbon\Carbon::parse($record->treatment_date)->format('d') }}</span>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        <div class="pr-2 truncate">
                            <h3 class="text-base font-bold text-gray-800 truncate">
                                {{ $record->package_name ?: __('mobile.general_service') }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1 truncate">
                                {{ $record->treatment_content ?: __('mobile.routine_training') }}
                            </p>
                        </div>
                        
                        <div class="shrink-0 mt-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-50 text-red-500 border border-red-100">
                                -{{ $record->deducted_sessions }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-dashed border-gray-100 flex items-center justify-between text-xs">
                        <span class="text-gray-400 flex items-center">
                            <x-heroicon-o-archive-box class="w-3.5 h-3.5 mr-1" />
                            {{ __('mobile.remaining_sessions_count') }}
                        </span>
                        <span class="font-medium text-gray-700">{{ $record->remaining_sessions }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-document-text class="w-10 h-10 text-gray-300" />
                </div>
                <p class="text-gray-500 text-sm">{{ __('mobile.no_usage_history') }}</p>
            </div>
        @endforelse
    </div>
</body>
</html>
