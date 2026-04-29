<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('mobile.unbound_title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
    <div class="absolute top-4 right-5 z-10">
        @if(app()->getLocale() == 'zh_CN')
            <a href="{{ route('mobile.lang.switch', 'zh_HK') }}" class="text-sm font-medium text-gray-400">繁體</a>
        @else
            <a href="{{ route('mobile.lang.switch', 'zh_CN') }}" class="text-sm font-medium text-gray-400">简体</a>
        @endif
    </div>
    <div class="bg-white p-8 rounded-2xl shadow-sm text-center w-full max-w-md border border-gray-100 rounded-2xl shadow-sm">
        <div class="text-gray-400 mb-4">
            <x-heroicon-o-user-minus class="w-16 h-16 mx-auto text-gray-300 mb-4" />
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">{{ __('mobile.unbound_title') }}</h2>
        <p class="text-sm text-gray-500 mb-6">{{ __('mobile.unbound_desc') }}</p>
    </div>
</body>
</html>
