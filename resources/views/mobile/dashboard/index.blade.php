<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>康复大本营</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] min-h-screen font-sans">
    
    <div class="bg-[#1E4D7B] rounded-b-3xl pt-10 pb-20 px-6 shadow-md">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-gray-200 rounded-full border-2 border-white flex items-center justify-center overflow-hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="text-white">
                <h1 class="text-xl font-medium tracking-wide">{{ $patient->name }}</h1>
                <p class="text-[#8FB3D9] text-sm mt-1 tracking-wider">ID{{ str_pad($patient->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>
    </div>

    <div class="px-5 -mt-12 grid grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center aspect-[4/5]">
            <div class="mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#1E4D7B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5z" />
                    <path d="M2 17L12 22l10-5" />
                    <path d="M2 12L12 17l10-5" />
                </svg>
            </div>
            <div class="text-sm font-medium text-gray-800 leading-snug">剩余总课时<br>12</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col items-center justify-center text-center aspect-[4/5]">
            <div class="mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-[#1E4D7B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div class="text-sm font-medium text-gray-800 leading-snug">上次评估<br>2023-10-01</div>
        </div>
    </div>

    <div class="px-8 mt-10 grid grid-cols-2 gap-y-10 gap-x-6">
        <div class="flex flex-col items-center cursor-pointer">
            <div class="w-14 h-14 mb-2 flex items-center justify-center bg-[#E8F1F9] rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#1E4D7B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-800">我的套餐</span>
        </div>
        <div class="flex flex-col items-center cursor-pointer">
            <div class="w-14 h-14 mb-2 flex items-center justify-center bg-[#F0F8F4] rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#1E4D7B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-800">使用记录</span>
        </div>
        <div class="flex flex-col items-center cursor-pointer">
            <div class="w-14 h-14 mb-2 flex items-center justify-center bg-[#FFF5E8] rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#1E4D7B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-800">康复报告</span>
        </div>
        <div class="flex flex-col items-center cursor-pointer">
            <div class="w-14 h-14 mb-2 flex items-center justify-center bg-[#F3E8FA] rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[#1E4D7B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-800">我的预约</span>
        </div>
    </div>
</body>
</html>
