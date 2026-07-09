<x-filament-panels::page>
    <div x-data="spineMapping()" class="flex flex-col h-[calc(100vh-8rem)] overflow-hidden">

        {{-- 头部标题区 --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800">脊柱-内脏神经映射系统</h2>
        </div>

        {{-- 中间核心展示区 (自适应填满剩余空间) --}}
        <div class="flex-1 mt-4 grid grid-cols-12 gap-6 min-h-0">

            {{-- 左侧脊柱区 (4 列) --}}
            <div class="col-span-4 bg-slate-50/50 rounded-xl border border-gray-100 flex items-center justify-center p-4 min-h-0">
                <svg class="w-48 h-auto max-h-full" viewBox="0 0 200 620" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg">
                    <rect width="100%" height="100%" fill="none" stroke="#e5e7eb" stroke-dasharray="4"/>

                    {{-- 颈椎段 C1-C7 --}}
                    <g
                        @mouseenter="activeSegment = 'C1-C7'"
                        @mouseleave="activeSegment = null"
                        :class="activeSegment === 'C1-C7' ? 'fill-blue-500' : 'fill-gray-300'"
                        class="cursor-pointer transition-all duration-200"
                    >
                        <rect x="70" y="20" width="60" height="100" rx="10" />
                        <text x="100" y="75" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">C1-C7</text>
                    </g>

                    {{-- 上胸段 T1-T4 --}}
                    <g
                        @mouseenter="activeSegment = 'T1-T4'"
                        @mouseleave="activeSegment = null"
                        :class="activeSegment === 'T1-T4' ? 'fill-blue-500' : 'fill-gray-300'"
                        class="cursor-pointer transition-all duration-200"
                    >
                        <rect x="70" y="130" width="60" height="80" rx="10" />
                        <text x="100" y="175" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">T1-T4</text>
                    </g>

                    {{-- 中胸段 T5-T9 --}}
                    <g
                        @mouseenter="activeSegment = 'T5-T9'"
                        @mouseleave="activeSegment = null"
                        :class="activeSegment === 'T5-T9' ? 'fill-blue-500' : 'fill-gray-300'"
                        class="cursor-pointer transition-all duration-200"
                    >
                        <rect x="70" y="220" width="60" height="100" rx="10" />
                        <text x="100" y="275" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">T5-T9</text>
                    </g>

                    {{-- 下胸段 T10-T12 --}}
                    <g
                        @mouseenter="activeSegment = 'T10-T12'"
                        @mouseleave="activeSegment = null"
                        :class="activeSegment === 'T10-T12' ? 'fill-blue-500' : 'fill-gray-300'"
                        class="cursor-pointer transition-all duration-200"
                    >
                        <rect x="70" y="330" width="60" height="70" rx="10" />
                        <text x="100" y="370" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">T10-T12</text>
                    </g>

                    {{-- 腰段 L1-L5 --}}
                    <g
                        @mouseenter="activeSegment = 'L1-L5'"
                        @mouseleave="activeSegment = null"
                        :class="activeSegment === 'L1-L5' ? 'fill-blue-500' : 'fill-gray-300'"
                        class="cursor-pointer transition-all duration-200"
                    >
                        <rect x="70" y="410" width="60" height="90" rx="10" />
                        <text x="100" y="460" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">L1-L5</text>
                    </g>

                    {{-- 骶骨段 S2-S4 --}}
                    <g
                        @mouseenter="activeSegment = 'S2-S4'"
                        @mouseleave="activeSegment = null"
                        :class="activeSegment === 'S2-S4' ? 'fill-blue-500' : 'fill-gray-300'"
                        class="cursor-pointer transition-all duration-200"
                    >
                        <rect x="70" y="510" width="60" height="70" rx="10" />
                        <text x="100" y="550" text-anchor="middle" class="text-xs fill-white font-medium" style="pointer-events:none;">S2-S4</text>
                    </g>

                </svg>
            </div>

            {{-- 右侧数据面板 (8 列，内容过多时内部滚动) --}}
            <div class="col-span-8 bg-white rounded-xl border border-gray-200 p-6 overflow-y-auto">
                <template x-if="activeSegment">
                    <div class="space-y-6">
                        <h3 class="text-xl font-bold text-blue-600 border-b pb-2" x-text="segments[activeSegment].title"></h3>

                        <div>
                            <div class="flex items-center gap-2 text-gray-700 font-bold mb-2">🎯 支配区域 (Organ Mapping)</div>
                            <div class="border-l-4 border-green-500 pl-4 text-gray-600" x-text="segments[activeSegment].organs_text"></div>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 text-gray-700 font-bold mb-2">🧠 神经属性 (Nerve Type)</div>
                            <div class="border-l-4 border-green-500 pl-4 text-gray-600" x-text="segments[activeSegment].nerve_type"></div>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 text-gray-700 font-bold mb-2"> 临床意义</div>
                            <div class="border-l-4 border-blue-500 pl-4 text-gray-600" x-text="segments[activeSegment].description"></div>
                        </div>
                    </div>
                </template>

                <template x-if="!activeSegment">
                    <div class="h-full flex items-center justify-center text-gray-400">请在左侧选择脊柱节段</div>
                </template>
            </div>
        </div>

        {{-- 底部控制台区 --}}
        <div class="mt-6 h-24 bg-gray-50 rounded-xl flex items-center justify-between p-4 shrink-0">
            <div class="text-sm text-gray-500">
                <span class="font-medium">当前选中：</span>
                <span x-text="activeSegment || '无'"></span>
            </div>
            <div class="text-sm text-gray-500">
                <span class="font-medium">关联器官数：</span>
                <span x-text="activeSegment ? segments[activeSegment].organs.length : 0"></span>
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
                        organs_text: '心脏、肺、胃、肝、膈肌',
                        nerve_type: '副交感神经（迷走神经）',
                    },
                    'T1-T4': {
                        title: '上胸段 (T1-T4) - 交感神经',
                        description: '支配心脏、肺、气管。该区域紊乱会导致心率加快、支气管异常扩张。',
                        organs: ['heart', 'lungs'],
                        organs_text: '心脏、肺、气管',
                        nerve_type: '交感神经',
                    },
                    'T5-T9': {
                        title: '中胸段 (T5-T9) - 交感神经大本营',
                        description: '支配胃、肝、胆、脾。该节段高频易化会抑制胃酸分泌、减弱胃肠蠕动。',
                        organs: ['stomach', 'liver', 'gallbladder', 'spleen'],
                        organs_text: '胃、肝、胆、脾',
                        nerve_type: '交感神经',
                    },
                    'T10-T12': {
                        title: '下胸段 (T10-T12) - 交感神经延伸',
                        description: '支配小肠、肾脏、肾上腺。该节段紊乱影响消化吸收与应激反应。',
                        organs: ['stomach', 'liver'],
                        organs_text: '小肠、肾脏、肾上腺',
                        nerve_type: '交感神经',
                    },
                    'L1-L5': {
                        title: '腰段 (L1-L5) - 下肢与盆腔神经',
                        description: '支配下肢运动感觉、部分盆腔器官。腰椎问题常导致坐骨神经痛。',
                        organs: ['colon', 'reproductive'],
                        organs_text: '下肢、盆腔器官',
                        nerve_type: '混合神经（运动 + 感觉）',
                    },
                    'S2-S4': {
                        title: '骶骨段 (S2-S4) - 盆腔副交感底座',
                        description: '支配降结肠、直肠、膀胱等。调整髂关节可恢复神经传导，处理肠易激综合征 (IBS) 或盆腔疼痛。',
                        organs: ['colon', 'rectum', 'bladder', 'reproductive'],
                        organs_text: '降结肠、直肠、膀胱、生殖器官',
                        nerve_type: '副交感神经（盆内脏神经）',
                    },
                },
            };
        }
    </script>
</x-filament-panels::page>
