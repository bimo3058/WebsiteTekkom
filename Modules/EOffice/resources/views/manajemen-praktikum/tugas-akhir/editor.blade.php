<section class="mp-card ta-card" style="border-radius:8px" @ta-delete="deleteOpen=true" x-data="{ deleteOpen:false, expanded: {{ (!$ta || !$ta->published || $errors->any()) ? 'true' : 'false' }} }">
    <button type="button" class="ta-card-toggle" @click="expanded=!expanded" :aria-expanded="expanded">
        <span><strong style="font-size:14px">Tugas Akhir</strong><span>Tipe Tugas Akhir: {{ ['individu'=>'Individu','kelompok'=>'Kelompok','kelompok_besar'=>'Kelompok besar'][$ta?->tipe] ?? 'Belum ditentukan' }}</span>@if($ta)<span>{{ $ta->created_at->translatedFormat('d M Y, H:i') }}@if($ta->updated_at->gt($ta->created_at)) <em>(Diedit {{ $ta->updated_at->translatedFormat('d M Y, H:i') }})</em>@endif</span>@endif</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" style="transition:transform .2s" :style="expanded ? 'transform:rotate(180deg)' : ''"><path d="m6 9 6 6 6-6"/></svg>
    </button>
    <x-eoffice::manajemen-praktikum.ui.grade-accordion state="expanded"><div style="border-top:1px solid #DFE1E7">
        <form method="POST" action="{{ route($base.'save',$routeArgs) }}" enctype="multipart/form-data" class="ta-form ta-editor" x-data="{{ json_encode($editor + ['tipe'=>old('tipe',$ta?->tipe ?? ''), 'confirmedStages'=>old('konfirmasi_hapus_tahap',[]), 'weightsLocked'=>$taWeightsLocked]) }}">
            @csrf<input type="hidden" name="version" value="{{ $ta?->version ?? 0 }}">

            <div class="ta-body">
                <div><label>Tipe Tugas Akhir <span class="ta-required">*</span></label><input type="hidden" name="tipe" :value="tipe" required><x-eoffice::manajemen-praktikum.ui.ta-select x-model="tipe" :options="['individu'=>'Individu','kelompok'=>'Kelompok','kelompok_besar'=>'Kelompok besar']" placeholder="Pilih tipe tugas akhir..." /><p class="ta-muted">Pengumpulan dan nilai tetap dicatat per mahasiswa.</p></div>
                <div><label>Ketentuan Umum <span class="ta-required">*</span> <span class="ta-muted">(Maks. 5000 karakter)</span></label><textarea name="ketentuan" required maxlength="5000" rows="1" x-init="$nextTick(() => window.taTextarea?.($el, true))" placeholder="Contoh: Buat aplikasi sesuai topik yang disepakati. Cantumkan tautan repositori dan laporan.">{{ old('ketentuan',$ta?->ketentuan) }}</textarea></div>
                <div><label>Berkas Ketentuan <span class="ta-muted">(opsional)</span></label><x-eoffice::manajemen-praktikum.ui.ta-upload name="lampiran[]" :stored="$ta?->lampiran ?? []" /></div>
                <div class="ta-divider ta-editor-section-heading"><h3 class="mp-card-title">Tahapan Tugas Akhir</h3><p class="ta-muted">Atur kegiatan, pengumpulan, dan kolom penilaian per tahap.</p></div>
                <template x-for="(item,index) in tahap" :key="item._key || item.id">
                    <section class="ta-stage" x-data="{ stageOpen: true, stageAnimating: false }">
                        <div class="ta-stage-heading" :class="{ 'is-open': stageOpen }"><button type="button" @click="stageAnimating=!window.matchMedia('(prefers-reduced-motion: reduce)').matches;stageOpen=!stageOpen" :aria-expanded="stageOpen"><strong x-text="item.nama || 'Tahap ' + (index+1)"></strong><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" style="transition:transform .24s ease" :style="stageOpen ? 'transform:rotate(180deg)' : ''"><path d="m6 9 6 6 6-6"/></svg></button></div>
                        <div class="ta-stage-expand" :class="{ 'is-open': stageOpen, 'is-animating': stageAnimating }" :aria-hidden="!stageOpen" :inert="!stageOpen" @transitionend.self="if($event.propertyName==='grid-template-rows') stageAnimating=false"><div class="ta-stage-clip"><div class="ta-stage-body">
                            <input type="hidden" :name="`tahap[${index}][id]`" :value="item.id || ''">
                            <div><label>Nama Tahap <span class="ta-required">*</span></label><input :name="`tahap[${index}][nama]`" x-model="item.nama" maxlength="150" required placeholder="Contoh: Asistensi"></div>
                            <div><label>Ketentuan Tahap <span class="ta-required">*</span> <span class="ta-muted">(Maks. 5000 karakter)</span></label><textarea :name="`tahap[${index}][ketentuan]`" x-model="item.ketentuan" maxlength="5000" rows="1" x-init="$nextTick(() => window.taTextarea?.($el, true))" required placeholder="Contoh: Presentasikan perkembangan proyek dan unggah laporan progres."></textarea></div>
                            <div class="ta-date-range"><div><label>Tambah Pengumpulan Berkas</label><input type="hidden" :name="`tahap[${index}][pengumpulan]`" :value="item.pengumpulan ? 1 : 0"><x-eoffice::manajemen-praktikum.ui.ta-select x-model="item.pengumpulan" :options="['true'=>'Ya','false'=>'Tidak']" /><p class="ta-muted">Tanpa pengumpulan, asisten tetap dapat mengisi nilai.</p></div><div x-show="item.pengumpulan"><label>Dibuka Pada</label><input type="datetime-local" x-init="$nextTick(() => window.taDatePicker?.($el, value => item.dibuka_pada=value))" :name="`tahap[${index}][dibuka_pada]`" x-model="item.dibuka_pada" :disabled="!item.pengumpulan"><p class="ta-muted">Kosongkan untuk langsung dibuka saat dipublikasikan.</p></div><div x-show="item.pengumpulan"><label>Ditutup Pada <span class="ta-required">*</span></label><input type="datetime-local" x-init="$nextTick(() => window.taDatePicker?.($el, value => item.deadline=value))" :name="`tahap[${index}][deadline]`" x-model="item.deadline" :required="item.pengumpulan" :disabled="!item.pengumpulan" aria-label="Tenggat pengumpulan"><p class="ta-muted">Wajib diisi sebagai batas pengumpulan.</p></div></div>
                            <div><label>Berkas Tahap <span class="ta-muted">(opsional)</span></label><x-eoffice::manajemen-praktikum.ui.ta-upload x-bind:name="`tahap[${index}][lampiran][]`" x-bind:data-stored-count="(item.lampiran || []).length" /></div>
                            <template x-if="item.lampiran && item.lampiran.length"><div><template x-for="file in item.lampiran" :key="file.path"><div class="ta-file"><span x-text="file.nama"></span><button type="button" class="ta-file-remove" @click="(item.hapus_lampiran ||= []).push(file.path); item.lampiran=item.lampiran.filter(f=>f.path!==file.path)" aria-label="Hapus berkas tahap" title="Hapus berkas tahap"><x-eoffice::manajemen-praktikum.ui.icon name="close" /></button></div></template></div></template><template x-for="path in (item.hapus_lampiran || [])" :key="path"><input type="hidden" name="hapus_lampiran[]" :value="path"></template>
                            <div class="ta-divider"><div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px"><div><label style="margin:0">Kolom Penilaian <span class="ta-required">*</span></label><p class="ta-muted">Wajib diisi minimal satu kolom. Kolom yang masih memiliki nilai tidak dapat dihapus.</p></div><button type="button" class="mp-btn primary sm ta-add-button" :disabled="item._structureLocked" @click="item.parameter.push({nama:''})">Tambah Kolom</button></div>
                                <template x-for="(parameter,pindex) in item.parameter" :key="parameter.id || parameter._key || (parameter._key = Math.random().toString(36))"><div class="ta-parameter"><input type="hidden" :name="`tahap[${index}][parameter][${pindex}][id]`" :value="parameter.id || ''"><input :name="`tahap[${index}][parameter][${pindex}][nama]`" x-model="parameter.nama" :readonly="item._structureLocked" maxlength="150" required placeholder="Contoh: Pemahaman materi"><button type="button" class="mp-btn sm ta-icon-button" aria-label="Hapus kolom penilaian" :disabled="parameter._deleteBlocked" :title="parameter._deleteBlocked ? 'Susunan kolom dikunci karena tahap sudah digunakan.' : 'Hapus kolom'" @click="if(!parameter._deleteBlocked) item.parameter.splice(pindex,1)"><x-eoffice::manajemen-praktikum.ui.icon name="trash" /></button></div></template>
                            </div>
                            <div class="ta-divider" x-show="item.pengumpulan"><label>Revisi</label><input type="hidden" :name="`tahap[${index}][revisi_aktif]`" :value="item.pengumpulan && item.revisi_aktif ? 1 : 0"><x-eoffice::manajemen-praktikum.ui.ta-select x-model="item.revisi_aktif" :options="['false'=>'Belum dibuka','true'=>'Dibuka']" /></div>
                            <div x-show="item.pengumpulan && item.revisi_aktif" class="ta-two-columns"><div><label>Tenggat Revisi <span class="ta-required">*</span></label><input type="datetime-local" x-init="$nextTick(() => window.taDatePicker?.($el, value => item.revisi_deadline=value))" :name="`tahap[${index}][revisi_deadline]`" x-model="item.revisi_deadline" :required="item.pengumpulan && item.revisi_aktif" :disabled="!item.pengumpulan || !item.revisi_aktif"></div><div><label>Perhitungan Nilai Revisi</label><input type="hidden" :name="`tahap[${index}][revisi_metode]`" :value="item.revisi_metode"><x-eoffice::manajemen-praktikum.ui.ta-select x-model="item.revisi_metode" :options="['rata_rata'=>'Rata-rata nilai awal dan revisi','berbobot'=>'Berbobot','tertinggi'=>'Nilai tertinggi','ganti'=>'Gunakan nilai revisi']" /></div><div x-show="item.revisi_metode==='berbobot'"><label>Bobot Nilai Awal (%)</label><input type="number" min="0" max="100" :name="`tahap[${index}][revisi_bobot_awal]`" x-model="item.revisi_bobot_awal" placeholder="Contoh: 40"><p class="ta-muted">Sisanya menjadi bobot nilai revisi.</p></div><p class="ta-muted" style="grid-column:1 / -1">Mahasiswa dapat mengumpulkan revisi selama periode revisi dibuka. Tanpa pengumpulan revisi, nilai awal tetap digunakan.</p></div>
                            <div class="ta-stage-actions" style="align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap"><p class="ta-muted" style="margin:0">Tahap yang sudah memiliki pengumpulan atau nilai tidak dapat dihapus.</p><button type="button" class="mp-btn error sm" :disabled="tahap.length===1 || item._deleteBlocked" :title="item._deleteBlocked ? 'Tahap sudah memiliki pengumpulan atau nilai.' : (tahap.length===1 ? 'Minimal satu tahap harus tersedia.' : 'Hapus tahap')" @click="if(tahap.length>1 && !item._deleteBlocked) tahap.splice(index,1)">Hapus Tahap</button></div>
                        </div></div></div>
                    </section>
                </template>
                <div><button type="button" class="mp-btn primary md ta-add-button" @click="tahap.push({_key:Date.now().toString(36)+Math.random().toString(36),nama:'',ketentuan:'',deadline:'',pengumpulan:true,dibuka_pada:'',parameter:[{nama:''}],lampiran:[],revisi_aktif:false,revisi_metode:'rata_rata',revisi_deadline:'',revisi_bobot_awal:50})">Tambah Tahap</button></div>
                <style>
                    .mp-app .ta-editor .ta-weight-table{table-layout:fixed;border-collapse:separate;border-spacing:0}
                    .mp-app .ta-editor .ta-weight-table th{padding:12px 16px!important;white-space:nowrap;border-bottom:1px solid var(--c-border);border-right:1px solid var(--c-border)}
                    .mp-app .ta-editor .ta-weight-table td{padding:14px 16px!important;font-size:13px;border-bottom:1px solid var(--c-border);border-right:1px solid var(--c-border);vertical-align:middle}
                    .mp-app .ta-editor .ta-weight-table .mp-tr{border-bottom:0}
                    .mp-app .ta-editor .ta-weight-table tr>:last-child{border-right:0}
                    .mp-app .ta-editor .ta-weight-table tbody:last-of-type tr:last-child td,.mp-app .ta-editor .ta-weight-table tbody:last-of-type .ta-weight-stage{border-bottom:0}
                    .mp-app .ta-editor .ta-weight-table .ta-weight-number{text-align:center;font-size:12px;font-weight:700;color:var(--c-fg-muted)}
                    .mp-app .ta-editor .ta-weight-table .ta-weight-stage{text-align:center;font-size:13px;font-weight:600;color:var(--c-fg);white-space:normal;overflow-wrap:anywhere}
                    .mp-app .ta-editor .ta-weight-table .ta-weight-input{width:48px;min-width:48px;min-height:32px;height:32px;padding:4px;font-size:13px;text-align:center;border:1px solid var(--c-border);border-radius:8px}
                    .mp-app .ta-editor .ta-weight-table .ta-weight-name{height:36px;min-height:36px;padding:8px 12px;font-size:13px;font-weight:600;border-radius:8px}
                    .mp-app .ta-editor .ta-weight-table .ta-weight-name[readonly]{padding:0;border:0;background:transparent;box-shadow:none;color:var(--c-fg)}
                    .mp-app .ta-editor .ta-weight-group{display:flex;align-items:center;justify-content:center;gap:8px}
                    .mp-app .ta-editor .ta-weight-group>span{font-size:12px;line-height:20px;color:var(--c-fg-muted)}
                    .mp-app .ta-editor .ta-weight-heading{display:inline-block;width:48px;flex-shrink:0;text-align:center}
                    .mp-app .ta-editor .ta-weight-table-wrap{overflow-x:auto;border:1px solid var(--c-border);border-radius:8px;margin-top:16px}
                    .mp-app .ta-editor .ta-weight-total,.mp-app .ta-editor .ta-weight-total strong{font-size:12px!important;font-weight:700!important;line-height:20px!important}
                    .mp-app .ta-editor>.ta-body{padding-bottom:0}
                    .mp-app .ta-editor .ta-weight-summary{padding:16px 0;display:flex;flex-direction:column;gap:12px}
                </style>
                <section class="ta-weight-section">
                    <div class="ta-divider ta-editor-section-heading"><h3 class="mp-card-title">Bobot Penilaian</h3><p class="ta-muted">Total bobot seluruh kolom tugas akhir harus 100%. Pengaturan ini juga tersedia di Absensi &amp; Nilai.</p></div>
                    <div class="ta-weight-table-wrap">
                        <table class="mp-table ta-weight-table" style="width:100%;min-width:560px">
                            <thead><tr><th class="mp-th" style="width:80px;text-align:center">NO</th><th class="mp-th" style="width:20%;text-align:center">TAHAP</th><th class="mp-th">NAMA KOLOM PENILAIAN</th><th class="mp-th" style="width:120px;text-align:center"><span class="ta-weight-group"><span class="ta-weight-heading">BOBOT</span><span style="visibility:hidden" aria-hidden="true">%</span></span></th></tr></thead>
                            <template x-for="(stage,stageIndex) in tahap" :key="stage._key || stage.id">
                                <tbody>
                                    <template x-for="(parameter,parameterIndex) in stage.parameter" :key="parameter.id || parameter._key || parameterIndex">
                                        <tr class="mp-tr">
                                            <td class="ta-weight-number" x-text="tahap.slice(0,stageIndex).reduce((sum,item)=>sum+item.parameter.length,0)+parameterIndex+1"></td>
                                            <template x-if="parameterIndex===0"><td class="ta-weight-stage" :rowspan="stage.parameter.length" x-text="stage.nama || 'Tahap ' + (stageIndex+1)"></td></template>
                                            <td><input class="mp-input ta-weight-name" x-model="parameter.nama" :readonly="stage._structureLocked" maxlength="150" placeholder="Contoh: Presentasi" :aria-label="'Nama kolom penilaian tahap ' + (stage.nama || stageIndex+1)"></td>
                                            <td><div class="ta-weight-group"><input class="mp-input ta-weight-input" type="number" :name="'tahap['+stageIndex+'][parameter]['+parameterIndex+'][bobot]'" x-model="parameter.bobot" :readonly="weightsLocked" min="1" max="100" step="1" placeholder="—" :aria-label="'Bobot ' + (parameter.nama || 'kolom penilaian')"><span>%</span></div></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </template>
                        </table>
                    </div>
                    <div class="ta-weight-summary">
                        <div class="ta-weight-total" :style="tahap.flatMap(stage=>stage.parameter).reduce((sum,parameter)=>sum+Number(parameter.bobot||0),0)===100 ? 'color:var(--c-success-200)' : 'color:var(--c-error-100)'">Total bobot: <strong x-text="tahap.flatMap(stage=>stage.parameter).reduce((sum,parameter)=>sum+Number(parameter.bobot||0),0)"></strong>%</div>
                        <div role="alert" class="mp-alert warning" style="margin:0" x-show="tahap.flatMap(stage=>stage.parameter).some(parameter=>!parameter.bobot) || tahap.flatMap(stage=>stage.parameter).reduce((sum,parameter)=>sum+Number(parameter.bobot||0),0)!==100">Bobot penilaian belum lengkap. Lengkapi seluruh bobot dengan total 100% agar rata-rata dapat dihitung.</div>
                        <div class="mp-alert warning" style="margin:0" x-show="weightsLocked">Bobot dikunci. Batalkan persetujuan atau publikasi nilai pada halaman Absensi &amp; Nilai sebelum melakukan perubahan.</div>
                    </div>
                </section>
            </div>
            <div class="mp-modal-footer">@if($ta)<button type="button" class="mp-btn error md" @click="$dispatch('ta-delete')">Hapus</button>@endif<button type="submit" class="mp-btn primary md">{{ $ta?->published ? 'Simpan Perubahan' : 'Simpan Draft' }}</button>@if(!$ta?->published)<button type="submit" name="publish" value="1" class="mp-btn primary md">Publikasikan</button>@endif</div>
        </form>
    </div></x-eoffice::manajemen-praktikum.ui.grade-accordion>

    @if($ta)
    <template x-teleport="body"><div x-show="deleteOpen" x-cloak @keydown.escape.window="deleteOpen=false" @click.self="deleteOpen=false" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px">
        <form method="POST" action="{{ route($base.'destroy',$routeArgs) }}" style="width:480px;max-width:100%;border-radius:14px;background:white;overflow:hidden">@csrf @method('DELETE')<input type="hidden" name="version" value="{{ $ta->version }}"><div style="padding:20px 22px;font-size:16px;font-weight:700;border-bottom:1px solid #DFE1E7">Hapus Tugas Akhir?</div><p style="padding:20px 22px;font-size:13px;color:#666D80">Ketentuan dan pengaturan tugas akhir akan dihapus. Tugas akhir yang sudah memiliki pengumpulan, nilai, atau absensi tidak dapat dihapus.</p><div style="padding:16px 22px;border-top:1px solid #DFE1E7;background:#FAFBFC;display:flex;gap:10px;justify-content:flex-end"><button type="button" class="mp-btn secondary md" @click="deleteOpen=false">Batal</button><button type="submit" class="mp-btn error md">Hapus</button></div></form>
    </div></template>
    @endif
