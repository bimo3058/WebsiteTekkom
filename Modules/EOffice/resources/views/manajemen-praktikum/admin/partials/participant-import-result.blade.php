@php($importReport=session('participant_import_reports.'.$praktikum->id))
@if(!empty($importReport['issues']))
<section class="mp-card mp-import-result" style="margin-bottom:20px;min-width:0;max-width:100%;border-radius:12px;overflow:hidden;" aria-label="Hasil Impor Praktikan">
    <style>
        .mp-import-result th,.mp-import-result td{white-space:normal!important;overflow-wrap:anywhere;box-sizing:border-box}
        @media(max-width:640px){.mp-import-result col:nth-child(1){width:12%!important}.mp-import-result col:nth-child(2){width:18%!important}.mp-import-result col:nth-child(3){width:30%!important}.mp-import-result col:nth-child(4){width:40%!important}.mp-import-result th,.mp-import-result td{padding:12px 8px!important}}
    </style>
    <div class="mp-card-header" style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;border-radius:11px 11px 0 0;"><div style="min-width:0"><h2 class="mp-card-title">Hasil Impor Praktikan</h2>
        <p class="mp-page-sub">{{ $importReport['added'] }} ditambahkan · {{ $importReport['duplicate'] }} duplikat dilewati · {{ count($importReport['issues']) }} perlu diperiksa.</p>
    </div>
    <form method="POST" style="margin-left:auto;flex:0 0 auto;width:auto;" action="{{ route('eoffice.manprak.admin.praktikum.import-complete',$praktikum->id) }}"
        x-data="{confirmOpen:false,confirmed:false,submitting:false}" x-ref="actionForm"
        @submit="if(submitting){$event.preventDefault();return}if(!confirmed){$event.preventDefault();confirmOpen=true}else{submitting=true}">
        @csrf
        <input type="hidden" name="report_token" value="{{ $importReport['token'] }}">
        <input type="hidden" name="confirmed" :value="confirmed?'1':'0'">
        <button type="submit" :disabled="submitting" style="border:0;background:none;color:#0B266E;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap">Tandai selesai</button>
        <x-eoffice::manajemen-praktikum.ui.schedule-confirmation title="Tandai Selesai?"
            message="Hasil impor praktikan akan hilang dari tampilan ini, pastikan semua praktikan yang perlu diperiksa sudah selesai."
            buttonLabel="Ya, Selesai" />
    </form>
    </div>
    <div style="max-height:240px;overflow:auto;scrollbar-gutter:stable;">
        <table class="mp-table" style="table-layout:fixed;width:100%;min-width:0;">
        <colgroup><col style="width:64px"><col><col style="width:190px"><col style="width:260px"></colgroup>
        <thead><tr><th style="text-align:left">Baris</th><th style="text-align:left">Kolom NIM</th><th style="text-align:left">NIM</th><th style="text-align:left">Keterangan</th></tr></thead><tbody>
        @foreach($importReport['issues'] as $issue)
            <tr><td>{{ $issue['row'] }}</td><td>{{ $issue['column'] ?? '—' }}</td><td>{{ $issue['nim'] ?: '—' }}</td><td>{{ $issue['reason'] }}</td></tr>
        @endforeach
        </tbody></table>
    </div>
</section>
@endif
