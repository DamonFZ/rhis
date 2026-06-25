<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('mobile.my_appointments') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F9FA] min-h-screen font-sans pb-10">

    <div class="bg-white px-5 py-4 flex items-center sticky top-0 z-10 shadow-sm">
        <a href="{{ route('mobile.dashboard') }}" class="text-gray-600 p-1 -ml-1">
            <x-heroicon-o-chevron-left class="w-6 h-6" />
        </a>
        <h1 class="text-lg font-medium text-gray-800 ml-4">{{ __('mobile.my_appointments') }}</h1>
    </div>

    @if($records->count() >= 2)
        <div class="px-5 mt-4">
            <div class="flex justify-between items-center mb-3">
                <span class="text-xs text-gray-500">{{ __('mobile.select_two_to_compare') }}</span>
                <button type="button" class="px-4 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-full shadow-sm active:bg-blue-700 transition-colors" onclick="startCompare()">
                    {{ __('mobile.start_comparison') }}
                </button>
            </div>

            <div class="space-y-3" id="record-list">
                @foreach($records as $record)
                    <label class="flex items-center gap-3 bg-white rounded-2xl p-4 shadow-sm border border-gray-100 cursor-pointer active:bg-gray-50">
                        <input type="checkbox" name="record-id" value="{{ $record->id }}" class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="limitSelection(this)">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-md
                                    @if($record->record_type == 1) bg-red-50 text-red-600
                                    @elseif($record->record_type == 2) bg-green-50 text-green-600
                                    @else bg-blue-50 text-blue-600 @endif">
                                    @if($record->record_type == 1) {{ __('mobile.imaging_type_before') }}
                                    @elseif($record->record_type == 2) {{ __('mobile.imaging_type_after') }}
                                    @else {{ __('mobile.imaging_type_during') }} @endif
                                </span>
                                <span class="text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($record->treatment_date)->format('Y-m-d') }}</span>
                            </div>
                            @if($record->remark)
                                <p class="text-xs text-gray-400 truncate">{{ $record->remark }}</p>
                            @endif
                        </div>
                        <x-heroicon-o-photo class="w-5 h-5 text-gray-300" />
                    </label>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-20 px-5">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <x-heroicon-o-photo class="w-10 h-10 text-gray-300" />
            </div>
            <p class="text-gray-500 text-sm">{{ __('mobile.need_two_records') }}</p>
            <p class="text-gray-400 text-xs mt-2">{{ __('mobile.current_records_count', ['count' => $records->count()]) }}</p>
        </div>
    @endif

    <script>
        function limitSelection(checkbox) {
            const checkboxes = document.querySelectorAll('input[name="record-id"]');
            const checked = Array.from(checkboxes).filter(cb => cb.checked);
            if (checked.length > 2) {
                checkbox.checked = false;
            }
        }

        function startCompare() {
            const checked = Array.from(document.querySelectorAll('input[name="record-id"]')).filter(cb => cb.checked);
            if (checked.length !== 2) {
                alert('{{ __('mobile.select_exact_two') }}');
                return;
            }
            const compareUrl = '{{ route('mobile.records.compare') }}?id1=' + checked[0].value + '&id2=' + checked[1].value;
            window.location.href = compareUrl;
        }
    </script>
</body>
</html>
