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
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">患者信息</h2>
                @if ($baseAssessment && $baseAssessment->patientProfile)
                    <p class="mb-2"><strong>患者：</strong>{{ $baseAssessment->patientProfile->name }}</p>
                @endif
            </div>
        
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-3">
                        评估 #1 - {{ $baseAssessment?->assessment_no }}
                    </h3>
                    <p><strong>日期：</strong>{{ $baseAssessment?->assessment_date?->format('Y-m-d') }}</p>
                    <p><strong>类型：</strong>{{ match($baseAssessment?->assessment_type) {
                        1 => '初评',
                        2 => '复评',
                        3 => '末评',
                        default => '未知'
                    } }}</p>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-3">
                        评估 #2 - {{ $targetAssessment?->assessment_no }}
                    </h3>
                    <p><strong>日期：</strong>{{ $targetAssessment?->assessment_date?->format('Y-m-d') }}</p>
                    <p><strong>类型：</strong>{{ match($targetAssessment?->assessment_type) {
                        1 => '初评',
                        2 => '复评',
                        3 => '末评',
                        default => '未知'
                    } }}</p>
                </div>
            </div>
            
            @if ($differences)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">基础体测对比</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">项目</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评估 #1</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评估 #2</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">差值</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $basicLabels = [
                                        'height' => '身高 (cm)',
                                        'weight' => '体重 (kg)',
                                        'bmi' => 'BMI',
                                        'body_fat_rate' => '体脂率 (%)'
                                    ];
                                @endphp
                                @foreach ($differences['basic'] as $key => $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $basicLabels[$key] ?? $key }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['base'] ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['target'] ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $data['delta'] > 0 ? 'text-red-600' : ($data['delta'] < 0 ? 'text-green-600' : 'text-gray-500') }}">
                                            {{ $data['delta'] > 0 ? '+' : '' }}{{ number_format($data['delta'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">围度对比</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">部位</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评估 #1</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评估 #2</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">差值</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $circLabels = [
                                        'chest' => '胸围',
                                        'waist' => '腰围',
                                        'hip' => '臀围',
                                        'left_arm' => '左臂围',
                                        'right_arm' => '右臂围',
                                        'left_thigh' => '左大腿围',
                                        'right_thigh' => '右大腿围'
                                    ];
                                @endphp
                                @foreach ($differences['circumference'] as $key => $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $circLabels[$key] ?? $key }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['base'] ? $data['base'] . ' cm' : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['target'] ? $data['target'] . ' cm' : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $data['delta'] > 0 ? 'text-red-600' : ($data['delta'] < 0 ? 'text-green-600' : 'text-gray-500') }}">
                                            {{ $data['delta'] > 0 ? '+' : '' }}{{ number_format($data['delta'], 2) }} cm
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">柔软度对比</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">部位</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评估 #1</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评估 #2</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">变化</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $flexLabels = [
                                        'trunk' => '躯干',
                                        'hamstrings' => '腘绳肌',
                                        'iliopsoas' => '髂腰肌群',
                                        'quadriceps' => '股四头肌',
                                        'calf' => '小腿肌群',
                                        'shoulder_1' => '肩部1',
                                        'shoulder_2' => '肩部2'
                                    ];
                                @endphp
                                @foreach ($differences['flexibility'] as $key => $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $flexLabels[$key] ?? $key }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['base'] ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['target'] ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $data['changed'] ? 'text-yellow-600' : 'text-gray-400' }}">
                                            {{ $data['changed'] ? '✓ 有变化' : '无变化' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">体态评估-侧面对比</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">部位</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评估 #1</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评估 #2</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">变化</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $postureSideLabels = [
                                        'side_head' => '头部',
                                        'side_cervical' => '颈椎',
                                        'side_scapula' => '肩胛骨',
                                        'side_thoracic' => '胸椎',
                                        'side_lumbar' => '腰椎',
                                        'side_pelvis' => '骨盆',
                                        'side_knee' => '膝关节'
                                    ];
                                @endphp
                                @foreach ($differences['posture_side'] as $key => $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $postureSideLabels[$key] ?? $key }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['base'] ? implode(', ', $data['base']) : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['target'] ? implode(', ', $data['target']) : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $data['changed'] ? 'text-yellow-600' : 'text-gray-400' }}">
                                            {{ $data['changed'] ? '✓ 有变化' : '无变化' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">体态评估-背面对比</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">部位</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评估 #1</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">评估 #2</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">变化</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $postureBackLabels = [
                                        'back_cervical' => '颈椎',
                                        'back_shoulder' => '肩部',
                                        'back_scapula' => '肩胛骨',
                                        'back_thoracolumbar' => '胸腰椎',
                                        'back_pelvis' => '骨盆',
                                        'back_knee' => '膝关节',
                                        'back_foot' => '足弓'
                                    ];
                                @endphp
                                @foreach ($differences['posture_back'] as $key => $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $postureBackLabels[$key] ?? $key }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['base'] ? implode(', ', $data['base']) : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['target'] ? implode(', ', $data['target']) : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $data['changed'] ? 'text-yellow-600' : 'text-gray-400' }}">
                                            {{ $data['changed'] ? '✓ 有变化' : '无变化' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
        
        <div class="flex gap-4">
            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800 transition">
                返回
            </a>
        </div>
    </div>
</x-filament-panels::page>
