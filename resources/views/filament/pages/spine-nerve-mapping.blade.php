<x-filament-panels::page>
    {{-- 1. 使用 push 将样式抽离到头部，彻底避开 Livewire 解析树 --}}
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@300;400;500;700&display=swap" rel="stylesheet">
        <style>
            .fade-in { animation: fadeIn 0.4s ease-out forwards; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .spine-block { transition: all 0.3s ease; }
            .spine-block:hover { transform: translateX(5px); }
            .spine-container { position: relative; }
            .spine-container::before {
                content: ''; position: absolute; left: 1.5rem; top: 2rem; bottom: 2rem;
                width: 4px; background-color: #e2e8f0; z-index: 0; border-radius: 2px;
            }
            /* 强制 FontAwesome 图标继承父元素文本颜色 */
            .fa-solid, .fa-regular, .fa-brands {
                color: inherit;
            }
        </style>
    @endpush

    {{-- 2. 核心隔离区：增加 wire:ignore 强制 Livewire 放行，绝不干涉内部 DOM --}}
    <div wire:ignore>
        <div class="w-full mx-auto">
            {{-- 头部信息 --}}
            <header class="text-center mb-10 fade-in">
                <h1 class="text-3xl md:text-4xl font-bold text-slate-800 mb-3 tracking-wide">
                    <i class="fa-solid fa-bone text-indigo-500 mr-2"></i>脊柱-内脏神经映射系统
                </h1>
                <p class="text-slate-500 max-w-2xl mx-auto text-sm md:text-base">
                    基于现代解剖学与自主神经系统的可视化图谱。探索骨医（整骨师）如何通过徒手干预特定的脊柱节段，打破"体表-内脏"恶性反射弧，重启人体内稳态。
                </p>
            </header>

            <div class="grid grid-cols-12 gap-6 lg:gap-8 relative">

                {{-- 左侧：脊柱导航栏 (占据 4 列) --}}
                <div class="col-span-12 md:col-span-4 xl:col-span-3 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 fade-in">
                    <h2 class="text-lg font-bold text-slate-700 mb-6 flex items-center">
                        <i class="fa-solid fa-network-wired mr-2 text-slate-400"></i> 选择脊柱节段
                    </h2>

                    <div class="spine-container space-y-4 relative z-10" id="spine-nav">
                        {{-- 导航按钮由 JS 动态生成 --}}
                    </div>
                </div>

                {{-- 右侧：详情展示面板 (占据 8 列) --}}
                <div class="col-span-12 md:col-span-8 xl:col-span-9 bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden fade-in relative min-h-[500px]">

                    {{-- 顶部彩色装饰条 --}}
                    <div id="panel-header-color" class="h-2 bg-slate-300 w-full transition-colors duration-500"></div>

                    <div class="p-8">
                        {{-- 标题与副标题 --}}
                        <div class="mb-8 border-b border-slate-100 pb-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h2 id="panel-title" class="text-3xl font-bold text-slate-800 mb-2">选择左侧节段</h2>
                                    <p id="panel-subtitle" class="text-slate-500 italic">查看对应的神经反射与器官映射</p>
                                </div>
                                <span id="nerve-badge" class="px-4 py-1.5 rounded-full text-sm font-semibold text-slate-600 bg-slate-100 hidden">
                                    神经类型
                                </span>
                            </div>
                        </div>

                        <div id="content-area" class="hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">

                                {{-- 支配器官 --}}
                                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                                    <h3 class="text-md font-bold text-slate-700 mb-4 flex items-center">
                                        <i class="fa-solid fa-heart-pulse mr-2 text-rose-400"></i> 主要支配器官
                                    </h3>
                                    <div id="organs-list" class="flex flex-wrap gap-2">
                                        {{-- 标签生成 --}}
                                    </div>
                                </div>

                                {{-- 常见症状 --}}
                                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                                    <h3 class="text-md font-bold text-slate-700 mb-4 flex items-center">
                                        <i class="fa-solid fa-triangle-exclamation mr-2 text-amber-400"></i> 关联功能性障碍
                                    </h3>
                                    <ul id="symptoms-list" class="space-y-2 text-sm text-slate-600 list-disc list-inside">
                                        {{-- 列表生成 --}}
                                    </ul>
                                </div>
                            </div>

                            {{-- 骨医干预逻辑 --}}
                            <div class="bg-indigo-50/50 rounded-xl p-6 border border-indigo-100">
                                <h3 class="text-md font-bold text-indigo-900 mb-3 flex items-center">
                                    <i class="fa-solid fa-hands-medical mr-2 text-indigo-500"></i> 徒手干预底层逻辑 (生理学依据)
                                </h3>
                                <p id="intervention-logic" class="text-indigo-800/80 text-sm leading-relaxed">
                                    {{-- 逻辑文本 --}}
                                </p>
                            </div>
                        </div>

                        {{-- 初始占位提示 --}}
                        <div id="empty-state" class="flex flex-col items-center justify-center h-64 text-slate-400">
                            <i class="fa-solid fa-hand-pointer text-4xl mb-4 opacity-50"></i>
                            <p>请点击左侧脊柱节段，探索深层人体网络。</p>
                        </div>

                    </div>
                </div>
            </div>

            {{-- 底部说明 --}}
            <div class="mt-8 text-center text-xs text-slate-400 fade-in">
                <p><i class="fa-solid fa-circle-info mr-1"></i> 免责声明：本图谱仅供解剖学与生物力学理论探讨。徒手治疗适用于功能性障碍与代偿性疼痛，无法替代器质性病变的现代医疗干预。</p>
            </div>
        </div>

        {{-- Tailwind JIT 防丢色安全屋 --}}
        <div class="hidden border-indigo-500 bg-indigo-50/30 shadow-md border-transparent text-sky-500 text-orange-500 bg-sky-100 text-sky-700 bg-orange-100 text-orange-700 bg-sky-500 bg-orange-500"></div>
    </div>

    {{-- 3. 使用 push 将脚本抽离到底部，彻底避开 Livewire 的正则扫描 --}}
    @push('scripts')
        <script>
            // --- 核心数据配置 ---
            const spineData = {
                cervical: {
                    id: 'cervical',
                    title: "颈椎段 (C1-C7)",
                    subtitle: "副交感神经的「总闸门」",
                    nerveType: "副交感神经 (主要为迷走神经)",
                    sysType: "parasympathetic",
                    icon: "fa-brain",
                    organs: [
                        { name: "大脑/脑干", icon: "fa-brain" },
                        { name: "面部器官", icon: "fa-eye" },
                        { name: "心脏/肺部", icon: "fa-heart" },
                        { name: "胃肠道", icon: "fa-utensils" },
                        { name: "膈肌 (C3-C5)", icon: "fa-lungs" }
                    ],
                    symptoms: [
                        "血管性/紧张性头痛",
                        "交感极度活跃导致的睡眠障碍",
                        "呼吸表浅 (神经受限)",
                        "咽部异物感 / 迷走神经张力过高或过低"
                    ],
                    logic: "上颈段（枕骨-C2）直接关系到迷走神经的出口。骨医处理枕下肌群和寰枢椎错位，并非因为颈神经直连内脏，而是为了解除对迷走神经的机械性压迫，激活副交感神经，从而降低心率、促进胃肠蠕动。释放中颈段(C3-C5)则直接恢复膈神经传导，重启深度的腹式呼吸泵。"
                },
                upperThoracic: {
                    id: 'upperThoracic',
                    title: "胸椎上段 (T1-T4)",
                    subtitle: "交感神经的「心肺中枢」",
                    nerveType: "交感神经 (星状神经节/上胸交感节)",
                    sysType: "sympathetic",
                    icon: "fa-lungs-virus",
                    organs: [
                        { name: "心脏", icon: "fa-heartbeat" },
                        { name: "肺部/气管", icon: "fa-lungs" },
                        { name: "头面部血管", icon: "fa-head-side-virus" }
                    ],
                    symptoms: [
                        "心悸 / 心动过速 (排除器质性)",
                        "胸闷 / 气短",
                        "神经性咳嗽 / 支气管痉挛",
                        "上肢麻木发冷 (交感导致血管收缩)"
                    ],
                    logic: "T1-T4 区域紧贴支配心肺的交感神经链。圆肩驼背等不良体态易导致该区域小关节卡压。持续的炎症会刺激交感神经节，引发心率异常或呼吸局促。徒手复位此区域，配合胸廓松动，旨在打破异常的传入神经冲动，降低交感神经兴奋度（踩下刹车），恢复心肺自律节律。"
                },
                midLowerThoracic: {
                    id: 'midLowerThoracic',
                    title: "胸椎中下段 (T5-T11)",
                    subtitle: "交感神经的「消化大本营」",
                    nerveType: "交感神经 (内脏大/小神经)",
                    sysType: "sympathetic",
                    icon: "fa-stomach",
                    organs: [
                        { name: "胃部", icon: "fa-hamburger" },
                        { name: "肝脏/胆囊", icon: "fa-capsules" },
                        { name: "脾脏/胰腺", icon: "fa-dna" },
                        { name: "小肠/肾脏", icon: "fa-water" }
                    ],
                    symptoms: [
                        "功能性消化不良 / 反酸",
                        "压力型胃痉挛",
                        "胆汁分泌异常",
                        "胸背部持续性束带感疼痛"
                    ],
                    logic: "这里是典型的『体表-内脏反射（Somatovisceral Reflex）』高发区。T5-T9 脊椎僵硬产生的错误电信号，会欺骗中枢神经系统，使其持续发出交感冲动，抑制胃肠蠕动并减少内脏血供。骨医的推按或高速低幅推拿（HVLA），通过强烈刺激本体感受器，覆盖并重置这一错误的反射弧，恢复消化系统血流。"
                },
                lumbarSacral: {
                    id: 'lumbarSacral',
                    title: "腰骶段 (T12-L2, S2-S4)",
                    subtitle: "盆腔的「副交感底座」",
                    nerveType: "交感(腰段) & 副交感(段)",
                    sysType: "parasympathetic",
                    icon: "fa-person-half-dress",
                    organs: [
                        { name: "大肠/直肠", icon: "fa-poop" },
                        { name: "膀胱/泌尿系", icon: "fa-toilet" },
                        { name: "生殖系统", icon: "fa-venus-mars" },
                        { name: "下肢血管", icon: "fa-shoe-prints" }
                    ],
                    symptoms: [
                        "便秘 / 肠易激综合征 (IBS)",
                        "尿频 / 膀胱过动",
                        "不明原因的盆腔痛 / 痛经",
                        "下肢静脉回流受阻"
                    ],
                    logic: "骨盆前倾或骶骨扭转不仅是力学问题，更是神经问题。S2-S4 发出盆内脏神经，是管控下半身代谢与排泄的副交感核心。调整骶髂关节（SI Joint），一方面解除了腰椎过度伸展对交感神经的压迫，另一方面直接释放了骶段副交感神经的受限。颅骶疗法（CST）极为看重此区域，认为其与颅骨构成液压泵的闭环。"
                }
            };

            // --- DOM 元素引用 ---
            const navContainer = document.getElementById('spine-nav');
            const emptyState = document.getElementById('empty-state');
            const contentArea = document.getElementById('content-area');

            // 面板元素
            const pHeaderColor = document.getElementById('panel-header-color');
            const pTitle = document.getElementById('panel-title');
            const pSubtitle = document.getElementById('panel-subtitle');
            const pBadge = document.getElementById('nerve-badge');
            const pOrgans = document.getElementById('organs-list');
            const pSymptoms = document.getElementById('symptoms-list');
            const pLogic = document.getElementById('intervention-logic');

            let currentActiveId = null;

            // --- 初始化生成导航菜单 ---
            function initNav() {
                Object.values(spineData).forEach((data, index) => {
                    const btn = document.createElement('button');
                    btn.className = `spine-block w-full flex items-center p-4 rounded-xl text-left bg-white border-2 border-transparent hover:border-slate-200 shadow-sm relative z-10 transition-all duration-300`;
                    btn.id = `nav-btn-${data.id}`;
                    btn.onclick = () => selectRegion(data.id);

                    // 根据类型设置图标颜色
                    const iconColor = data.sysType === 'parasympathetic' ? 'text-sky-500' : 'text-orange-500';

                    btn.innerHTML = `
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center mr-4 border border-slate-100 shadow-inner">
                            <i class="fa-solid flex-shrink-0 ${data.icon} ${iconColor}"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-[15px]">${data.title}</h3>
                            <p class="text-xs text-slate-500 mt-1">${data.nerveType.split(' ')[0]}</p>
                        </div>
                    `;
                    navContainer.appendChild(btn);
                });
            }

            // --- 处理点击与渲染逻辑 ---
            function selectRegion(id) {
                if (currentActiveId === id) return;
                currentActiveId = id;
                const data = spineData[id];

                // 1. 更新导航高亮状态
                document.querySelectorAll('.spine-block').forEach(el => {
                    el.classList.remove('border-indigo-500', 'bg-indigo-50/30', 'shadow-md');
                    el.classList.add('border-transparent');
                });
                const activeBtn = document.getElementById(`nav-btn-${id}`);
                activeBtn.classList.remove('border-transparent');
                activeBtn.classList.add('border-indigo-500', 'bg-indigo-50/30', 'shadow-md');

                // 2. 隐藏初始状态，显示内容区 (添加渐变动画效果)
                emptyState.classList.add('hidden');
                contentArea.classList.remove('hidden');

                // 重置动画
                contentArea.classList.remove('fade-in');
                void contentArea.offsetWidth; // 触发重排
                contentArea.classList.add('fade-in');

                // 3. 填充数据
                pTitle.innerText = data.title;
                pSubtitle.innerText = data.subtitle;

                // 神经徽章设置
                pBadge.classList.remove('hidden', 'bg-sky-100', 'text-sky-700', 'bg-orange-100', 'text-orange-700');
                if (data.sysType === 'parasympathetic') {
                    pBadge.classList.add('bg-sky-100', 'text-sky-700');
                    pHeaderColor.className = 'h-2 w-full transition-colors duration-500 bg-sky-500';
                } else {
                    pBadge.classList.add('bg-orange-100', 'text-orange-700');
                    pHeaderColor.className = 'h-2 w-full transition-colors duration-500 bg-orange-500';
                }
                pBadge.innerHTML = `<i class="fa-solid ${data.sysType === 'parasympathetic' ? 'fa-leaf' : 'fa-bolt'} mr-1"></i> ${data.nerveType}`;

                // 渲染器官列表
                pOrgans.innerHTML = data.organs.map(organ => `
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white border border-slate-200 shadow-sm text-sm font-medium text-slate-700">
                        <i class="fa-solid ${organ.icon} mr-2 text-slate-400"></i> ${organ.name}
                    </span>
                `).join('');

                // 渲染症状列表
                pSymptoms.innerHTML = data.symptoms.map(sym => `
                    <li class="flex items-start">
                        <span class="mr-2 mt-1 text-[10px] text-amber-500"><i class="fa-solid fa-circle"></i></span>
                        ${sym}
                    </li>
                `).join('');

                // 渲染逻辑
                pLogic.innerText = data.logic;
            }

            // 初始化
            document.addEventListener('DOMContentLoaded', () => {
                initNav();
            });
        </script>
    @endpush
</x-filament-panels::page>
