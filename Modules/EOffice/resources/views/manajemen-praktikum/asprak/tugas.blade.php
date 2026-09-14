<x-eoffice::manajemen-praktikum.layout :pageTitle="($praktikum->nama ?? 'Praktikum') . ' / Tugas'">

    {{-- Asprak Header Banner --}}
    @if(isset($praktikum) && $praktikum)
        <x-eoffice::manajemen-praktikum.asprak-header :praktikum="$praktikum" activeTab="tugas" />
    @endif

    <style>
        .modul-accordion-content {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.15s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.1s ease;
        }

        .modul-accordion-content.is-open {
            max-height: 2500px;
            opacity: 1;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
        }

        .tugas-accordion-content {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.15s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.1s ease;
        }

        .tugas-accordion-content.is-open {
            max-height: 1500px;
            opacity: 1;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
        }
    </style>

    <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 4px;">
        @forelse($modulList as $item)
            @php 
                $modul = $item['modul'];
                $tugasList = $item['tugas'];
                $isMine = $assignedModulIds->contains($modul->id);
            @endphp

            <div x-data="{ modulOpen: false }"
                style="background: #fff; border: 1px solid var(--c-border); border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; @if(!$isMine) opacity:0.8; @endif">
                {{-- Card Header: Modul Info --}}
                <div @click="modulOpen = !modulOpen"
                    style="padding: 16px 24px; background: #fff; border-bottom: 1px solid var(--c-border); cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: background 0.15s;"
                    onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                    <div>
                        <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 4px 0;">{{ $modul->nama }}</h3>
                        <div style="font-size: 13px; color: #6B7280;">
                            Asisten:
                            @if($item['asprak']->isNotEmpty()) {{ $item['asprak']->join(', ') }} @else - @endif
                        </div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        :style="modulOpen ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                {{-- List of Tugas as Accordions --}}
                <div class="modul-accordion-content" :class="{ 'is-open': modulOpen }">
                    <div style="display: flex; flex-direction: column;">
                        @php
                            $requiredTypes = ['tugas_pendahuluan' => 'Tugas Pendahuluan', 'laporan' => 'Laporan', 'responsi' => 'Responsi', 'tugas_pengganti' => 'Tugas Pengganti'];
                        @endphp

                        @foreach($requiredTypes as $type => $label)
                            @php
                                $t = $tugasList->firstWhere('jenis_tugas', $type);
                                $dlAC = $t && $t->deadline ? \Carbon\Carbon::parse($t->deadline) : null;
                                $dlACC = $t && $t->deadline_acc ? \Carbon\Carbon::parse($t->deadline_acc) : null;
                                $lewat = $dlACC && now()->gt($dlACC);
                                $oldFiles = [];
                                if ($t && $t->file_path) {
                                    $oldFiles = json_decode($t->file_path, true) ?? [];
                                    if (!is_array($oldFiles))
                                        $oldFiles = [$t->file_path];
                                }
                            @endphp
                            <div x-data="{ open: false, isEditing: false }" style="border-bottom: 1px solid var(--c-border);">

                                {{-- Accordion Header --}}
                                <div style="padding: 14px 24px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: #fff; transition: background 0.15s;"
                                    onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">

                                    {{-- Click Area Title --}}
                                    <div @click="open = !open; isEditing = false;"
                                        style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                        <div style="font-size: 14px; font-weight: 600; color: #0D0D12;">{{ $label }}</div>
                                        @if($t)
                                            @if($lewat) <span class="mp-badge neutral sm" style="font-size: 10px;">Berakhir</span>
                                            @else <span class="mp-badge success sm" style="font-size: 10px;"><span
                                                class="dot"></span>Aktif</span>
                                            @endif
                                        @endif
                                    </div>

                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        @if($t)
                                            <div style="font-size: 12px; color: #666D80; display: flex; gap: 10px;">
                                                @if($dlAC && $dlACC)
                                                    <span style="color:{{ $lewat ? '#A4ABB8' : '#353849' }};">Deadline AC:
                                                        {{ $dlAC->format('d/m/Y, H:i') }}</span>
                                                    <span style="color:{{ $lewat ? '#A4ABB8' : '#353849' }};">Deadline ACC:
                                                        {{ $dlACC->format('d/m/Y, H:i') }}</span>
                                                @elseif($dlACC)
                                                    <span style="color:{{ $lewat ? '#A4ABB8' : '#353849' }};">Deadline AC: -</span>
                                                    <span style="color:{{ $lewat ? '#A4ABB8' : '#353849' }};">Deadline ACC:
                                                        {{ $dlACC->format('d/m/Y, H:i') }}</span>
                                                @elseif($dlAC)
                                                    <span style="color:{{ $lewat ? '#A4ABB8' : '#353849' }};">Deadline AC:
                                                        {{ $dlAC->format('d/m/Y, H:i') }}</span>
                                                    <span style="color:{{ $lewat ? '#A4ABB8' : '#353849' }};">Deadline ACC: -</span>
                                                @else
                                                    <span style="color:#A4ABB8;">Tanpa batas waktu</span>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Pencil & Chevron --}}
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            @if($isMine)
                                            <button type="button" @click.stop="open = true; isEditing = true;"
                                                title="{{ $t ? 'Edit Tugas' : 'Buat Tugas' }}"
                                                style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background: #EEF2FF; color: #4F46E5; border: 1px solid #E0E7FF; outline:none; cursor:pointer;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </button>
                                            @endif
                                            <div @click="open = !open; isEditing = false;"
                                                style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background: transparent; color: #666D80; cursor: pointer;">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    :style="open ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'">
                                                    <polyline points="6 9 12 15 18 9"></polyline>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Accordion Body (View & Edit Modes) --}}
                                <div class="tugas-accordion-content" :class="{ 'is-open': open }">

                                    {{-- VIEW MODE --}}
                                    <div x-show="!isEditing"
                                        style="border-top: 1px solid var(--c-border); padding: 20px 24px 16px; background: #FAFBFC;">
                                        @if($t)
                                            @if($t->deskripsi)
                                                <div style="font-size: 13px; color: #374151; margin-bottom: 20px; line-height: 1.6; white-space: pre-wrap; text-align: left; margin-left: 0;">{{ trim($t->deskripsi) }}</div>
                                            @endif

                                            <div
                                                style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
                                                <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                                                    @foreach($oldFiles as $idx => $f)
                                                        @php 
                                                            $pathStr = is_array($f) && isset($f['path']) ? $f['path'] : $f;
                                                            $baseName = is_array($f) && isset($f['original_name']) ? $f['original_name'] : pathinfo($pathStr, PATHINFO_BASENAME);
                                                        @endphp
                                                        <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($pathStr, 'eoffice') }}"
                                                            target="_blank" title="{{ $baseName }}"
                                                            style="display: flex; flex-direction: column; width: 140px; height: 140px; border: 1px solid #DFE1E7; border-radius: 8px; overflow: hidden; text-decoration: none; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                                            <div
                                                                style="flex: 1; display: flex; align-items: center; justify-content: center; background: #F9FAFB;">
                                                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                                                    stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round"
                                                                    stroke-linejoin="round">
                                                                    <path
                                                                        d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z">
                                                                    </path>
                                                                    <polyline points="13 2 13 9 20 9"></polyline>
                                                                </svg>
                                                            </div>
                                                            <div
                                                                style="background: #293C79; color: #fff; padding: 10px 12px; font-size: 13px; font-weight: 600; text-align: center; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="{{ $baseName }}">
                                                                {{ $baseName }}
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>

                                                <div style="display: flex; gap: 8px;">
                                                    @if($isMine)
                                                        <a href="{{ route('eoffice.manprak.asprak.tugas.pengumpulan', $t->id) }}"
                                                            class="mp-btn primary sm" style="text-decoration: none; padding: 8px 16px;">
                                                            Lihat Pengumpulan
                                                        </a>
                                                    @else
                                                        <span style="font-size: 12px; color: #9CA3AF; font-style: italic; background: #F3F4F6; padding: 6px 12px; border-radius: 6px;">Anda bukan pengampu di modul ini.</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div style="font-size: 13px; color: #6B7280; font-style: italic;">{{ $label }} belum
                                                diunggah.</div>
                                        @endif
                                    </div>

                                    {{-- EDIT / CREATE FORM MODE --}}
                                    <div x-show="isEditing"
                                        style="border-top: 1px solid var(--c-border); padding: 24px; background: #fff;" x-cloak>
                                        <form method="POST"
                                            action="{{ $t ? route('eoffice.manprak.asprak.tugas.update', $t->id) : route('eoffice.manprak.asprak.tugas.store') }}"
                                            enctype="multipart/form-data"
                                            style="display:flex;flex-direction:column;gap:20px; margin:0;">
                                            @csrf

                                            {{-- DO NOT USE @method('PUT') based on explicit POST route requirement --}}

                                            <input type="hidden" name="modul_id" value="{{ $modul->id }}">
                                            <input type="hidden" name="jenis_tugas" value="{{ $type }}">
                                            <input type="hidden" name="judul" value="{{ $label }}">

                                            <div>
                                                <label
                                                    style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Modul
                                                    <span style="color:#DF1C41;">*</span></label>
                                                <input type="text" class="mp-input" readonly
                                                    value="{{ $modul->nama }}"
                                                    style="min-height:44px; background:#F9FAFB; cursor:not-allowed; color:#666D80; box-shadow:none;">
                                            </div>

                                            <div>
                                                <label
                                                    style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Deskripsi
                                                    <span style="color:#A4ABB8;">(opsional)</span></label>
                                                <textarea name="deskripsi" class="mp-input" rows="3" maxlength="1000" style="resize:vertical; min-height:80px; max-height:300px;"
                                                    placeholder="Jelaskan detail tugas, instruksi pengerjaan, atau catatan...">{{ $t ? trim($t->deskripsi) : '' }}</textarea>
                                            </div>

                                            {{-- Upload File --}}
                                            <div x-data="{
                                                            filesArr: [],
                                                            deletedOldFileIndices: [],
                                                            handleFiles(fList) {
                                                                let currentCount = ({{ count($oldFiles) }} - this.deletedOldFileIndices.length);
                                                                for(let i=0; i<fList.length; i++) {
                                                                    if(this.filesArr.length + currentCount >= 3) {
                                                                        alert('Batas maksimal 3 berkas lampiran telah tercapai!');
                                                                        break;
                                                                    }
                                                                    if(fList[i].size > 5242880) {
                                                                        alert('File ' + fList[i].name + ' melebihi 5MB!');
                                                                    } else {
                                                                        this.filesArr.push(fList[i]);
                                                                    }
                                                                }
                                                                this.syncInput();
                                                            },
                                                            removeFile(idx) {
                                                                this.filesArr.splice(idx, 1);
                                                                this.syncInput();
                                                            },
                                                            syncInput() {
                                                                const dt = new DataTransfer();
                                                                this.filesArr.forEach(f => dt.items.add(f));
                                                                this.$refs.fileInput.files = dt.files;
                                                            }
                                                         }">
                                                <label
                                                    style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Berkas
                                                    Lampiran <span style="color:#A4ABB8;">(opsional)</span></label>

                                                <input type="file" name="files[]" multiple x-ref="fileInput"
                                                    @change="handleFiles($event.target.files)" style="display:none;">
                                                <input type="hidden" name="is_published" value="1">

                                                <button type="button" @click="$refs.fileInput.click()"
                                                    style="width: 100%; border: 1px dashed var(--c-border); border-radius: 8px; padding: 14px; background: #fff; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 14px; font-weight: 500; color: #293C79; cursor: pointer; transition: background 0.15s;"
                                                    onmouseover="this.style.background='#F6F8FA'"
                                                    onmouseout="this.style.background='#fff'">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                                    </svg>
                                                    Tambah Berkas
                                                </button>

                                                <div
                                                    style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                                                    <span style="font-size:12px; color:#6B7280;">Batas akumulasi maksimal
                                                        lampiran adalah 3 file per tugas.</span>
                                                    <span style="font-size:12px; color:#6B7280;">Maks. 5MB per file</span>
                                                </div>

                                                <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 8px;"
                                                    x-show="filesArr.length > 0" x-cloak>
                                                    <template x-for="(f, idx) in filesArr" :key="idx">
                                                        <div
                                                            style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; padding: 8px 12px; border: 1px solid #DFE1E7; border-radius: 6px; background: #F9FAFB; margin-bottom: 5px; width: 100%;">
                                                            <div style="display: flex; flex-direction: row; align-items: center; gap: 8px; font-size: 13px; color: #111827; flex: 1; min-width: 0;">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                    stroke="#6B7280" stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round" style="flex-shrink: 0;">
                                                                    <path
                                                                        d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z">
                                                                    </path>
                                                                    <polyline points="13 2 13 9 20 9"></polyline>
                                                                </svg>
                                                                <span x-text="f.name"
                                                                    style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1;"></span>
                                                            </div>
                                                            <button type="button" @click="removeFile(idx)"
                                                                style="background: none; border: none; cursor: pointer; color: #DF1C41; outline: none; padding: 0; display: flex; flex-shrink: 0; margin-left: 12px;"
                                                                title="Batal Lampirkan">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round">
                                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>

                                                @if($t && count($oldFiles) > 0)
                                                    <div style="margin-top: 8px;">
                                                        <input type="hidden" name="hapus_file" :value="JSON.stringify(deletedOldFileIndices)">
                                                        @foreach($oldFiles as $fIdx => $fOld)
                                                            @php 
                                                                $pathStr = is_array($fOld) && isset($fOld['path']) ? $fOld['path'] : $fOld;
                                                                $baseName = is_array($fOld) && isset($fOld['original_name']) ? $fOld['original_name'] : pathinfo($pathStr, PATHINFO_BASENAME);
                                                            @endphp
                                                            {{-- Outer wrapper handles x-show (Alpine restores to 'block' which is fine as a wrapper) --}}
                                                            <div x-show="!deletedOldFileIndices.includes({{ $fIdx }})" style="margin-bottom: 5px;">
                                                                {{-- Inner div is always flex, unaffected by Alpine x-show toggling --}}
                                                                <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; padding: 8px 12px; border: 1px solid #DFE1E7; border-radius: 6px; background: #F9FAFB; width: 100%;">
                                                                    <div style="display: flex; flex-direction: row; align-items: center; gap: 8px; font-size: 13px; color: #293C79; flex: 1; min-width: 0;">
                                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                                            stroke="#6B7280" stroke-width="2" stroke-linecap="round"
                                                                            stroke-linejoin="round" style="flex-shrink: 0;">
                                                                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                                            <polyline points="13 2 13 9 20 9"></polyline>
                                                                        </svg>
                                                                        <a href="{{ \App\Services\SupabaseStorage::class ? app(\App\Services\SupabaseStorage::class)->publicUrl($pathStr, 'eoffice') : '#' }}" target="_blank"
                                                                            style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-decoration: none; color: #293C79; flex: 1; min-width: 0;"
                                                                            title="{{ $baseName }}">{{ $baseName }}</a>
                                                                    </div>
                                                                    <button type="button" @click="deletedOldFileIndices.push({{ $fIdx }})"
                                                                        style="background: none; border: none; cursor: pointer; color: #DF1C41; outline: none; padding: 0; display: flex; align-items: center; flex-shrink: 0; margin-left: 12px;"
                                                                        title="Hapus berkas lama ini">
                                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Deadlines with Custom Flatpickr --}}
                                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                                                <div>
                                                    <label
                                                        style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Deadline
                                                        AC <span style="color:#DF1C41;">*</span></label>
                                                    <div style="position:relative;">
                                                        <input type="text" name="deadline"
                                                            class="mp-input flatpickr-input-custom"
                                                            value="{{ $t && $t->deadline ? $t->deadline->format('Y-m-d H:i') : '' }}"
                                                            placeholder="mm/dd/yyyy --:--"
                                                            style="min-height:44px; padding-right:36px; width: 100%;">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%); pointer-events:none;"
                                                            stroke="#6B7280" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Deadline
                                                        ACC <span style="color:#DF1C41;">*</span></label>
                                                    <div style="position:relative;">
                                                        <input type="text" name="deadline_acc"
                                                            class="mp-input flatpickr-input-custom"
                                                            value="{{ $t && $t->deadline_acc ? $t->deadline_acc->format('Y-m-d H:i') : '' }}"
                                                            placeholder="mm/dd/yyyy --:--"
                                                            style="min-height:44px; padding-right:36px; width: 100%;">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%); pointer-events:none;"
                                                            stroke="#6B7280" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                                                <button type="submit"
                                                    class="font-bold rounded-[8px] transition-colors duration-200 bg-[#0B266E] text-white hover:bg-[#081e59] focus:outline-none"
                                                    style="height: 40px; font-size: 13px; padding: 0 16px;">
                                                    {{ $t ? 'Simpan' : 'Buat Tugas' }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        @empty
            <div
                style="padding: 48px; text-align: center; border: 1px solid var(--c-border); border-radius: 8px; background: #fff;">
                <div style="font-size: 14px; font-weight: 600; color: #111827;">Belum Ada Modul & Tugas</div>
                <div style="font-size: 13px; color: #6B7280; margin-top: 4px;">Anda belum di-assign modul manapun.</div>
            </div>
        @endforelse
    </div>

</x-eoffice::manajemen-praktikum.layout>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Styling Flatpickr untuk mengikuti tema desain */
    .flatpickr-calendar {
        width: 340px !important;
        padding-right: 12px !important;
        box-shadow: 0 8px 24px rgba(11, 38, 110, 0.12) !important;
        border: 1px solid #DFE1E7 !important;
        border-radius: 12px !important;
        font-family: inherit !important;
        padding-left: 8px !important;
        padding-bottom: 8px !important;
    }

    .flatpickr-calendar.hasTime .flatpickr-time {
        border-top: 1px solid #DFE1E7 !important;
        margin-top: 8px;
    }

    .flatpickr-days {
        width: 320px !important;
    }

    .dayContainer {
        width: 320px !important;
        min-width: 320px !important;
        max-width: 320px !important;
    }

    .flatpickr-months {
        margin-bottom: 8px !important;
    }

    .flatpickr-current-month {
        font-size: 14px !important;
        padding-top: 0px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months {
        display: none !important;
    }

    .flatpickr-current-month .cur-month {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #353849 !important;
        padding-top: 0px !important;
        cursor: pointer;
    }

    .flatpickr-current-month .cur-month:hover {
        color: #0B266E !important;
    }

    .custom-month-dropdown {
        position: absolute;
        background: #fff;
        border: 1px solid #DFE1E7;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(11, 38, 110, 0.12);
        z-index: 99999;
        width: 140px;
        max-height: 220px;
        overflow-y: auto;
        padding: 6px;
        font-family: inherit;
        visibility: hidden;
        opacity: 0;
        transform: translateY(-8px);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }

    .custom-month-dropdown::-webkit-scrollbar {
        width: 6px;
    }

    .custom-month-dropdown::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-month-dropdown::-webkit-scrollbar-thumb {
        background: #DFE1E7;
        border-radius: 4px;
    }

    .custom-month-dropdown.show {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .custom-month-arrow svg {
        transition: transform 0.2s ease;
    }

    .custom-month-arrow.open svg {
        transform: rotate(180deg);
    }

    .custom-month-item {
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 500;
        color: #353849;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .custom-month-item:hover {
        background: #F6F8FA;
        color: #0B266E;
    }

    .custom-month-item.active {
        background: #0B266E !important;
        color: #fff !important;
    }

    .flatpickr-current-month .numInputWrapper {
        width: 65px !important;
        border-radius: 6px !important;
        margin-left: 4px;
    }

    .flatpickr-current-month input.cur-year {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #353849 !important;
        padding: 6px 8px !important;
        border: 1px solid transparent !important;
        border-radius: 6px !important;
        height: 32px !important;
        box-sizing: border-box !important;
        background: transparent !important;
        transition: all 0.2s;
    }

    .flatpickr-current-month .numInputWrapper:hover input.cur-year {
        border: 1px solid #DFE1E7 !important;
        background: #F9FAFB !important;
    }

    .flatpickr-current-month .numInputWrapper span.arrowUp,
    .flatpickr-current-month .numInputWrapper span.arrowDown {
        border: none !important;
        border-left: 1px solid transparent !important;
        right: 1px;
    }

    .flatpickr-current-month .numInputWrapper:hover span.arrowUp,
    .flatpickr-current-month .numInputWrapper:hover span.arrowDown {
        border-left: 1px solid #DFE1E7 !important;
    }

    .flatpickr-current-month .numInputWrapper span.arrowUp {
        border-top-right-radius: 5px;
        top: 1px;
    }

    .flatpickr-current-month .numInputWrapper span.arrowDown {
        border-bottom-right-radius: 5px;
        bottom: 1px;
    }

    .flatpickr-current-month .numInputWrapper span:hover {
        background: #F0F2F5 !important;
    }

    .flatpickr-current-month .numInputWrapper span.arrowUp:after {
        border-bottom-color: #666D80 !important;
        top: 35% !important;
    }

    .flatpickr-current-month .numInputWrapper span.arrowDown:after {
        border-top-color: #666D80 !important;
        top: 40% !important;
    }

    .flatpickr-current-month .numInputWrapper span.arrowUp:hover:after {
        border-bottom-color: #0B266E !important;
    }

    .flatpickr-current-month .numInputWrapper span.arrowDown:hover:after {
        border-top-color: #0B266E !important;
    }

    .flatpickr-day {
        border-radius: 8px !important;
        font-size: 13px !important;
        color: #353849 !important;
        max-width: 42px !important;
        height: 40px !important;
        line-height: 40px !important;
    }

    .flatpickr-day.selected,
    .flatpickr-day.selected:focus,
    .flatpickr-day.selected:hover {
        background: #0B266E !important;
        border-color: #0B266E !important;
        color: #fff !important;
        font-weight: 600;
    }

    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
        color: #C1C7D0 !important;
    }

    .flatpickr-day:hover,
    .flatpickr-day.prevMonthDay:hover,
    .flatpickr-day.nextMonthDay:hover {
        background: #F6F8FA !important;
        border-color: #DFE1E7 !important;
    }

    .flatpickr-time input {
        color: #353849 !important;
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    .flatpickr-time .flatpickr-time-separator {
        color: #808897 !important;
    }

    .flatpickr-time .numInputWrapper:hover {
        background: #F6F8FA !important;
    }

    [x-cloak] {
        display: none !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    function initializeCustomFlatpickr(selector) {
        flatpickr(selector, {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            allowInput: true,
            monthSelectorType: "static",
            onReady: function (selectedDates, dateStr, instance) {
                const monthDropdown = document.createElement('div');
                monthDropdown.className = 'custom-month-dropdown';
                const monthsStr = instance.l10n.months.longhand;
                monthsStr.forEach((month, index) => {
                    const div = document.createElement('div');
                    div.className = 'custom-month-item';
                    div.textContent = month;
                    div.addEventListener('click', (e) => {
                        e.stopPropagation();
                        instance.changeMonth(index, false);
                        monthDropdown.classList.remove('show');
                        if (instance.monthNav.querySelector('.custom-month-arrow')) {
                            instance.monthNav.querySelector('.custom-month-arrow').classList.remove('open');
                        }
                    });
                    monthDropdown.appendChild(div);
                });

                instance.calendarContainer.appendChild(monthDropdown);
                const monthElement = instance.monthNav.querySelector('.cur-month');
                if (monthElement) {
                    const arrow = document.createElement('span');
                    arrow.className = 'custom-month-arrow';
                    arrow.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"></polyline></svg>`;
                    arrow.style.marginLeft = '4px';
                    arrow.style.cursor = 'pointer';
                    arrow.style.display = 'inline-flex';
                    arrow.style.alignItems = 'center';
                    monthElement.parentNode.insertBefore(arrow, monthElement.nextSibling);

                    const toggleDropdown = (e) => {
                        e.stopPropagation();
                        monthDropdown.classList.toggle('show');
                        if (monthDropdown.classList.contains('show')) {
                            arrow.classList.add('open');
                        } else {
                            arrow.classList.remove('open');
                        }
                        const monthRect = monthElement.getBoundingClientRect();
                        const calRect = instance.calendarContainer.getBoundingClientRect();
                        monthDropdown.style.top = (monthRect.bottom - calRect.top + 3) + 'px';
                        monthDropdown.style.left = (monthRect.left - calRect.left - 6) + 'px';
                        const items = monthDropdown.querySelectorAll('.custom-month-item');
                        items.forEach((item, idx) => {
                            item.classList.toggle('active', idx === instance.currentMonth);
                        });
                        const activeItem = monthDropdown.querySelector('.custom-month-item.active');
                        if (activeItem && monthDropdown.classList.contains('show')) {
                            monthDropdown.scrollTop = activeItem.offsetTop - 10;
                        }
                    };
                    monthElement.addEventListener('click', toggleDropdown);
                    arrow.addEventListener('click', toggleDropdown);
                }

                document.addEventListener('click', (e) => {
                    if (monthDropdown.classList.contains('show') && !instance.monthNav.contains(e.target) && !monthDropdown.contains(e.target)) {
                        monthDropdown.classList.remove('show');
                        if (monthElement.nextSibling && monthElement.nextSibling.classList) {
                            monthElement.nextSibling.classList.remove('open');
                        }
                    }
                });
            }
        });
    }

    document.addEventListener('alpine:init', () => {
        initializeCustomFlatpickr('.flatpickr-input-custom');
    });
    setTimeout(() => {
        initializeCustomFlatpickr('.flatpickr-input-custom');
    }, 500);
</script>