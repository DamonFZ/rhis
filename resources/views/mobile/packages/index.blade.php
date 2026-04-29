<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('mobile.my_packages') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] min-h-screen font-sans">
    
    <div class="bg-[#1E4D7B] pt-10 pb-6 px-6 shadow-md">
        <div class="flex items-center">
            <a href="{{ route('mobile.dashboard') }}" class="text-white mr-4">
                <x-heroicon-o-chevron-left class="w-6 h-6" />
            </a>
            <h1 class="text-xl font-medium text-white">{{ __('mobile.my_packages') }}</h1>
        </div>
    </div>

    <div class="px-5 mt-6">
        @if($packages->isEmpty())
            <div class="bg-white rounded-2xl p-8 text-center">
                <x-heroicon-o-inbox class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                <p class="text-gray-500">{{ __('mobile.no_packages') }}</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($packages as $package)
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-medium text-gray-900">{{ $package->package_name }}</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $package->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $package->status === 'active' ? __('mobile.package_active') : __('mobile.package_completed') }}
                            </span>
                        </div>
                        
                        <div class="text-sm text-gray-500 mb-2">
                            {{ __('mobile.package_code') }}: {{ $package->package_code ?: __('mobile.no_code') }}
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-100">
                            <div class="flex items-center">
                                <x-heroicon-o-clock class="w-5 h-5 text-[#1E4D7B] mr-2" />
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $package->remaining_sessions }}
                                </span>
                            </div>
                            @if($package->expiry_date)
                                <div class="flex items-center">
                                    <x-heroicon-o-calendar-days class="w-5 h-5 text-[#1E4D7B] mr-2" />
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($package->expiry_date)->format('Y-m-d') }}
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <x-heroicon-o-calendar-days class="w-5 h-5 text-[#1E4D7B] mr-2" />
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ __('mobile.valid_forever') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
