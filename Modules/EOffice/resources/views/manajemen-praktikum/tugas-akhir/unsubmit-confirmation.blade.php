<div x-data="{open:false}" style="margin-top:12px">
    <button type="button" class="mp-btn secondary sm" @click="open=true">Batalkan Pengumpulan</button>
    <template x-teleport="body">
        <div class="mp-confirm-special" x-show="open" x-cloak x-transition.opacity.duration.200ms @keydown.escape.window="open=false" @click.self="open=false" style="display:none;">
            <form method="POST" action="{{ route($base.'unsubmit',$routeArgs) }}" role="dialog" aria-modal="true" aria-label="Batalkan Pengumpulan" style="width:480px;max-width:100%;background:white;border-radius:14px;overflow:hidden;margin:0">
                @csrf
                <input type="hidden" name="tahap_id" value="{{ $stage['id'] }}"><input type="hidden" name="submission_token" value="{{ $submission->token() }}">
                <div style="padding:20px 22px;border-bottom:1px solid #DFE1E7;font-size:16px;font-weight:700">Batalkan Pengumpulan</div>
                <p style="padding:20px 22px;margin:0;font-size:13px;color:#666D80;line-height:1.6">Pengumpulan akan kembali berstatus Belum Dikumpulkan. Berkas dan riwayat tetap disimpan. Kumpulkan kembali sebelum tenggat. Apakah Anda yakin ingin membatalkan pengumpulan?</p>
                <div style="padding:16px 22px;background:#FAFBFC;border-top:1px solid #DFE1E7;display:flex;justify-content:flex-end;gap:10px"><button type="button" class="mp-btn secondary md" @click="open=false">Kembali</button><button type="submit" class="mp-btn error md">Batalkan Pengumpulan</button></div>
            </form>
        </div>
    </template>
</div>
