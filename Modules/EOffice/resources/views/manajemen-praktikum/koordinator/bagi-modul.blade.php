<x-eoffice::manajemen-praktikum.layout pageTitle="Pembagian Modul ke Asisten" :breadcrumbPraktikum="$praktikum">

    @php
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\AsistenPraktikum[] $asistenList */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Modul[] $modulList */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\ModulAsprak[] $distribusiList */
    @endphp
    {{-- Page Header --}}
    <div class="mp-page-header">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">Pembagian Modul ke Asisten</h1>
            </div>
            <p class="mp-page-sub">Tentukan asisten mana yang mengelola modul mana ·
                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
        <a href="{{ route('eoffice.manprak.koor.modul.index') }}" class="mp-btn secondary md" style="text-decoration:none;color:#0B266E;">Kembali</a>
    </div>

    <div class="sec-head">
        <span class="sec-bar"></span>
        <span class="sec-title">Distribusi Asisten & Modul</span>
        <span class="sec-rule"></span>
    </div>

    <div class="flex gap-3.5 flex-1 min-h-0">

        {{-- Panel Kiri: Daftar Asisten --}}
        <div class="mp-card shrink-0" style="width:280px;">
            <div class="mp-card-header">
                <div>
                    <span class="mp-card-title">Asisten</span>
                    <div style="font-size:12px;color:#666D80;margin-top:2px;">{{ $asistenList->count() }} asisten
                        terdaftar</div>
                </div>
            </div>
            <div class="overflow-y-auto flex-1">
                @forelse($asistenList ?? [] as $a)
                    <div style="padding:10px 20px;border-bottom:1px solid #DFE1E7;">
                        <div class="flex items-center gap-2.5">
                            <div class="mp-av green">{{ strtoupper(substr($a->user?->name ?? 'A', 0, 2)) }}</div>
                            <div class="min-w-0">
                                <div style="font-size:12px;font-weight:600;color:#0D0D12;" class="truncate">
                                    {{ $a->user?->name ?? '—' }}</div>
                                <div style="font-size:10px;color:#666D80;">{{ $a->modulAsprak->count() }} modul</div>
                            </div>
                        </div>

                    </div>
                @empty
                    <div style="padding:48px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                            stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                        </svg>
                        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada asprak.</div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Panel Kanan --}}
        <div class="flex flex-col gap-3.5 flex-1 min-w-0 overflow-y-auto">

            {{-- Form Assign --}}
            @if(isset($praktikum) && $praktikum->is_active)
                <div class="mp-card shrink-0" style="padding:20px;">
                    <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Assign Asisten
                        ke Modul</div>
                    <form method="POST" action="{{ route('eoffice.manprak.koor.bagi-modul.store') }}">
                        @csrf
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Asisten <span
                                        class="text-red-500">*</span></label>
                                @php
                                    $asistenOptions = [];
                                    foreach ($asistenList ?? [] as $a) {
                                        $asistenOptions[] = ['value' => (string) $a->id, 'label' => $a->user?->name ?? 'Asisten'];
                                    }
                                @endphp
                                <x-eoffice::manajemen-praktikum.ui.select name="asprak_id" :options="$asistenOptions"
                                    :selected="(string) old('asprak_id', '')" placeholder="Pilih Asisten..." minWidth="200px" />
                            </div>
                            <div>
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Modul <span
                                        class="text-red-500">*</span></label>
                                @php
                                    $modulOptions = [];
                                    if (isset($modulList)) {
                                        foreach ($modulList as $m) {
                                            $modulOptions[] = ['value' => (string) $m->id, 'label' => $m->nama];
                                        }
                                    }
                                @endphp
                                <x-eoffice::manajemen-praktikum.ui.select name="modul_id" :options="$modulOptions"
                                    :selected="(string) old('modul_id', request('modul_id', (isset($modul) ? $modul?->id : '')))"
                                    placeholder="Pilih Modul..." minWidth="200px" />
                            </div>
                        </div>
                        <div style="display:flex;justify-content:flex-end;"><button type="submit" class="mp-btn primary md" style="font-size:13px;padding:10px 16px;background:#0B266E;color:#FFFFFF;">Assign Modul</button></div>
                    </form>
                </div>
            @endif

            {{-- Tabel Distribusi Saat Ini --}}
            <div class="mp-card flex-1 min-h-0">
                <div class="mp-card-header" style="flex-shrink:0;">
                    <span class="mp-card-title">Distribusi Saat Ini</span>

                </div>
                <div style="flex-shrink:0;">
                    <table class="mp-table" style="width:100%;">
                        <thead>
                            <tr style="background:#F9FAFB;">
                                <th class="mp-th text-left" style="padding:10px 16px;">Modul</th>
                                <th class="mp-th text-left" style="padding:10px 16px;width:200px;">Asisten
                                </th>
                                <th class="mp-th text-left" style="padding:10px 16px;width:80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($distribusiList ?? [] as $d)
                                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                                    <td style="padding:12px 16px;">
                                        <div style="font-size:13px;font-weight:500;color:#0D0D12;">
                                            {{ $d->modul?->nama ?? '—' }}</div>
                                    </td>
                                    <td style="padding:12px 16px;">
                                        <div class="flex items-center gap-2">
                                            <div class="mp-av green" style="width:24px;height:24px;font-size:9px;">
                                                {{ strtoupper(substr($d->asprak?->user?->name ?? 'A', 0, 2)) }}</div>
                                            <span
                                                style="font-size:12px;color:#353849;">{{ $d->asprak?->user?->name ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td style="padding:12px 16px;">
                                        @if(isset($praktikum) && $praktikum->is_active)
                                            <form method="POST"
                                                action="{{ route('eoffice.manprak.koor.bagi-modul.destroy', $d->id) }}"
                                                onsubmit="return confirm('Hapus distribusi ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="mp-btn destructive sm">Hapus</button>
                                            </form>
                                        @else
                                            <span style="font-size:11px;color:#808897;">Read-only</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        <div style="padding:48px;text-align:center;">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8"
                                                stroke-width="1.5" stroke-linecap="round"
                                                style="margin:0 auto 12px;display:block;">
                                                <circle cx="12" cy="12" r="10" />
                                                <line x1="8" y1="12" x2="16" y2="12" />
                                            </svg>
                                            <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada distribusi
                                                modul.</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</x-eoffice::manajemen-praktikum.layout>
