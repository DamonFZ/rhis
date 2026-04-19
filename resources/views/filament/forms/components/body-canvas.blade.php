<div x-data="bodyCanvas()" x-init="init()" class="space-y-4">
    <div class="flex items-center space-x-4">
        <label class="flex items-center space-x-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4-4 4m8 0h-6a2 2 0 00-2 2v2a2 2 0 002 2h6a2 2 0 002-2v-2a2 2 0 00-2-2z"/>
            </svg>
            <span>上传背景图</span>
            <input type="file" accept="image/*" @change="loadImage($event.target.files[0])" class="hidden">
        </label>
        <button @click="clearDrawing()" type="button" class="flex items-center space-x-2 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>清除涂鸦</span>
        </button>
    </div>
    
    <div class="border-2 border-gray-300 rounded-lg overflow-hidden bg-gray-50">
        <canvas x-ref="canvas"
                @mousedown="startDrawing($event)"
                @mousemove="draw($event)"
                @mouseup="stopDrawing()"
                @mouseleave="stopDrawing()"
                @touchstart="startDrawing($event)"
                @touchmove="draw($event)"
                @touchend="stopDrawing()"
                class="block cursor-crosshair"
                style="width: 100%; max-width: 600px; height: auto;">
        </canvas>
    </div>

    @if ($state)
        <div class="text-sm text-gray-500">
            <span class="font-medium">已保存的图片：</span>
            <span class="text-blue-600">{{ $state }}</span>
        </div>
    @endif

    <input type="hidden" x-model="state" name="{{ $getName() }}" id="{{ $getId() }}" />
</div>

<script>
function bodyCanvas() {
    return {
        state: @json($state),
        canvas: null,
        ctx: null,
        isDrawing: false,
        lastX: 0,
        lastY: 0,
        backgroundImage: null,
        backgroundLoaded: false,
        
        init() {
            this.canvas = this.$refs.canvas;
            this.ctx = this.canvas.getContext('2d');
            
            // 设置 canvas 初始尺寸
            this.canvas.width = 600;
            this.canvas.height = 800;
            
            // 如果有初始状态（已保存的图片），加载它
            if (this.state && !this.state.startsWith('data:image')) {
                this.loadFromUrl(this.state);
            }
        },
        
        loadImage(file) {
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    // 等比例压缩图片，最大宽度600px
                    let width = img.width;
                    let height = img.height;
                    const maxWidth = 600;
                    
                    if (width > maxWidth) {
                        const ratio = maxWidth / width;
                        width = maxWidth;
                        height = height * ratio;
                    }
                    
                    // 设置 canvas 尺寸
                    this.canvas.width = width;
                    this.canvas.height = height;
                    
                    // 保存背景图引用
                    this.backgroundImage = img;
                    this.backgroundLoaded = true;
                    
                    // 绘制背景图
                    this.redraw();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        
        loadFromUrl(url) {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => {
                // 设置 canvas 尺寸
                this.canvas.width = img.width;
                this.canvas.height = img.height;
                
                // 保存背景图引用
                this.backgroundImage = img;
                this.backgroundLoaded = true;
                
                // 绘制背景图
                this.redraw();
            };
            img.onerror = () => {
                console.error('Failed to load image from:', url);
            };
            img.src = url;
        },
        
        redraw() {
            // 清空画布
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            
            // 绘制背景图
            if (this.backgroundImage && this.backgroundLoaded) {
                this.ctx.drawImage(this.backgroundImage, 0, 0, this.canvas.width, this.canvas.height);
            }
        },
        
        startDrawing(e) {
            this.isDrawing = true;
            
            const coords = this.getCoordinates(e);
            this.lastX = coords.x;
            this.lastY = coords.y;
        },
        
        draw(e) {
            if (!this.isDrawing) return;
            
            e.preventDefault();
            
            const coords = this.getCoordinates(e);
            
            // 设置画笔样式
            this.ctx.strokeStyle = '#EF4444';
            this.ctx.lineWidth = 3;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
            
            // 绘制线条
            this.ctx.beginPath();
            this.ctx.moveTo(this.lastX, this.lastY);
            this.ctx.lineTo(coords.x, coords.y);
            this.ctx.stroke();
            
            // 更新最后坐标
            this.lastX = coords.x;
            this.lastY = coords.y;
        },
        
        stopDrawing() {
            if (this.isDrawing) {
                this.isDrawing = false;
                
                // 导出 Base64 并更新状态
                this.state = this.canvas.toDataURL('image/jpeg', 0.8);
            }
        },
        
        getCoordinates(e) {
            const rect = this.canvas.getBoundingClientRect();
            let x, y;
            
            if (e.touches && e.touches.length > 0) {
                // 触摸事件
                x = e.touches[0].clientX - rect.left;
                y = e.touches[0].clientY - rect.top;
            } else {
                // 鼠标事件
                x = e.clientX - rect.left;
                y = e.clientY - rect.top;
            }
            
            // 计算缩放比例
            const scaleX = this.canvas.width / rect.width;
            const scaleY = this.canvas.height / rect.height;
            
            return {
                x: x * scaleX,
                y: y * scaleY
            };
        },
        
        clearDrawing() {
            // 只清除涂鸦，保留背景
            this.redraw();
            
            // 更新状态
            if (this.backgroundImage) {
                this.state = this.canvas.toDataURL('image/jpeg', 0.8);
            } else {
                this.state = null;
            }
        }
    };
}
</script>