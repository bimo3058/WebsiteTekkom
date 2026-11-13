{{--
    Riwayat catatan tinjauan (S-82), tertutup secara bawaan. Dipakai _tinjauan: di dalam kotak
    status (catatan yang sudah diperbaiki) atau berdiri sendiri setelah tahap tinjau lewat.

    Variabel: $riwayat (Collection KegiatanCatatan, muat penulis & pemerbaiki).
--}}
<details class="mk-tinjau-riwayat">
    <summary>
        <svg class="mk-tinjau-riwayat__panah" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
        Riwayat catatan ({{ $riwayat->count() }})
    </summary>
    @foreach($riwayat->sortByDesc('created_at') as $c)
        <div @class(['mk-tinjau-riwayat__item', 'is-terbuka' => $c->terbuka])>
            <div class="mk-tinjau-riwayat__kepala">
                {{ $c->created_at->locale('id')->translatedFormat('j M Y') }} · {{ $c->penulis?->name ?? '-' }}
                @if($c->terbuka)
                    · <span class="mk-tinjau-riwayat__belum">belum diperbaiki</span>
                @else
                    · dikirim ulang{{ $c->pemerbaiki ? ' oleh ' . $c->pemerbaiki->name : '' }}, {{ $c->diperbaiki_at->locale('id')->translatedFormat('j M Y') }}
                @endif
            </div>
            <div class="mk-tinjau-riwayat__isi">{{ $c->isi }}</div>
        </div>
    @endforeach
</details>

@once
<style>
    .mk-tinjau-riwayat summary { display: inline-flex; align-items: center; gap: 4px; margin-top: 2px; font-size: 12.5px; font-weight: 600;
        color: var(--c-fg-sec); cursor: pointer; list-style: none; user-select: none; }
    .mk-tinjau-riwayat summary::-webkit-details-marker { display: none; }
    .mk-tinjau-riwayat summary:hover { color: var(--c-primary); }
    .mk-tinjau-riwayat summary:focus-visible { outline: 2px solid var(--c-primary); outline-offset: 2px; border-radius: 4px; }
    .mk-tinjau-riwayat__panah { transition: transform .15s; }
    .mk-tinjau-riwayat[open] .mk-tinjau-riwayat__panah { transform: rotate(90deg); }
    .mk-tinjau-riwayat__item { margin-top: 8px; padding: 2px 0 2px 12px; border-left: 2px solid var(--c-border); }
    .mk-tinjau-riwayat__item.is-terbuka { border-left-color: var(--c-error); }
    .mk-tinjau-riwayat__kepala { font-size: 12px; font-weight: 600; color: var(--c-fg-sec); }
    .mk-tinjau-riwayat__belum { color: var(--c-error); }
    .mk-tinjau-riwayat__isi { font-size: 12.5px; line-height: 1.5; color: var(--c-fg-muted); white-space: pre-line; overflow-wrap: anywhere; }
</style>
@endonce
