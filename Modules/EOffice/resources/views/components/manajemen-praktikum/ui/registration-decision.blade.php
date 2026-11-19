@props(['registration', 'type', 'canOverride'=>false, 'canApprove'=>false])
<div class="mp-review-decision" style="padding:16px 20px;border-top:1px solid var(--c-border)" x-data="{decision:'',submitting:false}"
    @keydown.escape.window="if(!submitting)decision=''">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px">
        <span style="font-size:13px;font-weight:600;color:#353849">Keputusan Admin Praktikum</span>
        <div style="display:flex;gap:8px">
            <button type="button" class="mp-btn secondary md" style="width:40px;height:40px;padding:0;color:#0B266E"
                title="Setujui pendaftaran" aria-label="Setujui pendaftaran" @disabled(!$canApprove && !$canOverride)
                @click="decision='approve'">
                <x-eoffice::manajemen-praktikum.ui.icon name="check" :size="18" />
            </button>
            @if($registration->status==='pending')
            <button type="button" class="mp-btn secondary md" style="width:40px;height:40px;padding:0;color:#DF1C41"
                title="Tolak pendaftaran" aria-label="Tolak pendaftaran" @click="decision='reject'">
                <x-eoffice::manajemen-praktikum.ui.icon name="close" :size="18" />
            </button>
            @endif
        </div>
    </div>
    <template x-teleport="body">
        <div class="mp-confirm-overlay" x-show="decision" x-cloak x-transition.opacity.duration.200ms
            @click.self="if(!submitting)decision=''" style="display:none">
            <section class="mp-confirm-card" :class="decision==='reject'?'mp-confirm-danger':''" role="dialog" aria-modal="true"
                x-show="decision" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-3 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-3 scale-95"
                :aria-label="decision==='reject'?'Tolak Pendaftaran':'Setujui Pendaftaran'"
                >
                <div class="mp-confirm-symbol"><x-eoffice::manajemen-praktikum.ui.icon name="alert-circle" :size="26" /></div>
                <h2 class="mp-card-title" style="text-align:center" x-text="decision==='reject'?'Tolak Pendaftaran?':'Setujui Pendaftaran?'"></h2>
                <form method="POST" :action="decision==='reject' ? @js(route('eoffice.manprak.admin.pendaftaran-'.$type.'.reject',$registration->id)) : @js(route('eoffice.manprak.admin.pendaftaran-'.$type.'.approve',$registration->id))"
                    @submit="if(submitting){$event.preventDefault()}else{submitting=true}">
                    @csrf
                    <div style="color:#666D80;font-size:13px;line-height:22px">
                        <p>Pendaftaran {{ $registration->user?->name ?? 'mahasiswa' }} untuk {{ $registration->praktikum?->nama ?? 'praktikum ini' }}.</p>
                        @if($canOverride)
                        <template x-if="decision==='approve'">
                            <div style="margin-top:16px;text-align:left">
                                <input type="hidden" name="override_dosen" value="1">
                                <p style="margin:0 0 12px">Persetujuan ini mengesampingkan hasil review dosen. Keputusan dosen tetap tercatat.</p>
                                <label class="mp-review-label" for="decision-override">Alasan override <span style="color:#DF1C41">*</span></label>
                                <textarea id="decision-override" class="mp-input" name="override_reason" required minlength="10" maxlength="1000" rows="3">{{ old('override_reason') }}</textarea>
                                <p style="margin:6px 0 0">Minimal 10 karakter. Alasan dicatat dalam riwayat audit.</p>
                            </div>
                        </template>
                        @endif
                        <template x-if="decision==='reject'">
                            <div style="margin-top:16px;text-align:left">
                                <label class="mp-review-label" for="decision-rejection">Alasan penolakan <span style="color:#DF1C41">*</span></label>
                                <textarea id="decision-rejection" class="mp-input" name="alasan_penolakan" required maxlength="1000" rows="3">{{ old('alasan_penolakan') }}</textarea>
                            </div>
                        </template>
                    </div>
                    <div class="mp-confirm-actions">
                        <button type="button" class="mp-btn secondary md" :disabled="submitting" @click="decision=''">Batal</button>
                        <button type="submit" class="mp-btn primary md mp-confirm-approve" :disabled="submitting" x-text="submitting?'Memproses…':(decision==='reject'?'Ya, Tolak':'Ya, Setujui')"></button>
                    </div>
                </form>
            </section>
        </div>
    </template>
</div>
