<div x-show="confirmModal" tabindex="-1"
    class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6" style="display: none" x-cloak
    @keydown.escape.window="closeConfirm()">
    <div x-show="confirmModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-[#0D0D12]/45 backdrop-blur-sm" @click="closeConfirm()"></div>

    <div x-show="confirmModal" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative w-full max-w-[440px] bg-white border border-[#DFE1E6] rounded-2xl shadow-[0_20px_50px_rgba(13,13,18,0.18)] flex flex-col overflow-hidden max-h-full">

        <div class="px-5 pt-5 pb-4 flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                :class="confirmVariant === 'danger' ? 'bg-[#FADAE1] text-[#DF1C41]' : (confirmVariant === 'warning' ? 'bg-[#F9ECCB] text-[#956321]' : 'bg-[#DDF2EE] text-[#287F6E]')">
                <svg x-show="confirmVariant === 'success'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg x-show="confirmVariant === 'danger'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <svg x-show="confirmVariant === 'warning'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="flex-1 min-w-0 pt-1 text-[16px] font-semibold leading-snug tracking-tight text-[#0D0D12]"
                x-text="confirmTitle"></h3>
            <button type="button" @click="closeConfirm()" aria-label="Tutup"
                class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg cursor-pointer text-[#A4ABB8] hover:text-[#0D0D12] hover:bg-[#F6F8FA] transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="px-5 pb-5">
            <p class="text-[14px] leading-relaxed text-[#666D80]" x-text="confirmMessage"></p>
        </div>

        <div class="px-5 py-4 border-t border-[#DFE1E6] flex items-center gap-2">
            <button type="button" @click="closeConfirm()"
                class="flex-1 px-4 py-2 text-[13px] font-medium text-[#353849] bg-white border border-[#DFE1E6] hover:bg-[#F6F8FA] rounded-lg cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2A3A7C]/30">
                Batal
            </button>
            <form :action="confirmAction" method="POST" class="flex-1 m-0"
                @submit="if (isConfirmSubmitting) { $event.preventDefault(); return; } isConfirmSubmitting = true">
                @csrf
                <input type="hidden" name="_method" :value="confirmMethod">
                <template x-if="confirmStatus && !confirmIsBulk">
                    <input type="hidden" name="status_pendaftaran" :value="confirmStatus">
                </template>
                <template x-if="confirmIsBulk && confirmData && confirmData.length > 0">
                    <template x-for="id in confirmData" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                </template>
                <button type="submit" :disabled="isConfirmSubmitting" x-ref="confirmActionBtn"
                    :class="(confirmVariant === 'danger' ? 'bg-[#DF1C41] hover:bg-[#95122B]' : (confirmVariant === 'warning' ? 'bg-[#956321] hover:bg-[#5B3D1E]' : 'bg-[#2A3A7C] hover:bg-[#1E2A5E]')) + ' w-full px-4 py-2 text-[13px] font-medium text-white rounded-lg cursor-pointer transition-colors inline-flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2A3A7C]/40' + (isConfirmSubmitting ? ' opacity-60 cursor-not-allowed' : '')">
                    <svg x-show="isConfirmSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span
                        x-text="isConfirmSubmitting ? 'Memproses...' : (confirmConfirmText || 'Ya, Lanjutkan')"></span>
                </button>
            </form>
        </div>
    </div>
</div>
