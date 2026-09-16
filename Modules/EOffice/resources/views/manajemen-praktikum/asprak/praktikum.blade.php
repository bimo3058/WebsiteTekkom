<x-eoffice::manajemen-praktikum.layout pageTitle="Daftar Praktikum">
    <div class="mp-page-header" style="margin-bottom: 24px;">
        <div>
            <h1 class="mp-page-title">Daftar Praktikum</h1>
            <p class="mp-page-sub">Kelola kelas praktikum sebagai Asisten untuk {{ $semesterLabel }}</p>
        </div>
    </div>

    <div class="mp-card" style="padding: 24px;">
        @if(!isset($praktikums) || $praktikums->isEmpty())
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                    stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada praktikum yang diampu.</div>
            </div>
        @else
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:24px;">
                @foreach($praktikums as $p)
                    <a href="{{ route('eoffice.manprak.asprak.praktikum.show', $p->id) }}" 
                       style="display:flex; flex-direction:column; background:#fff; border:1px solid #E5E7EB; border-radius:12px; overflow:hidden; text-decoration:none; transition:all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05);"
                       onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 20px -5px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)';">
                        
                        {{-- Image Cover --}}
                        <div style="height:160px; background-color:#F3F4F6; display:flex; align-items:center; justify-content:center; border-bottom:1px solid #E5E7EB;">
                            @if($p->cover_path)
                                <img src="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->cover_path, 'eoffice') }}" alt="{{ $p->nama }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Card Content --}}
                        <div style="padding:20px; display:flex; flex-direction:column; flex:1;">
                            {{-- Badges --}}
                            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px;">
                                @if($p->status === 'aktif')
                                    <span style="font-size:11px; font-weight:600; background:#F1F5F9; color:#475569; padding:4px 10px; border-radius:6px;">Aktif</span>
                                @else
                                    <span style="font-size:11px; font-weight:600; background:#F1F5F9; color:#64748B; padding:4px 10px; border-radius:6px;">Tutup</span>
                                @endif
                                <span style="font-size:11px; font-weight:600; background:#EFF6FF; color:#2563EB; padding:4px 10px; border-radius:6px;">Semester {{ $p->semester ?? 'Genap' }}</span>
                            </div>

                            {{-- Title --}}
                            <h3 style="font-size:16px; font-weight:700; color:#111827; margin:0 0 3px 0; line-height:1.4;">{{ $p->nama }}</h3>

                            {{-- Subtitle / Details --}}
                            <p style="font-size:13px; color:#6B7280; margin:0; line-height:1.5;">
                                Tahun Ajaran: {{ $p->tahun_ajaran ?? '-' }}
                                <br>
                                Dosen: @if($p->dosens->count() > 0) {{ $p->dosens->first()->name }} @else - @endif
                                <br>
                                {{ $p->daftar_praktikan_count ?? 0 }} Praktikan &bull; {{ $p->asprak_praktikum_count ?? 0 }} Asisten
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-eoffice::manajemen-praktikum.layout>
