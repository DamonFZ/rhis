<div class="flex flex-col items-center justify-center p-6 space-y-4">
    <div class="text-center text-sm text-gray-600 dark:text-gray-400">
        请让客户 <strong class="text-lg text-gray-900 dark:text-white">{{ $patientName }}</strong> 用微信扫描下方二维码<br>
        完成专属档案绑定。
    </div>
    <div class="p-4 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center">
        {!! $qrCode !!}
    </div>
</div>
