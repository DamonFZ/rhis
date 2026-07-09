<x-filament-panels::page>
    <div x-data="spineMapping()" class="grid grid-cols-1 lg:grid-cols-12 gap-8 min-h-[75vh] w-full">

        {{-- 左侧：脊柱交互区 (4 列) --}}
        <div class="col-span-1 lg:col-span-4 bg-white shadow-sm ring-1 ring-gray-950/5 rounded-xl p-6 flex flex-col items-center justify-center">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 self-start">脊柱节段</h3>
            <svg class="w-full h-[60vh] max-h-full border-2 border-dashed border-gray-300 rounded-lg" viewBox="0 0 100 500" xmlns="http://www.w3.org/2000/svg">

                {{-- 颈椎段 C1-C7 --}}
                <g
                    @mouseenter="activeSegment = 'C1-C7'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'C1-C7' ? 'fill-blue-500' : 'fill-gray-300'"
                    class="cursor-pointer transition-all duration-200"
                >
                    <rect x="30" y="10" width="40" height="60" rx="6" />
                    <text x="50" y="44" text-anchor="middle" class="text-[8px] fill-white font-medium" style="pointer-events:none;">C1-C7</text>
                </g>

                {{-- 上胸段 T1-T4 --}}
                <g
                    @mouseenter="activeSegment = 'T1-T4'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'T1-T4' ? 'fill-blue-500' : 'fill-gray-300'"
                    class="cursor-pointer transition-all duration-200"
                >
                    <rect x="30" y="80" width="40" height="50" rx="6" />
                    <text x="50" y="109" text-anchor="middle" class="text-[8px] fill-white font-medium" style="pointer-events:none;">T1-T4</text>
                </g>

                {{-- 中胸段 T5-T9 --}}
                <g
                    @mouseenter="activeSegment = 'T5-T9'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'T5-T9' ? 'fill-blue-500' : 'fill-gray-300'"
                    class="cursor-pointer transition-all duration-200"
                >
                    <rect x="30" y="140" width="40" height="65" rx="6" />
                    <text x="50" y="177" text-anchor="middle" class="text-[8px] fill-white font-medium" style="pointer-events:none;">T5-T9</text>
                </g>

                {{-- 下胸段 T10-T12 --}}
                <g
                    @mouseenter="activeSegment = 'T10-T12'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'T10-T12' ? 'fill-blue-500' : 'fill-gray-300'"
                    class="cursor-pointer transition-all duration-200"
                >
                    <rect x="30" y="215" width="40" height="45" rx="6" />
                    <text x="50" y="242" text-anchor="middle" class="text-[8px] fill-white font-medium" style="pointer-events:none;">T10-T12</text>
                </g>

                {{-- 腰段 L1-L5 --}}
                <g
                    @mouseenter="activeSegment = 'L1-L5'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'L1-L5' ? 'fill-blue-500' : 'fill-gray-300'"
                    class="cursor-pointer transition-all duration-200"
                >
                    <rect x="30" y="270" width="40" height="60" rx="6" />
                    <text x="50" y="304" text-anchor="middle" class="text-[8px] fill-white font-medium" style="pointer-events:none;">L1-L5</text>
                </g>

                {{-- 骶骨段 S2-S4 --}}
                <g
                    @mouseenter="activeSegment = 'S2-S4'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'S2-S4' ? 'fill-blue-500' : 'fill-gray-300'"
                    class="cursor-pointer transition-all duration-200"
                >
                    <rect x="30" y="340" width="40" height="50" rx="6" />
                    <text x="50" y="369" text-anchor="middle" class="text-[8px] fill-white font-medium" style="pointer-events:none;">S2-S4</text>
                </g>

            </svg>
        </div>

        {{-- 右侧：信息面板与内脏区 (8 列) --}}
        <div class="col-span-1 lg:col-span-8 flex flex-col gap-6">

            {{-- 信息卡片 --}}
            <template x-if="activeSegment">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-xl">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2" x-text="segments[activeSegment].title"></h3>
                    <p class="text-gray-600 text-sm leading-relaxed" x-text="segments[activeSegment].description"></p>
                </div>
            </template>
            <template x-if="!activeSegment">
                <div class="bg-gray-50 border-l-4 border-gray-300 p-6 rounded-r-xl flex items-center justify-center min-h-[100px]">
                    <p class="text-gray-400 text-sm">请在左侧触控或悬停脊柱节段查看神经反射弧映射</p>
                </div>
            </template>

            {{-- 内脏占位区 --}}
            <div class="flex-1 bg-white shadow-sm ring-1 ring-gray-950/5 rounded-xl p-6 flex items-center justify-center">
                <svg class="w-full h-[50vh] border-2 border-dashed border-gray-300 rounded-lg" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">

                    {{-- 心脏 --}}
                    <g id="organ-heart">
                        <path
                            d="M250 100 C220 50, 170 50, 170 100 C170 160, 250 210, 250 210 C250 210, 330 160, 330 100 C330 50, 280 50, 250 100Z"
                            :class="activeSegment && segments[activeSegment].organs.includes('heart') ? 'opacity-100 fill-red-500' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="250" y="140" text-anchor="middle" class="text-[12px] fill-gray-600" style="pointer-events:none;">心脏</text>
                    </g>

                    {{-- 肺 --}}
                    <g id="organ-lungs">
                        <path
                            d="M150 80 C120 80, 110 120, 120 170 C130 210, 170 220, 200 210 C210 200, 210 140, 200 110 C190 80, 170 80, 150 80Z"
                            :class="activeSegment && segments[activeSegment].organs.includes('lungs') ? 'opacity-100 fill-pink-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <path
                            d="M350 80 C380 80, 390 120, 380 170 C370 210, 330 220, 300 210 C290 200, 290 140, 300 110 C310 80, 330 80, 350 80Z"
                            :class="activeSegment && segments[activeSegment].organs.includes('lungs') ? 'opacity-100 fill-pink-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="250" y="90" text-anchor="middle" class="text-[12px] fill-gray-600" style="pointer-events:none;">肺</text>
                    </g>

                    {{-- 膈肌 --}}
                    <g id="organ-diaphragm">
                        <path
                            d="M120 210 Q250 180 380 210"
                            fill="none"
                            :class="activeSegment && segments[activeSegment].organs.includes('diaphragm') ? 'opacity-100 stroke-teal-500' : 'opacity-30 stroke-gray-200'"
                            stroke-width="8"
                            stroke-linecap="round"
                            class="transition-all duration-300"
                        />
                        <text x="250" y="200" text-anchor="middle" class="text-[12px] fill-gray-600" style="pointer-events:none;">膈肌</text>
                    </g>

                    {{-- 胃 --}}
                    <g id="organ-stomach">
                        <path
                            d="M220 240 C190 240, 180 270, 190 310 C200 350, 240 360, 270 340 C300 320, 290 260, 260 240 C245 230, 230 240, 220 240Z"
                            :class="activeSegment && segments[activeSegment].organs.includes('stomach') ? 'opacity-100 fill-orange-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="235" y="300" text-anchor="middle" class="text-[12px] fill-gray-600" style="pointer-events:none;">胃</text>
                    </g>

                    {{-- 肝 --}}
                    <g id="organ-liver">
                        <path
                            d="M270 230 C320 220, 360 240, 350 290 C340 330, 300 340, 270 330 C240 320, 240 260, 270 230Z"
                            :class="activeSegment && segments[activeSegment].organs.includes('liver') ? 'opacity-100 fill-amber-600' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="305" y="285" text-anchor="middle" class="text-[12px] fill-gray-600" style="pointer-events:none;">肝</text>
                    </g>

                    {{-- 胆 --}}
                    <g id="organ-gallbladder">
                        <ellipse
                            cx="295" cy="330" rx="18" ry="25"
                            :class="activeSegment && segments[activeSegment].organs.includes('gallbladder') ? 'opacity-100 fill-green-500' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="295" y="335" text-anchor="middle" class="text-[10px] fill-gray-600" style="pointer-events:none;">胆</text>
                    </g>

                    {{-- 脾 --}}
                    <g id="organ-spleen">
                        <ellipse
                            cx="190" cy="310" rx="25" ry="35"
                            :class="activeSegment && segments[activeSegment].organs.includes('spleen') ? 'opacity-100 fill-purple-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="190" y="315" text-anchor="middle" class="text-[10px] fill-gray-600" style="pointer-events:none;">脾</text>
                    </g>

                    {{-- 结肠 --}}
                    <g id="organ-colon">
                        <path
                            d="M170 370 L170 410 C170 430, 195 440, 220 440 L280 440 C305 440, 330 430, 330 410 L330 370"
                            fill="none"
                            :class="activeSegment && segments[activeSegment].organs.includes('colon') ? 'opacity-100 stroke-yellow-600' : 'opacity-30 stroke-gray-200'"
                            stroke-width="14"
                            stroke-linecap="round"
                            class="transition-all duration-300"
                        />
                        <text x="250" y="410" text-anchor="middle" class="text-[12px] fill-gray-600" style="pointer-events:none;">结肠</text>
                    </g>

                    {{-- 直肠 --}}
                    <g id="organ-rectum">
                        <rect
                            x="238" y="450" width="24" height="40" rx="5"
                            :class="activeSegment && segments[activeSegment].organs.includes('rectum') ? 'opacity-100 fill-yellow-700' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="250" y="475" text-anchor="middle" class="text-[10px] fill-gray-600" style="pointer-events:none;">直肠</text>
                    </g>

                    {{-- 膀胱 --}}
                    <g id="organ-bladder">
                        <ellipse
                            cx="250" cy="490" rx="30" ry="18"
                            :class="activeSegment && segments[activeSegment].organs.includes('bladder') ? 'opacity-100 fill-cyan-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="250" y="495" text-anchor="middle" class="text-[10px] fill-gray-600" style="pointer-events:none;">膀胱</text>
                    </g>

                    {{-- 生殖器官 --}}
                    <g id="organ-reproductive">
                        <ellipse
                            cx="250" cy="520" rx="22" ry="12"
                            :class="activeSegment && segments[activeSegment].organs.includes('reproductive') ? 'opacity-100 fill-rose-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="250" y="525" text-anchor="middle" class="text-[10px] fill-gray-600" style="pointer-events:none;">生殖</text>
                    </g>

                </svg>
            </div>
        </div>
    </div>

    <script>
        function spineMapping() {
            return {
                activeSegment: null,
                segments: {
                    'C1-C7': {
                        title: '颈椎段 (C1-C7) - 副交感神经总闸门',
                        description: '解除迷走神经卡压，激活副交感神经，降低心率，促进胃肠蠕动。C3-C5 支配膈肌，恢复腹式呼吸。',
                        organs: ['heart', 'lungs', 'stomach', 'liver', 'diaphragm'],
                    },
                    'T1-T4': {
                        title: '上胸段 (T1-T4) - 交感神经',
                        description: '支配心脏、肺、气管。该区域紊乱会导致心率加快、支气管异常扩张。',
                        organs: ['heart', 'lungs'],
                    },
                    'T5-T9': {
                        title: '中胸段 (T5-T9) - 交感神经大本营',
                        description: '支配胃、肝、胆、脾。该节段高频易化会抑制胃酸分泌、减弱胃肠蠕动。',
                        organs: ['stomach', 'liver', 'gallbladder', 'spleen'],
                    },
                    'T10-T12': {
                        title: '下胸段 (T10-T12) - 交感神经延伸',
                        description: '支配小肠、肾脏、肾上腺。该节段紊乱影响消化吸收与应激反应。',
                        organs: ['stomach', 'liver'],
                    },
                    'L1-L5': {
                        title: '腰段 (L1-L5) - 下肢与盆腔神经',
                        description: '支配下肢运动感觉、部分盆腔器官。腰椎问题常导致坐骨神经痛。',
                        organs: ['colon', 'reproductive'],
                    },
                    'S2-S4': {
                        title: '骶骨段 (S2-S4) - 盆腔副交感底座',
                        description: '支配降结肠、直肠、膀胱等。调整骶髂关节可恢复神经传导，处理肠易激综合征(IBS)或盆腔疼痛。',
                        organs: ['colon', 'rectum', 'bladder', 'reproductive'],
                    },
                },
            };
        }
    </script>
</x-filament-panels::page>
