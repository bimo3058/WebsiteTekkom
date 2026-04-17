<div 
    x-data="toastManager()"
    @notify.window="addToast($event.detail)"
    class="fixed top-6 left-0 right-0 z-[100] flex flex-col items-center gap-3 pointer-events-none"
>
    <!-- Menangkap session flash default dari Laravel -->
    @if(session('success'))
        <div x-init="addToast({ type: 'success', message: '{{ addslashes(session('success')) }}' })"></div>
    @endif
    
    @if(session('error'))
        <div x-init="addToast({ type: 'error', message: '{{ addslashes(session('error')) }}' })"></div>
    @endif

    @if($errors->any())
        <div x-init="addToast({ type: 'error', message: '{{ addslashes($errors->first()) }}' })"></div>
    @endif

    @if(session('warning'))
        <div x-init="addToast({ type: 'warning', message: '{{ addslashes(session('warning')) }}' })"></div>
    @endif

    @if(session('info'))
        <div x-init="addToast({ type: 'info', message: '{{ addslashes(session('info')) }}' })"></div>
    @endif

    @if(session('toast'))
        <div x-init="addToast({{ json_encode(session('toast')) }})"></div>
    @endif

    <template x-for="toast in toasts" :key="toast.id">
        <div 
            x-show="toast.visible"
            x-transition:enter="transition ease-&lsqb;cubic-bezier(0.16,1,0.3,1)&rsqb; duration-500"
            x-transition:enter-start="opacity-0 -translate-y-6 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
            @mouseenter="pauseToast(toast.id)"
            @mouseleave="resumeToast(toast.id)"
            class="pointer-events-auto w-80 sm:w-96 flex items-start gap-3 p-4 rounded-2xl shadow-2xl border bg-white overflow-hidden relative"
            :class="{
                'border-emerald-100 shadow-emerald-500/10': toast.type === 'success',
                'border-rose-100 shadow-rose-500/10': toast.type === 'error',
                'border-amber-100 shadow-amber-500/10': toast.type === 'warning',
                'border-blue-100 shadow-blue-500/10': toast.type === 'info'
            }"
        >
            <!-- Progress Bar Atas -->
            <div class="absolute top-0 left-0 h-1 bg-slate-100 w-full">
                <div class="h-full origin-left transition-all ease-linear"
                     :class="{
                        'bg-emerald-500': toast.type === 'success',
                        'bg-rose-500': toast.type === 'error',
                        'bg-amber-500': toast.type === 'warning',
                        'bg-blue-500': toast.type === 'info'
                     }"
                     :style="`width: ${toast.progress}%`">
                </div>
            </div>

            <!-- Ikon Status Dinamis -->
            <div class="flex-shrink-0 w-9 h-9 rounded-xl flex items-center justify-center pt-0.5"
                 :class="{
                    'bg-emerald-50 text-emerald-600': toast.type === 'success',
                    'bg-rose-50 text-rose-600': toast.type === 'error',
                    'bg-amber-50 text-amber-600': toast.type === 'warning',
                    'bg-blue-50 text-blue-600': toast.type === 'info'
                 }">
                <!-- Ikon Success -->
                <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <!-- Ikon Error -->
                <svg x-show="toast.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                <!-- Ikon Warning -->
                <svg x-show="toast.type === 'warning'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <!-- Ikon Info -->
                <svg x-show="toast.type === 'info'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>

            <!-- Pesan -->
            <div class="flex-1 pt-1.5 pb-0.5">
                <h4 class="text-xs font-bold uppercase tracking-wider mb-0.5"
                    :class="{
                        'text-emerald-700': toast.type === 'success',
                        'text-rose-700': toast.type === 'error',
                        'text-amber-700': toast.type === 'warning',
                        'text-blue-700': toast.type === 'info'
                    }" 
                    x-text="toast.type === 'success' ? 'Berhasil' : (toast.type === 'error' ? 'Kesalahan' : (toast.type === 'warning' ? 'Peringatan' : 'Informasi'))"></h4>
                <p class="text-[13.5px] font-medium text-slate-700 leading-snug" x-text="toast.message"></p>
            </div>

            <!-- Tombol Close -->
            <button @click="removeToast(toast.id)" class="text-slate-400 hover:text-slate-600 transition-colors pt-1.5 focus:outline-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </template>
</div>

<script>
    function toastManager() {
        return {
            toasts: [],
            addToast(notice) {
                const id = Date.now() + Math.floor(Math.random() * 1000);
                const toast = { 
                    id, 
                    type: notice.type || 'info', 
                    message: notice.message, 
                    visible: true,
                    timer: null,
                    progressInterval: null,
                    duration: 5000,
                    timeLeft: 5000, 
                    startTime: Date.now(),
                    progress: 100
                };
                
                this.toasts.push(toast);
                
                // Beri jeda kecil sebelum start agar transisi progress bar mulus
                setTimeout(() => this.startTimer(toast), 50);
            },
            startTimer(toast) {
                toast.startTime = Date.now();
                // Set timeout removal
                toast.timer = setTimeout(() => { this.removeToast(toast.id); }, toast.timeLeft);
                
                // Set interval untuk animasi progress bar
                toast.progressInterval = setInterval(() => {
                    const elapsed = Date.now() - toast.startTime;
                    toast.progress = Math.max(0, ((toast.timeLeft - elapsed) / toast.duration) * 100);
                }, 10);
            },
            pauseToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    if(toast.timer) clearTimeout(toast.timer);
                    if(toast.progressInterval) clearInterval(toast.progressInterval);
                    toast.timeLeft -= (Date.now() - toast.startTime);
                }
            },
            resumeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) this.startTimer(toast);
            },
            removeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.visible = false;
                    if(toast.timer) clearTimeout(toast.timer);
                    if(toast.progressInterval) clearInterval(toast.progressInterval);
                    
                    // Eksekusi hapus di array sesudah animasi CSS leave
                    setTimeout(() => { 
                        this.toasts = this.toasts.filter(t => t.id !== id); 
                    }, 300);
                }
            }
        }
    }
</script>
