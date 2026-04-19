<div
    x-data="{
        state: @js($getState()),
        
        init() {
            // 初始化时如果有图片，加载它
        },
        
        loadImage(file) {
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                // 直接使用原图，不压缩
                this.state = e.target.result;
                this.$refs.hiddenInput.value = this.state;
                this.$refs.hiddenInput.dispatchEvent(new Event('input'));
            };
            reader.readAsDataURL(file);
        },
        
        clearImage() {
            this.state = null;
            this.$refs.hiddenInput.value = null;
            this.$refs.hiddenInput.dispatchEvent(new Event('input'));
        }
    }"
    x-init="init()"
    class="space-y-4"
    wire:ignore
>
    <div class="flex items-center space-x-4">
        <label class="flex items-center space-x-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4-4 4m8 0h-6a2 2 0 00-2 2v2a2 2 0 002 2h6a2 2 0 002-2v-2a2 2 0 00-2-2z"/>
            </svg>
            <span>上传图片</span>
            <input type="file" accept="image/*" @change="loadImage($event.target.files[0])" class="hidden">
        </label>
        <button @click="clearImage()" type="button" class="flex items-center space-x-2 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>清除图片</span>
        </button>
    </div>
    
    <!-- 图片预览区域 -->
    <div class="border-2 border-gray-300 rounded-lg overflow-hidden bg-gray-50 min-h-[200px] flex items-center justify-center">
        <template x-if="state">
            <img :src="state" class="max-w-full max-h-[600px] object-contain" alt="上传的图片" />
        </template>
        <template x-if="!state">
            <p class="text-gray-400">暂无图片，请点击上方按钮上传</p>
        </template>
    </div>

    <input 
        x-ref="hiddenInput"
        type="hidden" 
        name="{{ $getName() }}"
        x-model="state"
        value="{{ $getState() }}"
    />
</div>
