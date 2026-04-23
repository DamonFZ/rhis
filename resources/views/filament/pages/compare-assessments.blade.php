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
                    <h1 class="text-2xl font-bold text-gray-800">康复成效对比报告</h1>
                    <p class="text-gray-600 mt-1">
                        @if ($firstRecord && $firstRecord->patientProfile)
                            患者：{{ $firstRecord->patientProfile->name }}
                        @endif
                    </p>
                </div>
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2-2V6a2 2 0 012-2h6a2 2 0 012 2zm11-15v4a2 2 0 01-2 2H9a2 2 0 01-2-2V6a2 2 0 012-2h6a2 2 0 012 2z"></path>
                    </svg>
                    打印报告
                </button>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-semibold mb-4">基础体测</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">指标名称</th>
                                @foreach ($records as $record)
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $record->assessment_date->format('Y-m-d') }}
                                    </th>
                                @endforeach
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">总成效</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $basicLabels = [
                                    'height' => '身高 (cm)',
                                    'weight' => '体重 (kg)',
                                    'bmi' => 'BMI',
                                    'body_fat_rate' => '体脂率 (%)',
                                ];
                            @endphp
                            @foreach ($basicLabels as $key => $label)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $label }}</td>
                                    @foreach ($records as $record)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $record->$key ?? '-' }}
                                        </td>
                                    @endforeach
                                    @php
                                        $delta = $differences['basic'][$key]['delta'] ?? 0;
                                        $hasChange = $delta != 0;
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ $hasChange ? 'font-bold' : '' }}" style="{{ $hasChange ? 'color: #b2c5d5' : '' }}">
                                        @if ($delta > 0)
                                            &uarr; +{{ number_format(abs($delta), 2) }}
                                        @elseif ($delta < 0)
                                            &darr; -{{ number_format(abs($delta), 2) }}
                                        @else
                                            无变化
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-semibold mb-4">详细围度</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">指标名称</th>
                                @foreach ($records as $record)
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $record->assessment_date->format('Y-m-d') }}
                                    </th>
                                @endforeach
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">总成效</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $circLabels = [
                                    'chest' => '胸围 (cm)',
                                    'waist' => '腰围 (cm)',
                                    'hip' => '臀围 (cm)',
                                    'left_arm' => '左臂围 (cm)',
                                    'right_arm' => '右臂围 (cm)',
                                    'left_thigh' => '左大腿围 (cm)',
                                    'right_thigh' => '右大腿围 (cm)',
                                ];
                            @endphp
                            @foreach ($circLabels as $key => $label)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $label }}</td>
                                    @foreach ($records as $record)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $record->circumference[$key] ?? '-' }}
                                        </td>
                                    @endforeach
                                    @php
                                        $delta = $differences['circumference'][$key]['delta'] ?? 0;
                                        $hasChange = $delta != 0;
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ $hasChange ? 'font-bold' : '' }}" style="{{ $hasChange ? 'color: #b2c5d5' : '' }}">
                                        @if ($delta > 0)
                                            &uarr; +{{ number_format(abs($delta), 2) }}
                                        @elseif ($delta < 0)
                                            &darr; -{{ number_format(abs($delta), 2) }}
                                        @else
                                            无变化
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-semibold mb-4">柔软度评估</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">指标名称</th>
                                @foreach ($records as $record)
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $record->assessment_date->format('Y-m-d') }}
                                    </th>
                                @endforeach
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">总成效</th>
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
                                    'shoulder_2' => '肩部2',
                                ];
                            @endphp
                            @foreach ($flexLabels as $key => $label)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $label }}</td>
                                    @foreach ($records as $record)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $record->flexibility[$key] ?? '-' }}
                                        </td>
                                    @endforeach
                                    @php
                                        $changed = $differences['flexibility'][$key]['changed'] ?? false;
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ $changed ? 'font-bold' : '' }}" style="{{ $changed ? 'color: #b2c5d5' : '' }}">
                                        {{ $changed ? '有变化' : '无变化' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-semibold mb-4">体态评估-侧面</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">指标名称</th>
                                @foreach ($records as $record)
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $record->assessment_date->format('Y-m-d') }}
                                    </th>
                                @endforeach
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">总成效</th>
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
                                    'side_knee' => '膝关节',
                                ];
                            @endphp
                            @foreach ($postureSideLabels as $key => $label)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $label }}</td>
                                    @foreach ($records as $record)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $record->posture_tags[$key] ? implode(', ', $record->posture_tags[$key]) : '-' }}
                                        </td>
                                    @endforeach
                                    @php
                                        $changed = $differences['posture_side'][$key]['changed'] ?? false;
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ $changed ? 'font-bold' : '' }}" style="{{ $changed ? 'color: #b2c5d5' : '' }}">
                                        {{ $changed ? '有变化' : '无变化' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-semibold mb-4">体态评估-背面</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">指标名称</th>
                                @foreach ($records as $record)
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $record->assessment_date->format('Y-m-d') }}
                                    </th>
                                @endforeach
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">总成效</th>
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
                                    'back_foot' => '足弓',
                                ];
                            @endphp
                            @foreach ($postureBackLabels as $key => $label)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $label }}</td>
                                    @foreach ($records as $record)
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $record->posture_tags[$key] ? implode(', ', $record->posture_tags[$key]) : '-' }}
                                        </td>
                                    @endforeach
                                    @php
                                        $changed = $differences['posture_back'][$key]['changed'] ?? false;
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center {{ $changed ? 'font-bold' : '' }}" style="{{ $changed ? 'color: #b2c5d5' : '' }}">
                                        {{ $changed ? '有变化' : '无变化' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
