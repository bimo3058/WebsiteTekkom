<div
    x-data="toastManager()"
    @notify.window="addToast($event.detail)"
    class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-3 pointer-events-none"
>
    <!-- Menangkap session flash default dari Laravel -->
    @if(session('success'))
        <div x-init="addToast({ type: 'success', message: '{{ addslashes(session('success')) }}' })"></div>
    @endif
    
    @if(session('error'))
        <div x-init="addToast({ type: 'error', message: '{{ addslashes(session('error')) }}' })"></div>
    @endif

    @php($globalErrors = $errors ?? View::shared('errors'))

    @if($globalErrors && $globalErrors->getBag('default')->any() && !View::hasSection('hide_global_errors'))
        <div x-init="addToast({ type: 'error', message: '{{ addslashes($globalErrors->getBag('default')->first()) }}' })"></div>
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
            x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
            x-transition:enter-start="opacity-0 translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            @mouseenter="pauseToast(toast.id)"
            @mouseleave="resumeToast(toast.id)"
            class="pointer-events-auto w-80 sm:w-96 flex items-start gap-3 p-3 rounded-xl border border-[#ECEEF2] bg-white shadow-lg"
        >
            <!-- Ikon Status Dinamis -->
            <div class="flex-shrink-0 w-5 h-5 mt-[1px]" x-cloak>
                <!-- Ikon Success -->
                <svg x-show="toast.type === 'success'" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#E8F7EC"/><path d="M8.5 12.4l2.4 2.4 4.6-5" stroke="#16A34A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <!-- Ikon Error -->
                <svg x-show="toast.type === 'error'" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#FDECEC"/><path d="M9.3 9.3l5.4 5.4m0-5.4l-5.4 5.4" stroke="#DC2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <!-- Ikon Warning -->
                <svg x-show="toast.type === 'warning'" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#FEF6E0"/><path d="M12 8v5m0 2.6h.01" stroke="#D97706" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <!-- Ikon Info -->
                <svg x-show="toast.type === 'info'" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#EAF2FE"/><path d="M12 11h.01M11.5 14.4H12a.6.6 0 0 0 .6-.6V10.6" stroke="#2563EB" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>

            <!-- Pesan -->
            <div class="flex-1 min-w-0 pt-[2px]">
                <p class="text-[13px] font-medium text-slate-700 leading-snug" x-text="toast.message"></p>
            </div>

            <!-- Tombol Close -->
            <button @click="removeToast(toast.id)" class="flex-shrink-0 self-start -mr-1 mt-[2px] p-1 rounded-md text-slate-400 hover:text-slate-600 transition-colors focus:outline-none" aria-label="Tutup">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 7l10 10M17 7L7 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
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
                    timeLeft: 5000,
                    startTime: Date.now(),
                };
                
                this.toasts.push(toast);
                setTimeout(() => { toast.timer = setTimeout(() => { this.removeToast(toast.id); }, toast.timeLeft); }, 50);
            },
            pauseToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    if(toast.timer) clearTimeout(toast.timer);
                    toast.timeLeft -= (Date.now() - toast.startTime);
                }
            },
            resumeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.startTime = Date.now();
                    toast.timer = setTimeout(() => { this.removeToast(toast.id); }, toast.timeLeft);
                }
            },
            removeToast(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.visible = false;
                    if(toast.timer) clearTimeout(toast.timer);
                    
                    // Eksekusi hapus di array sesudah animasi CSS leave
                    setTimeout(() => { 
                        this.toasts = this.toasts.filter(t => t.id !== id); 
                    }, 300);
                }
            }
        }
    }
</script>