</section>
@once
    @push('scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            window.taTextarea = function(element, fitContent = false) {
                let lastWidth=0;
                const size=()=>{ const width=element.getBoundingClientRect().width; if(!width || width===lastWidth) return; lastWidth=width;
                    const probe=document.createElement('textarea'); const style=getComputedStyle(element);
                    const minHeight=parseFloat(style.minHeight) || 42;
                    Object.assign(probe.style,{position:'fixed',visibility:'hidden',height:'auto',width:width+'px',font:style.font,lineHeight:style.lineHeight,padding:style.padding,boxSizing:'border-box',overflowY:'scroll'});
                    probe.value='Contoh ketentuan tugas akhir. '.repeat(200).slice(0,5010);
                    document.body.appendChild(probe);const maxHeight=Math.max(minHeight,probe.scrollHeight+parseFloat(style.borderTopWidth)+parseFloat(style.borderBottomWidth));probe.remove();
                    element.style.minHeight=minHeight+'px';
                    element.style.maxHeight=maxHeight+'px';
                    if(fitContent) {
                        element.style.height='auto';
                        const height=Math.min(maxHeight,Math.max(minHeight,element.scrollHeight+parseFloat(style.borderTopWidth)+parseFloat(style.borderBottomWidth)));
                        element.style.height=height+'px';
                    }
                };
                new ResizeObserver(size).observe(element);size();
            };
            window.taDatePicker = function(element, changed) {
                if (!window.flatpickr) return;
                element.type = 'text';
                element.placeholder = 'Pilih tanggal dan waktu...';
                const picker = flatpickr(element, {
                    enableTime:true, time_24hr:true, monthSelectorType:"static", dateFormat:'Y-m-d\\TH:i',
                    altInput:true, altFormat:'d/m/Y H:i', allowInput:false,
                    locale:{firstDayOfWeek:1, weekdays:{shorthand:['Min','Sen','Sel','Rab','Kam','Jum','Sab'],longhand:['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']},months:{shorthand:['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],longhand:['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']}},
onReady: function (selectedDates, dateStr, instance) {
                // Buat custom dropdown untuk ganti bulan secara estetik
                const monthDropdown = document.createElement('div');
                monthDropdown.className = 'custom-month-dropdown';

                const monthsStr = instance.l10n.months.longhand;
                monthsStr.forEach((month, index) => {
                    const div = document.createElement('div');
                    div.className = 'custom-month-item';
                    div.textContent = month;
                    div.addEventListener('click', (e) => {
                        e.stopPropagation();
                        instance.changeMonth(index, false); // set bulan
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
                    // Add simple arrow to element parent to avoid reset issue
                    const arrow = document.createElement('span');
                    arrow.className = 'custom-month-arrow';
                    arrow.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"></polyline></svg>`;
                    arrow.style.marginLeft = '4px';
                    arrow.style.cursor = 'pointer';
                    arrow.style.display = 'inline-flex';
                    arrow.style.alignItems = 'center';
                    monthElement.parentNode.insertBefore(arrow, monthElement.nextSibling);

                    // Wrapper trigger
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

                        // Scroll into view safely
                        const activeItem = monthDropdown.querySelector('.custom-month-item.active');
                        if (activeItem && monthDropdown.classList.contains('show')) {
                            monthDropdown.scrollTop = activeItem.offsetTop - 10;
                        }
                    };

                    monthElement.addEventListener('click', toggleDropdown);
                    arrow.addEventListener('click', toggleDropdown);
                }

                // Hide if clicked out
                document.addEventListener('click', (e) => {
                    if (monthDropdown.classList.contains('show') && !instance.monthNav.contains(e.target) && !monthDropdown.contains(e.target)) {
                        monthDropdown.classList.remove('show');
                        if (monthElement.nextSibling && monthElement.nextSibling.classList) {
                            monthElement.nextSibling.classList.remove('open');
                        }
                    }
                });
            },
                    onChange:(_, value)=>changed(value),
                });
                if(picker.altInput) { picker.altInput.placeholder='Pilih tanggal dan waktu...'; picker.altInput.disabled=element.disabled; picker.altInput.required=element.required; }
                const observer=new MutationObserver(()=>{if(picker.altInput){picker.altInput.disabled=element.disabled;picker.altInput.required=element.required;}});
                observer.observe(element,{attributes:true,attributeFilter:['disabled','required']});
                const destroy=picker.destroy.bind(picker);
                picker.destroy=()=>{observer.disconnect();destroy();};
            };
        </script>
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

    .flatpickr-days {
        width: 320px !important;
    }

    .dayContainer {
        width: 320px !important;
        min-width: 320px !important;
        max-width: 320px !important;
    }

    .flatpickr-calendar.hasTime .flatpickr-time {
        border-top: 1px solid #DFE1E7 !important;
        margin-top: 8px;
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

    /* Customizing the arrows */
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

    /* Color of the arrow triangles */
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
</style>
    @endpush
@endonce
