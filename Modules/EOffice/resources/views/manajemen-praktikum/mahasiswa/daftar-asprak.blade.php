<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Aktif">

<div class="mp-page-header" style="margin-bottom: 24px;">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pendaftaran Aktif</h1>
            <span class="mp-badge warning sm"><span class="dot"></span>Mahasiswa</span>
        </div>
        <p class="mp-page-sub">Daftarkan diri sebagai calon koordinator atau asisten</p>
    </div>
</div>

<div class="mp-card" style="padding: 24px;">
@if($praktikumDenganPeriode->isEmpty())
<div class="flex-1 flex items-center justify-center" style="min-height:300px;">
    <div style="padding:48px;text-align:center;">
        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 14px;display:block;">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
        <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Saat ini pendaftaran belum dibuka</div>
        <div style="font-size:13px;color:#666D80;">Admin belum membuka periode pendaftaran asisten praktikum atau koordinator saat ini.</div>
    </div>
</div>
@else
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(100%, 320px), 1fr)); gap:24px;">
    @foreach($praktikumDenganPeriode as $p)
        @php
            $pAsprak = $periodeAktif[$p->id]['asprak'] ?? null;
            $pKoor   = $periodeAktif[$p->id]['koor']   ?? null;
            
            $existingAsprak = $pendaftaranAsprakByPraktikum->get($p->id);
            $existingKoor = $pendaftaranKoorByPraktikum->get($p->id);
            $roles = $rolesByPraktikum->get($p->id, collect());
            $isAsprakDiPraktikumIni = $roles->contains('role', 'asprak');
            $isKoorDiPraktikumIni = $roles->contains('role', 'koor');
        @endphp

        {{-- KARTU ASISTEN PRAKTIKUM --}}
        @if($pAsprak)
        <div x-data="{ open: false }" style="display:flex; flex-direction:column; background:#fff; border:1px solid #E5E7EB; border-radius:12px; overflow:hidden; transition:all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
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
                

                {{-- Title --}}
                <h3 style="font-size:16px; font-weight:700; color:#111827; margin:0 0 8px 0; line-height:1.4;">
                    {{ $pAsprak->judul ?: 'Pendaftaran Asisten ' . $p->nama }}
                </h3>

                {{-- Subtitle / Details --}}
                <div style="font-size:13px; color:#6B7280; line-height:1.6; margin-bottom: 16px;">
                    
                    <div style="display: flex; gap: 8px;">
                        <span style="font-weight: 500; min-width: 90px; color:#4B5563;">Tahun Ajaran</span>: {{ $p->tahun_ajaran ?? '-' }}
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <span style="font-weight: 500; min-width: 90px; color:#4B5563;">Dosen</span>: @if($p->dosens->count() > 0) {{ $p->dosens->first()->name }} @else - @endif
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <span style="font-weight: 500; min-width: 90px; color:#4B5563;">Dibuka Pada</span>: 
                        {{ $pAsprak->dibuka_pada ? $pAsprak->dibuka_pada->format('d M Y H:i') : '-' }} - {{ $pAsprak->ditutup_pada ? $pAsprak->ditutup_pada->format('d M Y H:i') : '-' }}
                    </div>
                </div>

                <button type="button" @click="open = true" class="mp-btn primary md w-full mt-auto" style="display: flex; justify-content: center;">
                    Lihat Pendaftaran
                </button>
            </div>

            {{-- Modal Form Asprak --}}
            <div x-show="open" style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300 p-4" x-cloak>
                
                    <div class="relative bg-white rounded-xl shadow-2xl w-[600px] max-w-[90%] max-h-[90vh] overflow-y-auto" style="border:1px solid #E5E7EB;" @click.away="open = false" x-show="open"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        
                        <div class="mp-card-header flex justify-between items-center" style="flex-shrink:0;">
                            <span class="mp-card-title">{{ $pAsprak->judul ?: "Pendaftaran Asisten Praktikum - " . $p->nama }}</span>
                            <button @click="open = false" style="background:none; border:none; cursor:pointer; color:#666D80;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                        </div>
                        
                        <div class="overflow-y-auto" style="padding:24px;">
                            @if($isAsprakDiPraktikumIni)
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span style="font-weight:600;color:#10B981;">Anda sudah aktif sebagai Asisten Praktikum</span>
                                </div>
                                <div style="font-size:13px;color:#666D80;margin-top:4px;">Anda terdaftar dan aktif sebagai asisten di praktikum ini.</div>
                            @elseif($existingAsprak && in_array($existingAsprak->status, ['pending','approved']))
                                <div style="font-size:14px;color:#666D80;margin-bottom:12px;font-weight:600;">{{ $pAsprak->judul ?: 'Status Pendaftaran' }}</div>
                                @if($existingAsprak->status === 'pending')
                                    <span class="mp-badge warning sm"><span class="dot"></span>Menunggu seleksi koordinator</span>
                                    <div style="font-size:13px;color:#666D80;margin-top:8px;">Pendaftaran Anda telah dikirim. Tunggu review dari Koordinator Praktikum.</div>
                                @else
                                    <span class="mp-badge success sm"><span class="dot"></span>Diterima!</span>
                                    <div style="font-size:13px;color:#666D80;margin-top:8px;">Selamat! Anda telah diterima sebagai asisten praktikum.</div>
                                @endif
                            @else
                                <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.store') }}" enctype="multipart/form-data" onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = 'Mengirim...';">
                                    @csrf
                                    <input type="hidden" name="praktikum_id" value="{{ $p->id }}">

                                    @if($pAsprak->judul || $pAsprak->deskripsi)
                                    <div style="background: #F6F8FA; border: 1px solid #DFE1E7; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                                        @if($pAsprak->judul)
                                        <h4 style="margin: 0 0 8px 0; font-size: 14px; color: #0D0D12;">{{ $pAsprak->judul }}</h4>
                                        @endif
                                        @if($pAsprak->deskripsi)
                                        <p style="margin: 0; font-size: 13px; color: #353849; white-space: pre-wrap;">{{ $pAsprak->deskripsi }}</p>
                                        @endif
                                    </div>
                                    @endif

                                    <div style="margin-bottom: 16px;">
                                        <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:6px;">IPK <span style="color:#DF1C41;">*</span></label>
                                        <input type="number" name="ipk" step="0.01" min="0" max="4" required placeholder="3.50" class="mp-input w-full">
                                    </div>

                                    

                                    <div style="margin-bottom: 16px;">
                                        <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:6px;">Transkrip Nilai <span style="color:#DF1C41;">*</span> <span style="font-weight:400;color:#666D80;font-size:11px;">(.pdf maks 5 MB)</span></label>
                                        <div x-data="{ fileName: '' }">
                                        <input type="file" name="transkrip" accept=".pdf" required class="hidden" x-ref="fileInput" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                        <div x-show="!fileName" @click="$refs.fileInput.click()" class="mp-input w-full flex items-center bg-white cursor-pointer text-[13px] text-[#6B7280]">
                                            Choose File No file chosen
                                        </div>
                                        <div x-show="fileName" style="display:none;" class="mp-input w-full flex items-center justify-between bg-[#F8FAFC] text-[13px] border-[#DFE1E7]">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-[#828896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                <span class="text-[#0B266E]" x-text="fileName"></span>
                                            </div>
                                            <button type="button" @click="$refs.fileInput.value = ''; fileName = ''" class="text-[#DF1C41] p-[2px] transition-colors rounded">
                                                <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    </div>

                                    <div style="margin-bottom: 16px;">
                                        <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:6px;">Keanggotaan CERC <span style="font-weight:400;color:#666D80;font-size:11px;">(Akan menjadi nilai tambah, .pdf maks 5 MB)</span></label>
                                        <div x-data="{ fileName: '' }">
                                        <input type="file" name="berkas_cerc" accept=".pdf,.jpg,.jpeg,.png,.csv,.xlsx" None class="hidden" x-ref="fileInput" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                        <div x-show="!fileName" @click="$refs.fileInput.click()" class="mp-input w-full flex items-center bg-white cursor-pointer text-[13px] text-[#6B7280]">
                                            Choose File No file chosen
                                        </div>
                                        <div x-show="fileName" style="display:none;" class="mp-input w-full flex items-center justify-between bg-[#F8FAFC] text-[13px] border-[#DFE1E7]">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-[#828896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                <span class="text-[#0B266E]" x-text="fileName"></span>
                                            </div>
                                            <button type="button" @click="$refs.fileInput.value = ''; fileName = ''" class="text-[#DF1C41] p-[2px] transition-colors rounded">
                                                <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    </div>

                                    <div style="margin-bottom: 24px;">
                                        <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:8px;">Jadwal <span style="color:#DF1C41;">*</span> <span style="font-weight:400;color:#666D80;font-size:11px;">(Pilih hari luang)</span></label>
                                        <div style="display:flex;flex-wrap:wrap;gap:12px;">
                                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                                <div style="position:relative; width:20px; height:20px; border-radius:6px; border:1px solid #DFE1E7; display:flex; align-items:center; justify-content:center; background:#fff; transition:all 0.2s;">
                                                    <input type="checkbox" name="jadwal[]" value="{{ $hari }}" style="position:absolute; inset:0; opacity:0; cursor:pointer;" onchange="this.parentElement.style.background = this.checked ? '#2A3E7B' : '#fff'; this.parentElement.style.borderColor = this.checked ? '#2A3E7B' : '#DFE1E7'; this.nextElementSibling.style.display = this.checked ? 'block' : 'none'">
                                                    <svg style="display:none; pointer-events:none; width:14px; height:14px; color:#fff;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <span style="font-size:13px;color:#353849;">{{ $hari }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    @if($pAsprak->nama_berkas_tambahan)
                                    <div style="margin-bottom: 24px;">
                                        <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:6px;">{{ $pAsprak->nama_berkas_tambahan }} <span style="color:#DF1C41;">*</span> <span style="font-weight:400;color:#666D80;font-size:11px;">(@if($pAsprak->keterangan_berkas_tambahan){{ $pAsprak->keterangan_berkas_tambahan }}, @endif.pdf maks 5 MB)</span></label>
                                        <div x-data="{ fileName: '' }">
                                        <input type="file" name="berkas_tambahan" accept=".pdf,.jpg,.jpeg,.png" required class="hidden" x-ref="fileInput" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                        <div x-show="!fileName" @click="$refs.fileInput.click()" class="mp-input w-full flex items-center bg-white cursor-pointer text-[13px] text-[#6B7280]">
                                            Choose File No file chosen
                                        </div>
                                        <div x-show="fileName" style="display:none;" class="mp-input w-full flex items-center justify-between bg-[#F8FAFC] text-[13px] border-[#DFE1E7]">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-[#828896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                <span class="text-[#0B266E]" x-text="fileName"></span>
                                            </div>
                                            <button type="button" @click="$refs.fileInput.value = ''; fileName = ''" class="text-[#DF1C41] p-[2px] transition-colors rounded">
                                                <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    </div>
                                    @endif

                                    <button type="submit" class="mp-btn primary md w-full" style="justify-content:center;">
                                        Kirim Pendaftaran Asisten
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif


        {{-- KARTU KOORDINATOR --}}
        @if($pKoor)
        <div x-data="{ open: false }" style="display:flex; flex-direction:column; background:#fff; border:1px solid #E5E7EB; border-radius:12px; overflow:hidden; transition:all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
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
                

                {{-- Title --}}
                <h3 style="font-size:16px; font-weight:700; color:#111827; margin:0 0 8px 0; line-height:1.4;">
                    {{ $pKoor->judul ?: 'Pendaftaran Koor ' . $p->nama }}
                </h3>

                {{-- Subtitle / Details --}}
                <div style="font-size:13px; color:#6B7280; line-height:1.6; margin-bottom: 16px;">
                    
                    <div style="display: flex; gap: 8px;">
                        <span style="font-weight: 500; min-width: 90px; color:#4B5563;">Tahun Ajaran</span>: {{ $p->tahun_ajaran ?? '-' }}
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <span style="font-weight: 500; min-width: 90px; color:#4B5563;">Dosen</span>: @if($p->dosens->count() > 0) {{ $p->dosens->first()->name }} @else - @endif
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <span style="font-weight: 500; min-width: 90px; color:#4B5563;">Dibuka Pada</span>: 
                        {{ $pKoor->dibuka_pada ? $pKoor->dibuka_pada->format('d M Y H:i') : '-' }} - {{ $pKoor->ditutup_pada ? $pKoor->ditutup_pada->format('d M Y H:i') : '-' }}
                    </div>
                </div>

                <button type="button" @click="open = true" class="mp-btn navy md w-full mt-auto" style="display: flex; justify-content: center;">
                    Lihat Pendaftaran
                </button>
            </div>

            {{-- Modal Form Koor --}}
            <div x-show="open" style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300 p-4" x-cloak>
                
                    <div class="relative bg-white rounded-xl shadow-2xl w-[600px] max-w-[90%] max-h-[90vh] overflow-y-auto" style="border:1px solid #E5E7EB;" @click.away="open = false" x-show="open"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        
                        <div class="mp-card-header flex justify-between items-center" style="flex-shrink:0;">
                            <span class="mp-card-title">{{ $pKoor->judul ?: "Pendaftaran Koordinator - " . $p->nama }}</span>
                            <button @click="open = false" style="background:none; border:none; cursor:pointer; color:#666D80;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                        </div>
                        
                        <div class="overflow-y-auto" style="padding:24px;">
                            @if($isKoorDiPraktikumIni)
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span style="font-weight:600;color:#0B266E;">Anda sudah aktif sebagai Koordinator</span>
                                </div>
                                <div style="font-size:13px;color:#666D80;margin-top:4px;">Anda terdaftar dan aktif sebagai koordinator di praktikum ini.</div>
                            @elseif($existingKoor && in_array($existingKoor->status, ['pending','approved']))
                                <div style="font-size:14px;color:#666D80;margin-bottom:12px;font-weight:600;">{{ $pKoor->judul ?: 'Status Pendaftaran' }}</div>
                                @if($existingKoor->status === 'pending')
                                    @php
                                        $reviewStatus = $existingKoor->status_dosen === 'disetujui' 
                                            ? 'Disetujui dosen, menunggu approval admin' 
                                            : 'Menunggu review dosen';
                                    @endphp
                                    <span class="mp-badge warning sm"><span class="dot"></span>{{ $reviewStatus }}</span>
                                    <div style="font-size:13px;color:#666D80;margin-top:8px;">Pendaftaran Anda sedang dalam proses review.</div>
                                @else
                                    <span class="mp-badge navy sm"><span class="dot"></span>Diterima!</span>
                                    <div style="font-size:13px;color:#666D80;margin-top:8px;">Selamat! Anda telah diterima sebagai koordinator praktikum.</div>
                                @endif
                            @else
                                <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.daftar-koor.store') }}" enctype="multipart/form-data" onsubmit="const btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = 'Mengirim...';">
                                    @csrf
                                    <input type="hidden" name="praktikum_id" value="{{ $p->id }}">

                                    @if($pKoor->judul || $pKoor->deskripsi)
                                    <div style="background: #F6F8FA; border: 1px solid #DFE1E7; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                                        @if($pKoor->judul)
                                        <h4 style="margin: 0 0 8px 0; font-size: 14px; color: #0D0D12;">{{ $pKoor->judul }}</h4>
                                        @endif
                                        @if($pKoor->deskripsi)
                                        <p style="margin: 0; font-size: 13px; color: #353849; white-space: pre-wrap;">{{ $pKoor->deskripsi }}</p>
                                        @endif
                                    </div>
                                    @endif

                                    <div style="display: flex; gap: 24px; margin-bottom: 16px;">
                                        <div style="flex: 1;">
                                            <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:6px;">Nama Mahasiswa</label>
                                            <input type="text" value="{{ auth()->user()->name }}" class="mp-input w-full" disabled style="background:#F9FAFB; color:#808897;">
                                        </div>
                                        <div style="flex: 1;">
                                            <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:6px;">NIM</label>
                                            <input type="text" value="{{ auth()->user()->username ?? '-' }}" class="mp-input w-full" disabled style="background:#F9FAFB; color:#808897;">
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 24px; margin-bottom: 16px;">
                                        <div style="flex: 1;">
                                            <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:6px;">IPK <span style="color:#DF1C41;">*</span></label>
                                            <input type="number" name="ipk" step="0.01" min="0" max="4" required placeholder="3.60" class="mp-input w-full">
                                        </div>
                                        <div style="flex: 1;">
                                            <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:6px;">Transkrip Nilai <span style="color:#DF1C41;">*</span> <span style="font-weight:400;color:#666D80;font-size:11px;">(.pdf maks 5 MB)</span></label>
                                            <div x-data="{ fileName: '' }">
                                        <input type="file" name="transkrip" accept=".pdf" required class="hidden" x-ref="fileInput" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                        <div x-show="!fileName" @click="$refs.fileInput.click()" class="mp-input w-full flex items-center bg-white cursor-pointer text-[13px] text-[#6B7280]">
                                            Choose File No file chosen
                                        </div>
                                        <div x-show="fileName" style="display:none;" class="mp-input w-full flex items-center justify-between bg-[#F8FAFC] text-[13px] border-[#DFE1E7]">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-[#828896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                <span class="text-[#0B266E]" x-text="fileName"></span>
                                            </div>
                                            <button type="button" @click="$refs.fileInput.value = ''; fileName = ''" class="text-[#DF1C41] p-[2px] transition-colors rounded">
                                                <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                        </div>
                                    </div>

                                    <div style="display: flex; flex-direction:column; gap: 16px; margin-bottom: 24px;">
                                        <div>
                                            <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:6px;">Keanggotaan CERC <span style="font-weight:400;color:#666D80;font-size:11px;">(Akan menjadi nilai tambah, .pdf maks 5 MB)</span></label>
                                            <div x-data="{ fileName: '' }">
                                        <input type="file" name="berkas_cerc" accept=".pdf,.jpg,.jpeg,.png,.csv,.xlsx" None class="hidden" x-ref="fileInput" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                        <div x-show="!fileName" @click="$refs.fileInput.click()" class="mp-input w-full flex items-center bg-white cursor-pointer text-[13px] text-[#6B7280]">
                                            Choose File No file chosen
                                        </div>
                                        <div x-show="fileName" style="display:none;" class="mp-input w-full flex items-center justify-between bg-[#F8FAFC] text-[13px] border-[#DFE1E7]">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-[#828896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                <span class="text-[#0B266E]" x-text="fileName"></span>
                                            </div>
                                            <button type="button" @click="$refs.fileInput.value = ''; fileName = ''" class="text-[#DF1C41] p-[2px] transition-colors rounded">
                                                <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                        </div>
                                        @if($pKoor->nama_berkas_tambahan)
                                        <div>
                                            <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:6px;">{{ $pKoor->nama_berkas_tambahan }} <span style="color:#DF1C41;">*</span> <span style="font-weight:400;color:#666D80;font-size:11px;">(@if($pKoor->keterangan_berkas_tambahan){{ $pKoor->keterangan_berkas_tambahan }}, @endif.pdf maks 5 MB)</span></label>
                                            <div x-data="{ fileName: '' }">
                                        <input type="file" name="berkas_tambahan" accept=".pdf,.jpg,.jpeg,.png" required class="hidden" x-ref="fileInput" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                        <div x-show="!fileName" @click="$refs.fileInput.click()" class="mp-input w-full flex items-center bg-white cursor-pointer text-[13px] text-[#6B7280]">
                                            Choose File No file chosen
                                        </div>
                                        <div x-show="fileName" style="display:none;" class="mp-input w-full flex items-center justify-between bg-[#F8FAFC] text-[13px] border-[#DFE1E7]">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-[#828896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                <span class="text-[#0B266E]" x-text="fileName"></span>
                                            </div>
                                            <button type="button" @click="$refs.fileInput.value = ''; fileName = ''" class="text-[#DF1C41] p-[2px] transition-colors rounded">
                                                <svg class="w-[14px] h-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                        </div>
                                        @endif
                                    </div>

                                    <button type="submit" class="mp-btn navy md w-full" style="justify-content:center;">
                                        Kirim Pendaftaran Koordinator
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    @endforeach
</div>
@endif

</x-eoffice::manajemen-praktikum.layout>
