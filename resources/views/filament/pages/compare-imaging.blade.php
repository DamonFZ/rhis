<x-filament-panels::page>
    <div class="space-y-6">
        @if ($hasError)
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-xl font-bold text-red-800">出错了</h2>
                </div>
                <p class="text-red-700 mt-2">{{ $errorMessage }}</p>
            </div>
        @else
            <div class="flex items-center justify-between mb-4 print:hidden">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">康复影像对比</h1>
                    <p class="text-gray-600 mt-1">共 {{ $records->count() }} 条记录</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002 2v-4a2 2 0 00-2-2H9a2 0 00-2-2V6a2 2 0 001-2h6a2 2 0 002 2z"></path>
                        </svg>
                        打印报告
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto w-full bg-white rounded-lg shadow border border-gray-200">
                <div class="min-w-full divide-y divide-gray-200">
                    {{-- 日期行 --}}
                    <div class="flex">
                        <div class="w-36 shrink-0 p-4 border-r border-gray-200 bg-gray-50 font-medium text-gray-800 flex items-center">日期</div>
                        @foreach ($records as $record)
                            <div class="p-4 border-r border-gray-200 last:border-r-0" style="min-width: 280px;">
                                <div class="font-bold text-gray-800">{{ $record->treatment_date->format('Y-m-d') }}</div>
                                <div class="text-sm text-gray-600">{{ $this->getRecordTypeLabel($record) }}</div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- 照片行 --}}
                    @foreach ($photoLabels as $photoKey => $photoLabel)
                        <div class="flex">
                            <div class="w-36 shrink-0 p-4 border-r border-gray-200 bg-gray-50 font-medium text-gray-800 flex items-center">
                                {{ $photoLabel }}
                            </div>
                            @foreach ($records as $record)
                                @php
                                    $photoUrl = $this->getPhotoUrl($record, $photoKey);
                                @endphp
                                <div class="p-2 border-r border-gray-200 last:border-r-0" style="min-width: 280px;">
                                    @if ($photoUrl)
                                        <div class="w-full" style="aspect-ratio: 3/4;">
                                            <div class="block relative w-full h-full bg-gray-100 rounded-lg border border-gray-200 overflow-hidden hover:ring-2 hover:ring-primary-500 transition-all">
                                                <img src="{{ $photoUrl }}" 
                                                     class="absolute inset-0 w-full h-full object-contain p-2 cursor-pointer transition-transform hover:scale-105" 
                                                     alt="康复影像" 
                                                     x-on:click="$dispatch('open-image-modal', '{{ $photoUrl }}')">
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-full flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200 text-gray-400" style="aspect-ratio: 3/4;">
                                            <span>暂无照片</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    
                    {{-- 备注行 --}}
                    <div class="flex">
                        <div class="w-36 shrink-0 p-4 border-r border-gray-200 bg-gray-50 font-medium text-gray-800 flex items-center">
                            备注
                        </div>
                        @foreach ($records as $record)
                            <div class="p-4 border-r border-gray-200 last:border-r-0" style="min-width: 280px;">
                                <div class="text-gray-700 whitespace-pre-wrap">{{ $record->remark ?? '暂无备注' }}</div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- 视频行 --}}
                    <div class="flex">
                        <div class="w-36 shrink-0 p-4 border-r border-gray-200 bg-gray-50 font-medium text-gray-800 flex items-center">
                            动态视频
                        </div>
                        @foreach ($records as $record)
                            @php
                                $videoUrl = $this->getVideoUrl($record);
                            @endphp
                            <div class="p-4 border-r border-gray-200 last:border-r-0" style="min-width: 280px;">
                                @if ($videoUrl)
                                    <video 
                                        src="{{ $videoUrl }}" 
                                        controls 
                                        class="w-full rounded-lg border border-gray-200 print:max-w-full"
                                    ></video>
                                @else
                                    <div class="text-gray-400">暂无视频</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        
        <div class="flex gap-4 print:hidden">
            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                返回
            </a>
        </div>
    </div>
    
    {{-- 图片模态框 - 使用 Alpine.js 和 x-teleport --}}
    <div x-data="{ isModalOpen: false, modalImageUrl: '' }" 
         @open-image-modal.window="modalImageUrl = $event.detail; isModalOpen = true" 
         @keydown.escape.window="isModalOpen = false">

        <template x-teleport="body"> 
            <div x-show="isModalOpen" 
                 class="fixed inset-0 z-[999999] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 md:p-8" 
                 style="display: none;" 
                 x-transition.opacity.duration.300ms 
                 @click="isModalOpen = false" 
                 x-cloak>
                
                <button class="absolute top-6 right-6 text-white/70 hover:text-white hover:scale-110 transition-all z-[1000000]">
                    <svg class="w-12 h-12 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <img :src="modalImageUrl" 
                     class="block object-contain rounded-lg shadow-2xl ring-1 ring-white/10" 
                     style="max-width: 95vw; max-height: 95vh; width: auto; height: auto;" 
                     alt="全屏预览" 
                     @click.stop>
            </div>
        </template>
    </div>
</x-filament-panels::page>
