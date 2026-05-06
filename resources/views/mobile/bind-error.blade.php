<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>绑定提示</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
    <div class="bg-white p-8 rounded-2xl shadow-sm text-center w-full max-w-md border border-gray-100 rounded-2xl shadow-sm">
        <div class="text-yellow-500 mb-4">
            <x-heroicon-o-exclamation-circle class="w-16 h-16 mx-auto" />
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">无法绑定</h2>
        <p class="text-sm text-gray-500 mb-6">{{ $message }}</p>
        <p class="text-xs text-gray-400">如需更换绑定，请联系管理员</p>
    </div>
</body>
</html>
