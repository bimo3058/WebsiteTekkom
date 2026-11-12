<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$daftarPraktikan?->praktikum" pageTitle="Tugas">
    @php
        $archiveReadOnly = $daftarPraktikan && !$daftarPraktikan->praktikum->is_active;
    @endphp

    @if(!$daftarPraktikan)
        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Tugas Praktikum</h1>
            </div>
        </div>
        <div class="mp-alert warning flex-shrink-0">Anda belum terdaftar di praktikum manapun.</div>
    @else
        <x-eoffice::manajemen-praktikum.mhs-header :praktikum="$daftarPraktikan->praktikum" />
    @endif

    
    {{-- CSS untuk Accordion --}}
    <style>
</style>

    <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 4px;">
        @forelse($modulList as $item)
            @php 
                $modul = $item['modul']; 
                $tugasList = $item['tugas'];
            @endphp
            
            <div x-data="{ modulOpen: @js($tugasList->contains(fn($task)=>(string)$task->id===(string)request('tugas_id'))) }" style="background: #fff; border: 1px solid var(--c-border); border-radius: 8px; box-shadow:none; overflow: hidden;">
                
                {{-- Card Header: Modul Info --}}
                <div class="mp-module-card-header" @click="modulOpen = !modulOpen" :style="{boxShadow:modulOpen ? 'inset 0 -1px 0 var(--c-border)' : 'none'}" style="padding: 16px 24px; background: #fff; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                    <div>
                        <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">{{ $modul->nama }}</h3>
                        <div style="font-size:13px; color:#666D80; font-weight:400; margin-bottom:4px;">
                            Asisten: 
                            @if($item['asprak']->isNotEmpty())
                                {{ $item['asprak']->join(', ') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="modulOpen ? 'transform: rotate(180deg); transition: transform 0.2s;' : 'transform: rotate(0deg); transition: transform 0.2s;'">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                {{-- List of Tugas as Accordions --}}
                <x-eoffice::manajemen-praktikum.ui.grade-accordion state="modulOpen">
                    <div style="display: flex; flex-direction: column;">
                    @php
                        $requiredTypes = $praktikum->moduleTaskTypes();
                    @endphp

                    @foreach($requiredTypes as $type => $label)
                        @php
                            $t = $tugasList->firstWhere('jenis_tugas', $type);
                            
                            $dlAC  = $t && $t->deadline ? \Carbon\Carbon::parse($t->deadline) : null;
                            $dlACC = $t && $t->deadline_acc ? \Carbon\Carbon::parse($t->deadline_acc) : null;
                            
                            $lewat = $dlAC && now()->gt($dlAC);
                            $lewatMutlak = ($dlACC ?? $dlAC) && now()->gt($dlACC ?? $dlAC);
                            
                            $sisaAC = $dlAC ? now()->diffInDays($dlAC, false) : null;

                            $pengumpulan = $t ? $t->pengumpulan : null;
                            $sudahKumpul = !is_null($pengumpulan);
                            $statusTugas = $pengumpulan?->status_pengumpulan ?? 'belum_kumpul';
                            $submittedFiles = collect($pengumpulan?->files ?? [])->map(function ($file) {
                                $path = is_array($file) ? ($file['path'] ?? '') : $file;
                                return [
                                    'path' => $path,
                                    'name' => is_array($file) && isset($file['original_name'])
                                        ? $file['original_name']
                                        : pathinfo($path, PATHINFO_BASENAME),
                                ];
                            })->filter(fn ($file) => $file['path'])->values();

                            $isLate = false;
                            if ($sudahKumpul && $pengumpulan->riwayat) {
                                $firstSubmission = $pengumpulan->riwayat->sortBy('created_at')->first();
                                if ($firstSubmission && $dlAC && \Carbon\Carbon::parse($firstSubmission->created_at)->gt($dlAC)) {
                                    $isLate = true;
                                }
                            }

                            $oldFiles = [];
                            if ($t && $t->file_path) {
                                $oldFiles = json_decode($t->file_path, true) ?? [];
                                if (!is_array($oldFiles)) $oldFiles = [$t->file_path];
                            }
                        @endphp
                        
                        <div id="{{ $t ? 'tugas-'.$t->id : 'jenis-'.$modul->id.'-'.$type }}" x-data="{ open: @js($t && (string)$t->id===(string)request('tugas_id')), showUpload: false }" x-init="if(open)$nextTick(()=>requestAnimationFrame(()=>$el.scrollIntoView({behavior:'smooth',block:'center'})))" style="scroll-margin-top:24px;{{ $loop->last ? '' : 'border-bottom:1px solid var(--c-border);' }}">
                            
                            {{-- Accordion Header --}}
                            <div @click="open = !open" style="padding: 14px 24px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: #fff; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                    <div style="font-size: 14px; font-weight: 600; color: #0D0D12;">{{ $label }}</div>
                                    @if($t)
                                        @if($statusTugas === 'acc')
                                            <span class="mp-badge success sm"><span class="dot"></span>ACC @if($pengumpulan?->nilai !== null)<span>Nilai: {{ str_replace('.', ',', (string)$pengumpulan->nilai) }}</span>@endif</span>
                                        @elseif($statusTugas === 'revisi')
                                            <span class="mp-badge error sm"><span class="dot"></span>Perlu Revisi</span>
                                        @elseif($statusTugas === 'belum_dicek')
                                            <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Penilaian</span>
                                        @elseif($statusTugas === 'draft')
                                            <span class="mp-badge neutral sm"><span class="dot"></span>Pengiriman Dibatalkan</span>
                                        @elseif($lewatMutlak)
                                            <span class="mp-badge error sm"><span class="dot"></span>Waktu Habis</span>
                                        @elseif($lewat)
                                            <span class="mp-badge warning sm"><span class="dot"></span>AC Terlewat</span>
                                        @elseif($sisaAC !== null && $sisaAC <= 2)
                                            <span class="mp-badge warning sm"><span class="dot"></span>Segera!</span>
                                        @else
                                            <span class="mp-badge neutral sm"><span class="dot"></span>Belum Dikumpul</span>
                                        @endif

                                        @if($isLate)
                                            <span class="mp-badge error sm" style="background:#FFF0F2;color:#DF1C41;border:1px solid #DF1C41;">Terlambat</span>
                                        @endif
                                    @endif
                                </div>
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    @if($t)
                                    <div style="font-size:12px;color:#666D80;display:flex;gap:10px;flex-wrap:wrap;">
                                        @if($dlAC && $dlACC)
                                            <span style="color:{{ $lewatMutlak ? '#A4ABB8' : '#353849' }};">Tenggat AC: {{ $dlAC->format('d/m/Y, H:i') }}</span>
                                            <span style="color:{{ $lewatMutlak ? '#A4ABB8' : '#353849' }};">Tenggat ACC: {{ $dlACC->format('d/m/Y, H:i') }}</span>
                                        @elseif($dlACC)
                                            <span style="color:#A4ABB8;">Tenggat AC: -</span>
                                            <span style="color:{{ $lewatMutlak ? '#A4ABB8' : '#353849' }};">Tenggat ACC: {{ $dlACC->format('d/m/Y, H:i') }}</span>
                                        @elseif($dlAC)
                                            <span style="color:{{ $lewatMutlak ? '#A4ABB8' : '#353849' }};">Tenggat AC: {{ $dlAC->format('d/m/Y, H:i') }}</span>
                                            <span style="color:#A4ABB8;">Tenggat ACC: -</span>
                                        @else
                                            <span style="color:#A4ABB8;">Tanpa batas waktu</span>
                                        @endif
                                    </div>
                                    @endif
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="open ? 'transform: rotate(180deg); transition: transform 0.2s;' : 'transform: rotate(0deg); transition: transform 0.2s;'">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>

                            {{-- Accordion Body --}}
                            <x-eoffice::manajemen-praktikum.ui.grade-accordion state="open">
                                <div style="border-top: 1px solid var(--c-border); padding: 16px 24px; background: #FAFBFC;">
                                    
                                    @if($t)
                                        @if($t->deskripsi)
                                            <div style="font-size: 13px; color: #374151; margin-bottom: 16px; line-height: 1.5; white-space: pre-wrap;">{{ $t->deskripsi }}</div>
                                        @endif
                                        
                                        <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 16px;">
                                            @foreach($oldFiles as $idx => $f)
                                                @php 
                                                    $pathStr = is_array($f) && isset($f['path']) ? $f['path'] : $f;
                                                    $baseName = is_array($f) && isset($f['original_name']) ? $f['original_name'] : pathinfo($pathStr, PATHINFO_BASENAME);
                                                @endphp
                                                <a rel="noopener noreferrer" href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($pathStr, 'eoffice', $baseName) }}"
                                                    target="_blank" title="{{ $baseName }}"
                                                    style="display: flex; flex-direction: column; width: 120px; height: 120px; border: 1px solid #DFE1E7; border-radius: 8px; overflow: hidden; text-decoration: none; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.02); transition:transform 0.15s, box-shadow 0.15s;"
                                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';"
                                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                                    <div style="flex: 1; display: flex; align-items: center; justify-content: center; background: #F9FAFB;">
                                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                            stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                            <polyline points="13 2 13 9 20 9"></polyline>
                                                        </svg>
                                                    </div>
                                                    <div style="background: #0B266E; color: #fff; padding: 8px 10px; font-size: 11px; font-weight: 600; text-align: center; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $baseName }}">
                                                        {{ $baseName }}
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>

                                        <div style="border-top:1px dashed #DFE1E7; margin:16px 0;"></div>
                                        @php
                                            $isInRevisionFlow = $pengumpulan && (in_array($statusTugas, ['revisi']) || $pengumpulan->is_revision);
                                            $firstSub = $pengumpulan ? ($pengumpulan->riwayat->where('is_revision', false)->sortByDesc('created_at')->first() ?? $pengumpulan->riwayat->sortByDesc('created_at')->first()) : null;
                                        @endphp
                                        <div x-data="{
                                            isEditing: {{ !$archiveReadOnly && in_array($statusTugas, ['draft', 'belum_kumpul', 'revisi']) ? 'true' : 'false' }},
                                            retainedFiles: @js($isInRevisionFlow && in_array($statusTugas, ['revisi']) ? [] : $submittedFiles),
                                            files: [],
                                            init() {
                                                // addInput is the trigger; fileInput is the real named input
                                            },
                                            addFiles(event) {
                                                const newFiles = Array.from(event.target.files);
                                                
                                                if (this.retainedFiles.length + this.files.length + newFiles.length > 3) {
                                                    alert('Maksimal 3 file diperbolehkan.');
                                                    event.target.value = '';
                                                    return;
                                                }
                                                
                                                const maxSize = 5 * 1024 * 1024;
                                                for(let i=0; i<newFiles.length; i++) {
                                                    if (newFiles[i].size > maxSize) {
                                                        alert('Ukuran file ' + newFiles[i].name + ' melebihi 5MB.');
                                                        event.target.value = '';
                                                        return;
                                                    }
                                                }
                                                
                                                let dt = new DataTransfer();
                                                this.files.forEach(f => dt.items.add(f));
                                                newFiles.forEach(f => dt.items.add(f));
                                                
                                                // Update the real named input
                                                this.$refs.fileInput.files = dt.files;
                                                // Update reactive list
                                                this.files = Array.from(this.$refs.fileInput.files);
                                                // Only reset addInput (the trigger), NOT fileInput
                                                this.$refs.addInput.value = '';
                                            },
                                            removeFile(index) {
                                                let dt = new DataTransfer();
                                                this.files.splice(index, 1);
                                                this.files.forEach(f => dt.items.add(f));
                                                this.$refs.fileInput.files = dt.files;
                                                this.$refs.addInput.value = '';
                                            },
                                            removeRetainedFile(index) {
                                                this.retainedFiles.splice(index, 1);
                                            }
                                        }">
                                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                                                <h3 style="font-size:14px; font-weight:700; color:#353849;">Pengumpulan Tugas</h3>
                                                @if($sudahKumpul && $pengumpulan)
                                                    <span style="font-size:12px; font-weight:500; color:#666D80;">{{ $statusTugas === 'draft' && !$isInRevisionFlow ? 'Dibatalkan pada' : 'Dikumpulkan pada' }}: {{ \Carbon\Carbon::parse($firstSub ? $firstSub->created_at : $pengumpulan->updated_at)->format('d/m/Y, H:i') }}</span>
                                                @endif
                                            </div>

                                            {{-- File yang Dikumpulkan (view mode) --}}
                                            @if($sudahKumpul && $pengumpulan?->file_path)
                                                <div x-show="!isEditing || {{ $isInRevisionFlow ? 'true' : 'false' }}" class="mb-4">
                                                    <div style="display:flex;flex-direction:column;gap:5px;">
                                                        @php
                                                            $filesToDisplay = $isInRevisionFlow && $firstSub && !empty($firstSub->files) ? $firstSub->files : $pengumpulan->files;
                                                        @endphp
                                                        @if($filesToDisplay)
                                                            @foreach($filesToDisplay as $fileIdx => $pPathObj)
                                                                @php 
                                                                    $pPath = is_array($pPathObj) && isset($pPathObj['path']) ? $pPathObj['path'] : $pPathObj;
                                                                    $pName = is_array($pPathObj) && isset($pPathObj['original_name']) ? $pPathObj['original_name'] : pathinfo($pPath, PATHINFO_BASENAME);
                                                                @endphp
                                                                <a rel="noopener noreferrer" href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($pPath, 'eoffice', $pName) }}"
                                                                    target="_blank"
                                                                    style="display:flex; align-items:center; padding:10px 14px; border:1px solid #DFE1E7; border-radius:8px; background:#fff; text-decoration:none; transition:border-color 0.2s;"
                                                                    onmouseover="this.style.borderColor='#0B266E'" onmouseout="this.style.borderColor='#DFE1E7'">
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-right:8px;">
                                                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                                        <polyline points="13 2 13 9 20 9"></polyline>
                                                                    </svg>
                                                                    <span style="font-size:13px; font-weight:500; color:#0B266E; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $pName }}</span>
                                                                </a>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    
                                                    @if(!$archiveReadOnly && !$isInRevisionFlow && !$lewatMutlak && !in_array($statusTugas, ['acc']))
                                                        <div style="display:flex; justify-content:flex-end; margin-top:12px;">
                                                            @if($statusTugas === 'belum_dicek')
                                                                <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.tugas.batalkan', $t->id) }}">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="mp-btn secondary" style="width:200px; padding:8px 16px; font-size:13px; height:30px; border-radius:8px; justify-content:center; color:#DF1C41; border-color:#DF1C41;">
                                                                        Batalkan Pengiriman
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- File Revisi dari Asisten (View Mode) --}}
                                            @if($pengumpulan?->file_revisi_asprak)
                                                <div style="border-top:1px dashed #DFE1E7; margin:16px 0;"></div>
                                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                                                    <h3 style="font-size:14px; font-weight:700; color:#353849;">Revisi Tugas</h3>
                                                </div>
                                                <div class="mb-4">
                                                    <div style="display:flex;flex-direction:column;gap:5px;">
                                                        @php
                                                            $revStr = $pengumpulan->file_revisi_asprak;
                                                            $revFiles = json_decode($revStr, true);
                                                            if (json_last_error() !== JSON_ERROR_NONE || !is_array($revFiles)) {
                                                                $revFiles = [$revStr];
                                                            }
                                                        @endphp
                                                        @if(is_array($revFiles))
                                                            @foreach($revFiles as $rf)
                                                                @php 
                                                                    $rPath = is_array($rf) ? ($rf['path'] ?? null) : $rf;
                                                                    $rName = is_array($rf) ? ($rf['original_name'] ?? basename($rPath)) : basename($rPath);
                                                                @endphp
                                                                @if($rPath)
                                                                <a rel="noopener noreferrer" href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($rPath, 'eoffice', $rName) }}"
                                                                    target="_blank"
                                                                    style="display:flex; align-items:center; padding:10px 14px; border:1px solid #DFE1E7; border-radius:8px; background:#fff; text-decoration:none; transition:border-color 0.2s;"
                                                                    onmouseover="this.style.borderColor='#0B266E'" onmouseout="this.style.borderColor='#DFE1E7'">
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-right:8px;">
                                                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                                        <polyline points="13 2 13 9 20 9"></polyline>
                                                                    </svg>
                                                                    <span style="font-size:13px; font-weight:500; color:#0B266E; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $rName }}</span>
                                                                </a>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Pengumpulan Revisi Header and View Mode --}}
                                            @if($isInRevisionFlow)
                                                <div style="border-top:1px dashed #DFE1E7; margin:16px 0;"></div>
                                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                                                    <h3 style="font-size:14px; font-weight:700; color:#353849;">Pengumpulan Revisi</h3>
                                                    @if($statusTugas !== 'revisi' && $pengumpulan)
                                                        <span style="font-size:12px; font-weight:500; color:#666D80;">{{ $statusTugas === 'draft' ? 'Dibatalkan pada' : 'Dikumpulkan pada' }}: {{ \Carbon\Carbon::parse($pengumpulan->updated_at)->format('d/m/Y, H:i') }}</span>
                                                    @endif
                                                </div>

                                                @if($statusTugas !== 'revisi' || $archiveReadOnly)
                                                    <div x-show="!isEditing" class="mb-4">
                                                        <div style="display:flex;flex-direction:column;gap:5px;">
                                                            @if($pengumpulan && $pengumpulan->files)
                                                                @foreach($pengumpulan->files as $fileIdx => $pPathObj)
                                                                    @php 
                                                                        $pPath = is_array($pPathObj) && isset($pPathObj['path']) ? $pPathObj['path'] : $pPathObj;
                                                                        $pName = is_array($pPathObj) && isset($pPathObj['original_name']) ? $pPathObj['original_name'] : pathinfo($pPath, PATHINFO_BASENAME);
                                                                    @endphp
                                                                    <a rel="noopener noreferrer" href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($pPath, 'eoffice', $pName) }}"
                                                                        target="_blank"
                                                                        style="display:flex; align-items:center; padding:10px 14px; border:1px solid #DFE1E7; border-radius:8px; background:#fff; text-decoration:none; transition:border-color 0.2s;"
                                                                        onmouseover="this.style.borderColor='#0B266E'" onmouseout="this.style.borderColor='#DFE1E7'">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-right:8px;">
                                                                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                                            <polyline points="13 2 13 9 20 9"></polyline>
                                                                        </svg>
                                                                        <span style="font-size:13px; font-weight:500; color:#0B266E; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $pName }}</span>
                                                                    </a>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                        @if(!$archiveReadOnly && !$lewatMutlak && !in_array($statusTugas, ['acc']))
                                                            <div style="display:flex; justify-content:flex-end; margin-top:12px;">
                                                                @if($statusTugas === 'belum_dicek')
                                                                    <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.tugas.batalkan', $t->id) }}">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="mp-btn secondary" style="width:200px; padding:8px 16px; font-size:13px; height:30px; border-radius:8px; justify-content:center; color:#DF1C41; border-color:#DF1C41;">
                                                                            Batalkan Pengiriman
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif

                                            {{-- Edit mode: existing files + new upload area --}}
                                            @if(!$archiveReadOnly && !$lewatMutlak && !in_array($statusTugas, ['acc']))
                                                <div x-show="isEditing" x-cloak class="mb-4">
                                                    <form method="POST" action="{{ in_array($statusTugas, ['belum_kumpul']) ? route('eoffice.manprak.mahasiswa.tugas.kumpul', $t->id) : route('eoffice.manprak.mahasiswa.tugas.kirim-ulang', $t->id) }}" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="file" name="file[]" multiple x-ref="fileInput" style="display:none;">
                                                        <input type="file" multiple @change="addFiles" x-ref="addInput" style="display:none;">

                                                        <label style="display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:8px;">
                                                            Berkas Lampiran <span style="color:#DF1C41;">*</span>
                                                        </label>
                                                        <button type="button" @click="$refs.addInput.click()" title="Pilih berkas lampiran"
                                                            style="display:flex; width:100%; align-items:center; justify-content:center; gap:8px; border:1px dashed #A4ABB8; background:#fff; color:#0B266E; padding:12px 20px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s;"
                                                            onmouseover="this.style.background='#F9FAFB'; this.style.borderColor='#0B266E';"
                                                            onmouseout="this.style.background='#fff'; this.style.borderColor='#A4ABB8';">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                                            </svg>
                                                            Tambah Berkas
                                                        </button>

                                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; margin-bottom:12px;">
                                                            <span style="font-size:11px; color:#666D80;">Batas akumulasi maksimal lampiran adalah 3 file per tugas.</span>
                                                            <span style="font-size:11px; color:#666D80;">Maks. 5MB per file</span>
                                                        </div>

                                                        <div x-show="retainedFiles.length > 0" style="font-size:13px;font-weight:600;color:#353849;margin:16px 0 8px;">Berkas Saat Ini:</div>
                                                        <div x-show="retainedFiles.length > 0" style="display:flex;flex-direction:column;gap:5px; margin-bottom:6px;">
                                                            <template x-for="(file, index) in retainedFiles" :key="file.path">
                                                                <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border:1px solid #DFE1E7; border-radius:8px; background:#F9FAFB; margin-bottom:5px;">
                                                                    <input type="hidden" name="retained_files[]" :value="file.path">
                                                                    <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
                                                                        <span x-text="file.name" style="font-size:13px;font-weight:500;color:#353849;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></span>
                                                                    </div>
                                                                    <button type="button" @click="removeRetainedFile(index)" aria-label="Hapus berkas" style="color:#DF1C41;background:none;border:none;padding:4px;cursor:pointer;display:flex;border-radius:4px;" class="mp-delete-button" title="Hapus"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        <div x-show="files.length > 0" style="display:flex;flex-direction:column;gap:5px; margin-bottom:12px;">
                                                            <template x-for="(file, index) in files" :key="index">
                                                                <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border:1px solid #DFE1E7; border-radius:8px; background:#fff; margin-bottom:5px;">
                                                                    <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                                                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                                            <polyline points="13 2 13 9 20 9"></polyline>
                                                                        </svg>
                                                                        <span x-text="file.name" style="font-size:13px; font-weight:500; color:#353849; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"></span>
                                                                    </div>
                                                                    <button type="button" @click="removeFile(index)" style="color:#DF1C41; background:none; border:none; padding:4px; cursor:pointer; display:flex; align-items:center; justify-content:center; border-radius:4px;" onmouseover="this.style.background='#FFF0F2'" onmouseout="this.style.background='none'">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </template>
                                                        </div>

                                                        {{-- Action Buttons --}}
                                                        <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:4px; flex-wrap:wrap;">
                                                            <button type="submit" class="mp-btn primary md" x-bind:disabled="retainedFiles.length + files.length === 0" style="width:150px; border-radius:8px; font-size:13px; justify-content:center;">
                                                                {{ $statusTugas === 'revisi' ? 'Kumpul Revisi' : ($statusTugas === 'draft' ? 'Kumpulkan Kembali' : 'Kumpulkan') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                        
                                    @else
                                        <div style="font-size: 13px; color: #6B7280; font-style: italic;">{{ $label }} belum diunggah.</div>
                                    @endif

                                </div>
                            </x-eoffice::manajemen-praktikum.ui.grade-accordion>

                        </div>
                    @endforeach
                    </div>
                </x-eoffice::manajemen-praktikum.ui.grade-accordion>

            </div>
        @empty
            <div style="padding: 48px; text-align: center; border: 1px solid var(--c-border); border-radius: 8px; background: #fff;">
                <x-eoffice::manajemen-praktikum.ui.empty-icon name="task" />
                <div style="font-size: 14px; font-weight: 600; color: #111827;">Belum Ada Modul & Tugas</div>
                <div style="font-size: 13px; color: #6B7280; margin-top: 4px;">Belum ada data yang dapat ditampilkan.</div>
            </div>
        @endforelse
    </div>

    <x-eoffice::manajemen-praktikum.ui.tugas-akhir-card :praktikum="$daftarPraktikan?->praktikum" role="mahasiswa" section="tugas" />
</x-eoffice::manajemen-praktikum.layout>
