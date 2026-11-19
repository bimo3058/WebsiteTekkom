<x-eoffice::manajemen-praktikum.layout pageTitle="Koordinator CERC">
    <x-eoffice::manajemen-praktikum.ui.staffing-styles />
    <style>
.mp-cerc-settings .mp-page-header{position:relative;padding-bottom:20px}.mp-cerc-settings .mp-page-header::after{content:"";position:absolute;bottom:0;left:-24px;right:-24px;border-bottom:1px solid var(--c-border)}.mp-cerc-assignment{display:flex;gap:20px;align-items:stretch;min-width:0}.mp-cerc-assignment>.mp-cerc-current{flex:1;min-width:0}.mp-cerc-assignment>.mp-cerc-appointment{width:380px;max-width:100%;flex-shrink:0;overflow:visible}.mp-cerc-holder{padding:24px;display:flex;flex-direction:column;align-items:flex-start;gap:16px}.mp-cerc-appointment>form{padding:24px}.mp-cerc-appointment .mp-card-header{border-radius:12px 12px 0 0}.mp-cerc-save{display:flex;justify-content:flex-end;margin-top:16px}.mp-cerc-empty{display:flex;align-items:flex-start;gap:12px}.mp-cerc-empty>.mp-icon{color:var(--c-error,#DF1C41);flex-shrink:0}.mp-cerc-settings .mp-card-title{font-size:14px;color:#353849}.mp-cerc-settings .mp-settings-description{font-size:12px;line-height:18px;margin-top:4px}.mp-cerc-settings .mp-settings-name,.mp-cerc-settings .mp-settings-table td,.mp-cerc-settings .mp-page-title{color:#353849}.mp-cerc-settings .mp-settings-table td{font-size:12px}.mp-cerc-settings .mp-settings-name{font-size:13px;overflow-wrap:anywhere}
@media(max-width:768px){body[data-mobile-shell] .mp-cerc-settings .mp-page-header::after{left:-12px;right:-12px}.mp-cerc-assignment{flex-direction:column}.mp-cerc-assignment>.mp-cerc-appointment{width:100%}.mp-cerc-holder,.mp-cerc-appointment>form{padding:16px}}
    </style>
    <div class="mp-cerc-settings" style="display:contents">
    <div class="mp-page-header"><div><h1 class="mp-page-title">Koordinator CERC</h1><p class="mp-page-sub">Kelola penanggung jawab Koordinator CERC.</p></div></div>
    @if(session('success'))<div class="mp-alert success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mp-alert warning" role="alert">{{ $errors->first() }}</div>@endif
    <div class="mp-cerc-assignment">
        <section class="mp-card mp-settings-card mp-cerc-current">
            <div class="mp-card-header"><h2 class="mp-card-title">Penanggung Jawab Aktif</h2><span class="mp-badge {{ $coordinator?'success':'neutral' }} sm">{{ $coordinator?'Aktif':'Belum Ditunjuk' }}</span></div>
            <div class="mp-cerc-holder">
                @if($coordinator)<x-eoffice::manajemen-praktikum.ui.staff-person :user="$coordinator" :show-email="true" :wrap="true" />@else<div class="mp-cerc-empty"><x-eoffice::manajemen-praktikum.ui.icon name="alert-circle" size="24" /><div><div class="mp-settings-name">Belum ada Koordinator CERC</div><p class="mp-settings-description">Tunjuk mahasiswa melalui form di samping.</p></div></div>@endif
                @if($coordinator)<form method="POST" action="{{ route('eoffice.manprak.admin.cerc.update') }}" x-data="{confirmOpen:false,confirmed:false,submitting:false}" x-ref="actionForm" @submit="if(submitting){$event.preventDefault();return}if(!confirmed){$event.preventDefault();confirmOpen=true}else{submitting=true}">
                    @csrf @method('PUT')<input type="hidden" name="user_id" value="">
                    <button class="mp-btn destructive sm" :disabled="submitting"><x-eoffice::manajemen-praktikum.ui.icon name="trash" />Cabut Penugasan</button>
                    <x-eoffice::manajemen-praktikum.ui.schedule-confirmation title="Cabut Penugasan CERC" message="Cabut akses Koordinator CERC dari pengurus saat ini? Role mahasiswa dan penugasan praktikum lainnya tetap berlaku." buttonLabel="Ya, Cabut" />
                </form>@endif
            </div>
        </section>
        <section class="mp-card mp-settings-card mp-cerc-appointment">
            <div class="mp-card-header"><h2 class="mp-card-title">{{ $coordinator?'Ganti Koordinator CERC':'Tunjuk Koordinator CERC' }}</h2><x-eoffice::manajemen-praktikum.ui.icon name="plus" /></div>
            <form method="POST" action="{{ route('eoffice.manprak.admin.cerc.update') }}" x-data="{confirmOpen:false,confirmed:false,submitting:false,selectedId:@js((string)old('user_id',$coordinator?->id ?? '')),originalId:@js((string)($coordinator?->id ?? ''))}" x-ref="actionForm" @submit="if(submitting||!selectedId||selectedId===originalId){$event.preventDefault();return}if(!confirmed){$event.preventDefault();confirmOpen=true}else{submitting=true}">
                @csrf @method('PUT')
                @php $options=$candidates->map(fn($u)=>['value'=>(string)$u->id,'label'=>$u->name.' / '.$u->email])->all(); @endphp
                <label class="mp-settings-label">Cari Mahasiswa</label>
                <x-eoffice::manajemen-praktikum.ui.select name="user_id" :options="$options" :selected="(string)old('user_id',$coordinator?->id ?? '')" onChange="selectedId=$event.target.value" placeholder="Pilih mahasiswa..." :searchable="true" searchPlaceholder="Cari nama / email..." />
                <div class="mp-cerc-save"><button class="mp-btn primary md" :disabled="submitting||!selectedId||selectedId===originalId" :style="submitting||!selectedId||selectedId===originalId?'cursor:not-allowed':''">Simpan Penugasan</button></div>
                <x-eoffice::manajemen-praktikum.ui.schedule-confirmation title="Simpan Penugasan CERC" message="Tetapkan mahasiswa terpilih sebagai Koordinator CERC? Akses CERC pengurus sebelumnya akan dicabut. Role lainnya tetap berlaku." buttonLabel="Ya, Simpan" />
            </form>
        </section>
    </div>
    <section id="riwayat-cerc" class="mp-card mp-settings-card">
        <div class="mp-card-header"><div><h2 class="mp-card-title">Riwayat Aktivitas</h2><p class="mp-settings-description">Seluruh aktivitas terkait Koordinator CERC akan tertampil</p></div>
            @if($history->total())<form method="POST" action="{{ route('eoffice.manprak.admin.cerc.history.destroy') }}" x-data="{confirmOpen:false,confirmed:false,submitting:false}" x-ref="actionForm" @submit="if(submitting){$event.preventDefault();return}if(!confirmed){$event.preventDefault();confirmOpen=true}else{submitting=true}">
                @csrf @method('DELETE')<input type="hidden" name="before_id" value="{{ $historyLatestId }}"><button class="mp-delete-button mp-btn destructive sm" :disabled="submitting" title="Hapus Riwayat" aria-label="Hapus Riwayat"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button>
                <x-eoffice::manajemen-praktikum.ui.schedule-confirmation title="Hapus Riwayat" message="Hapus seluruh riwayat saat ini dari daftar? Aktivitas baru akan tetap dicatat." buttonLabel="Ya, Hapus" />
            </form>@endif
        </div>
        <div class="mp-data-scroll"><table class="mp-table mp-settings-table" style="min-width:1100px;table-layout:fixed">
            <colgroup><col style="width:6%"><col style="width:17%"><col style="width:11%"><col style="width:22%"><col style="width:22%"><col style="width:22%"></colgroup>
            <thead><tr><th class="mp-th mp-settings-no">NO</th><th class="mp-th">WAKTU</th><th class="mp-th">AKTIVITAS</th><th class="mp-th">SEBELUM</th><th class="mp-th">SESUDAH</th><th class="mp-th">OLEH</th></tr></thead>
            <tbody>
                @forelse($history as $entry)
                    @php
                        $oldIds=$entry->old_data['user_ids']??[];
                        $newId=$entry->new_data['user_id']??null;
                        $beforeUsers=collect($entry->old_data['users']??collect($oldIds)->map(fn($id)=>$historyUsers->get($id)?->only(['id','name','email'])??['name'=>'Akun tidak tersedia'])->all());
                        $afterUser=$entry->new_data['user']??$historyUsers->get($newId)?->only(['id','name','email']);
                        $isActivity=$entry->action==='CERC_ACTIVITY';
                        $activity=$isActivity?$entry->description:($newId===null?'Pencabutan':(empty($oldIds)?'Penunjukan':((string)($oldIds[0]??'')===(string)$newId?'Pembaruan':'Pergantian')));
                    @endphp
                    <tr class="mp-tr">
                        <td class="mp-settings-no">{{ $history->firstItem()+$loop->index }}</td>
                        <td style="color:var(--c-fg-muted);white-space:nowrap">{{ $entry->created_at?->locale('id')->translatedFormat('d M Y, H:i') ?? '—' }}</td>
                        <td style="overflow-wrap:anywhere">@if($isActivity)<div class="text-[13px] font-semibold">{{ $activity }}</div><div class="mp-settings-email">{{ $entry->new_data['context'] ?? '' }}</div>@else<span class="mp-badge {{ $activity==='Pencabutan'?'neutral':'primary' }} sm">{{ $activity }}</span>@endif</td>
                        @if($isActivity)
                        <td style="overflow-wrap:anywhere">@forelse($entry->old_data['summary']??[] as $summary)<div class="mp-settings-email">{{ $summary }}</div>@empty<span class="mp-settings-email">—</span>@endforelse</td>
                        <td style="overflow-wrap:anywhere">@forelse($entry->new_data['summary']??[] as $summary)<div class="mp-settings-email">{{ $summary }}</div>@empty<span class="mp-settings-email">—</span>@endforelse</td>
                        @else
                        <td>@forelse($beforeUsers as $previous)@php $previousProfile=(object)array_merge(['name'=>'Akun tidak tersedia','email'=>''],$previous,['avatar_url'=>$historyUsers->get($previous['id']??null)?->avatar_url]); @endphp<x-eoffice::manajemen-praktikum.ui.staff-person :user="$previousProfile" :show-email="true" :wrap="true" /> @empty<span class="mp-settings-email">Belum ditunjuk</span>@endforelse</td>
                        <td>@if($newId!==null)@php $afterProfile=(object)array_merge(['name'=>'Akun tidak tersedia','email'=>''],$afterUser??[],['avatar_url'=>$historyUsers->get($newId)?->avatar_url]); @endphp<x-eoffice::manajemen-praktikum.ui.staff-person :user="$afterProfile" :show-email="true" :wrap="true" /> @else<span class="mp-settings-email">Tidak ditunjuk</span>@endif</td>
                        @endif
                        <td>@if($entry->user)<x-eoffice::manajemen-praktikum.ui.staff-person :user="$entry->user" :show-email="true" :wrap="true" />@else<span class="mp-settings-email">Akun tidak tersedia</span>@endif</td>

                    </tr>
                @empty<tr><td colspan="6" class="mp-settings-empty">Belum ada riwayat aktivitas.</td></tr>@endforelse
            </tbody>
        </table></div>
        <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$history" :options="[5,10,20]" fragment="riwayat-cerc" />
    </section>
    </div>
</x-eoffice::manajemen-praktikum.layout>
