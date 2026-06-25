<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ __('mobile.imaging_comparison') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .photo-zoom-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            background: rgba(0, 0, 0, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
        }
        .photo-zoom-overlay.active {
            display: flex;
        }
        .photo-zoom-overlay img {
            max-width: 95%;
            max-height: 95vh;
            object-fit: contain;
        }
        .photo-zoom-overlay .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
        }
    </style>
</head>
<body class="bg-[#F8F9FA] min-h-screen font-sans pb-10">

    <div class="bg-white px-5 py-4 flex items-center sticky top-0 z-10 shadow-sm">
        <a href="{{ route('mobile.records.index') }}" class="text-gray-600 p-1 -ml-1">
            <x-heroicon-o-chevron-left class="w-6 h-6" />
        </a>
        <h1 class="text-lg font-medium text-gray-800 ml-4">{{ __('mobile.imaging_comparison') }}</h1>
    </div>

    <div class="px-4 mt-4 space-y-6">
        @forelse($pairs as $angle => $pair)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800">{{ $pair['label'] }}</h3>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-red-400">{{ $pair['date_before'] }}</span>
                        <span class="text-xs text-green-400">{{ $pair['date_after'] }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-0.5 p-0.5">
                    <div class="relative" onclick="zoomPhoto(this)">
                        <img src="{{ $pair['before'] }}" alt="Before" class="w-full h-64 object-cover" loading="lazy">
                        <span class="absolute top-2 left-2 px-2 py-0.5 bg-red-500/80 text-white text-xs rounded">{{ __('mobile.imaging_type_before') }}</span>
                    </div>
                    <div class="relative" onclick="zoomPhoto(this)">
                        <img src="{{ $pair['after'] }}" alt="After" class="w-full h-64 object-cover" loading="lazy">
                        <span class="absolute top-2 right-2 px-2 py-0.5 bg-green-500/80 text-white text-xs rounded">{{ __('mobile.imaging_type_after') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-o-photo class="w-10 h-10 text-gray-300" />
                </div>
                <p class="text-gray-500 text-sm">{{ __('mobile.no_same_angle') }}</p>
            </div>
        @endforelse
    </div>

    <div class="photo-zoom-overlay" id="zoomOverlay" onclick="closeZoom(event)">
        <button class="close-btn">&times;</button>
        <img src="" alt="Zoomed" id="zoomImage">
    </div>

    <script>
        function zoomPhoto(el) {
            const img = el.querySelector('img');
            const overlay = document.getElementById('zoomOverlay');
            const zoomImg = document.getElementById('zoomImage');
            zoomImg.src = img.src;
            overlay.classList.add('active');
        }

        function closeZoom(e) {
            if (e.target === document.getElementById('zoomOverlay') || e.target.classList.contains('close-btn')) {
                document.getElementById('zoomOverlay').classList.remove('active');
            }
        }
    </script>
</body>
</html>
