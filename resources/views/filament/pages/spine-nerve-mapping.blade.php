<x-filament-panels::page>
    <div x-data="spineMapping()" class="grid grid-cols-12 gap-6 h-[calc(100vh-12rem)]">

        {{-- 左侧：脊柱交互区 (4 列) --}}
        <div class="col-span-4 bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">脊柱节段</h3>
            <svg viewBox="0 0 200 600" class="flex-1 w-full" xmlns="http://www.w3.org/2000/svg">

                {{-- 颈椎段 C1-C7 --}}
                <g
                    @mouseenter="activeSegment = 'C1-C7'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'C1-C7' ? 'fill-blue-500 scale-105 cursor-pointer' : 'fill-gray-300 cursor-pointer'"
                    class="transition-all duration-200"
                >
                    <rect x="70" y="20" width="60" height="100" rx="10" />
                    <text x="100" y="75" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">C1-C7</text>
                </g>

                {{-- 上胸段 T1-T4 --}}
                <g
                    @mouseenter="activeSegment = 'T1-T4'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'T1-T4' ? 'fill-blue-500 scale-105 cursor-pointer' : 'fill-gray-300 cursor-pointer'"
                    class="transition-all duration-200"
                >
                    <rect x="70" y="130" width="60" height="80" rx="10" />
                    <text x="100" y="175" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">T1-T4</text>
                </g>

                {{-- 中胸段 T5-T9 --}}
                <g
                    @mouseenter="activeSegment = 'T5-T9'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'T5-T9' ? 'fill-blue-500 scale-105 cursor-pointer' : 'fill-gray-300 cursor-pointer'"
                    class="transition-all duration-200"
                >
                    <rect x="70" y="220" width="60" height="100" rx="10" />
                    <text x="100" y="275" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">T5-T9</text>
                </g>

                {{-- 下胸段 T10-T12 --}}
                <g
                    @mouseenter="activeSegment = 'T10-T12'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'T10-T12' ? 'fill-blue-500 scale-105 cursor-pointer' : 'fill-gray-300 cursor-pointer'"
                    class="transition-all duration-200"
                >
                    <rect x="70" y="330" width="60" height="70" rx="10" />
                    <text x="100" y="370" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">T10-T12</text>
                </g>

                {{-- 腰段 L1-L5 --}}
                <g
                    @mouseenter="activeSegment = 'L1-L5'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'L1-L5' ? 'fill-blue-500 scale-105 cursor-pointer' : 'fill-gray-300 cursor-pointer'"
                    class="transition-all duration-200"
                >
                    <rect x="70" y="410" width="60" height="90" rx="10" />
                    <text x="100" y="460" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">L1-L5</text>
                </g>

                {{-- 骶骨段 S2-S4 --}}
                <g
                    @mouseenter="activeSegment = 'S2-S4'"
                    @mouseleave="activeSegment = null"
                    :class="activeSegment === 'S2-S4' ? 'fill-blue-500 scale-105 cursor-pointer' : 'fill-gray-300 cursor-pointer'"
                    class="transition-all duration-200"
                >
                    <rect x="70" y="510" width="60" height="70" rx="10" />
                    <text x="100" y="550" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">S2-S4</text>
                </g>

            </svg>
        </div>

        {{-- 右侧：内脏映射与数据面板区 (8 列) --}}
        <div class="col-span-8 flex flex-col gap-6">

            {{-- 数据面板 --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 min-h-[120px]">
                <template x-if="activeSegment">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2" x-text="segments[activeSegment].title"></h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed" x-text="segments[activeSegment].description"></p>
                    </div>
                </template>
                <template x-if="!activeSegment">
                    <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                        <p class="text-sm">请在左侧触控或悬停脊柱节段查看神经反射弧映射</p>
                    </div>
                </template>
            </div>

            {{-- 内脏 SVG 骨架区 --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex-1 flex items-center justify-center">
                <svg viewBox="0 0 400 500" class="w-full h-full max-h-[500px]" xmlns="http://www.w3.org/2000/svg">

                    {{-- 心脏 --}}
                    <g id="organ-heart">
                        <path
                            d="M200 120 C180 80, 140 80, 140 120 C140 160, 200 200, 200 200 C200 200, 260 160, 260 120 C260 80, 220 80, 200 120Z"
                            :class="activeSegment && segments[activeSegment].organs.includes('heart') ? 'opacity-100 fill-red-500' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="200" y="160" text-anchor="middle" class="text-xs fill-gray-600" style="pointer-events:none;">心脏</text>
                    </g>

                    {{-- 肺 --}}
                    <g id="organ-lungs">
                        <path
                            d="M120 100 C100 100, 90 130, 100 170 C110 200, 140 210, 160 200 C170 190, 170 140, 160 120 C150 100, 130 100, 120 100Z"
                            :class="activeSegment && segments[activeSegment].organs.includes('lungs') ? 'opacity-100 fill-pink-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <path
                            d="M280 100 C300 100, 310 130, 300 170 C290 200, 260 210, 240 200 C230 190, 230 140, 240 120 C250 100, 270 100, 280 100Z"
                            :class="activeSegment && segments[activeSegment].organs.includes('lungs') ? 'opacity-100 fill-pink-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="200" y="110" text-anchor="middle" class="text-xs fill-gray-600" style="pointer-events:none;">肺</text>
                    </g>

                    {{-- 胃 --}}
                    <g id="organ-stomach">
                        <path
                            d="M180 220 C160 220, 150 240, 160 270 C170 300, 200 310, 220 290 C240 270, 230 230, 210 220 C200 215, 190 220, 180 220Z"
                            :class="activeSegment && segments[activeSegment].organs.includes('stomach') ? 'opacity-100 fill-orange-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="195" y="265" text-anchor="middle" class="text-xs fill-gray-600" style="pointer-events:none;">胃</text>
                    </g>

                    {{-- 肝 --}}
                    <g id="organ-liver">
                        <path
                            d="M220 210 C260 200, 290 220, 280 260 C270 290, 240 300, 220 290 C200 280, 200 230, 220 210Z"
                            :class="activeSegment && segments[activeSegment].organs.includes('liver') ? 'opacity-100 fill-amber-600' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="250" y="255" text-anchor="middle" class="text-xs fill-gray-600" style="pointer-events:none;">肝</text>
                    </g>

                    {{-- 胆 --}}
                    <g id="organ-gallbladder">
                        <ellipse
                            cx="240" cy="280" rx="15" ry="20"
                            :class="activeSegment && segments[activeSegment].organs.includes('gallbladder') ? 'opacity-100 fill-green-500' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="240" y="285" text-anchor="middle" class="text-xs fill-gray-600" style="pointer-events:none;">胆</text>
                    </g>

                    {{-- 脾 --}}
                    <g id="organ-spleen">
                        <ellipse
                            cx="155" cy="260" rx="20" ry="30"
                            :class="activeSegment && segments[activeSegment].organs.includes('spleen') ? 'opacity-100 fill-purple-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="155" y="265" text-anchor="middle" class="text-xs fill-gray-600" style="pointer-events:none;">脾</text>
                    </g>

                    {{-- 结肠 --}}
                    <g id="organ-colon">
                        <path
                            d="M140 330 L140 370 C140 390, 160 400, 180 400 L220 400 C240 400, 260 390, 260 370 L260 330"
                            fill="none"
                            :class="activeSegment && segments[activeSegment].organs.includes('colon') ? 'opacity-100 stroke-yellow-600' : 'opacity-30 stroke-gray-200'"
                            stroke-width="12"
                            stroke-linecap="round"
                            class="transition-all duration-300"
                        />
                        <text x="200" y="370" text-anchor="middle" class="text-xs fill-gray-600" style="pointer-events:none;">结肠</text>
                    </g>

                    {{-- 直肠 --}}
                    <g id="organ-rectum">
                        <rect
                            x="190" y="410" width="20" height="40" rx="5"
                            :class="activeSegment && segments[activeSegment].organs.includes('rectum') ? 'opacity-100 fill-yellow-700' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="200" y="435" text-anchor="middle" class="text-xs fill-gray-600" style="pointer-events:none;">直肠</text>
                    </g>

                    {{-- 膀胱 --}}
                    <g id="organ-bladder">
                        <ellipse
                            cx="200" cy="460" rx="25" ry="18"
                            :class="activeSegment && segments[activeSegment].organs.includes('bladder') ? 'opacity-100 fill-cyan-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="200" y="465" text-anchor="middle" class="text-xs fill-gray-600" style="pointer-events:none;">膀胱</text>
                    </g>

                    {{-- 生殖器官 --}}
                    <g id="organ-reproductive">
                        <ellipse
                            cx="200" cy="490" rx="18" ry="10"
                            :class="activeSegment && segments[activeSegment].organs.includes('reproductive') ? 'opacity-100 fill-rose-400' : 'opacity-30 fill-gray-200'"
                            class="transition-all duration-300"
                        />
                        <text x="200" y="495" text-anchor="middle" class="text-xs fill-gray-600" style="pointer-events:none;">生殖</text>
                    </g>

                    {{-- 膈肌 --}}
                    <g id="organ-diaphragm">
                        <path
                            d="M100 195 Q200 175 300 195"
                            fill="none"
                            :class="activeSegment && segments[activeSegment].organs.includes('diaphragm') ? 'opacity-100 stroke-teal-500' : 'opacity-30 stroke-gray-200'"
                            stroke-width="6"
                            stroke-linecap="round"
                            class="transition-all duration-300"
                        />
                        <text x="200" y="190" text-anchor="middle" class="text-xs fill-gray-600" style="pointer-events:none;">膈肌</text>
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
