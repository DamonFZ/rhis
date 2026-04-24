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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2V6a2 2 0 012-2h6a2 2 0 012 2zm11-15v4a2 2 0 01-2 2H9a2 2 0 01-2-2V6a2 2 0 012-2h6a2 2 0 012 2z"></path>
                        </svg>
                        打印报告
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto w-full bg-white rounded-lg shadow border border-gray-200">
                <div class="min-w-full divide-y divide-gray-200">
                    {{-- 日期行 --}}
                    <div class="flex">
                        <div class="w-36 shrink-0 p-4 border-r border-gray-200 bg-gray-50"></div>
                        @foreach ($records as $record)
                            <div class="flex-1 p-4 border-r border-gray-200 last:border-r-0">
                                <div class="font-bold text-gray-800">{{ $record->treatment_date->format('Y-m-d') }}</div>
                                <div class="text-sm text-gray-600">{{ $this->getRecordTypeLabel($record) }}</div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- 照片行 --}}
                    @foreach ($photoLabels as $photoKey => $photoLabel)
                        <div class="flex">
                            <div class="w-36 shrink-0 p-4 border-r border-gray-200 bg-gray-50 font-medium text-gray-800">
                                {{ $photoLabel }}
                            </div>
                            @foreach ($records as $record)
                                @php
                                    $photoUrl = $this->getPhotoUrl($record, $photoKey);
                                @endphp
                                <div class="p-2 min-w-[240px] md:min-w-[280px] align-top border-r border-gray-200 last:border-r-0">
                                    @if ($photoUrl)
                                        <a href="{{ $photoUrl }}" 
                                           target="_blank" 
                                           class="block relative w-full aspect-[3/4] bg-gray-100 rounded-lg border border-gray-200 overflow-hidden hover:ring-2 hover:ring-primary-500 transition-all">
                                            <img src="{{ $photoUrl }}" 
                                                 class="absolute inset-0 w-full h-full object-contain p-2" 
                                                 alt="康复影像">
                                        </a>
                                    @else
                                        <div class="w-full aspect-[3/4] flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200 text-gray-400">
                                            <span>暂无照片</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    
                    {{-- 备注行 --}}
                    <div class="flex">
                        <div class="w-36 shrink-0 p-4 border-r border-gray-200 bg-gray-50 font-medium text-gray-800">
                            备注
                        </div>
                        @foreach ($records as $record)
                            <div class="flex-1 p-4 border-r border-gray-200 last:border-r-0">
                                <div class="text-gray-700 whitespace-pre-wrap">{{ $record->remark ?? '暂无备注' }}</div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- 视频行 --}}
                    <div class="flex">
                        <div class="w-36 shrink-0 p-4 border-r border-gray-200 bg-gray-50 font-medium text-gray-800">
                            动态视频
                        </div>
                        @foreach ($records as $record)
                            @php
                                $videoUrl = $this->getVideoUrl($record);
                            @endphp
                            <div class="flex-1 p-4 border-r border-gray-200 last:border-r-0">
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
</x-filament-panels::page>
