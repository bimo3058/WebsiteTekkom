@props(['praktikum', 'role', 'section' => 'modul', 'edit' => false, 'students' => null])
@if($praktikum)
    @php
        $ta = \Modules\EOffice\Models\TugasAkhir::where('praktikum_id', $praktikum->id)->first();
        $published = (bool) $ta?->published;
    @endphp
    @if($edit && $role === 'koor')
        @include('eoffice::manajemen-praktikum.tugas-akhir.content', ['praktikan'=>collect(), 'submissions'=>collect(), 'grades'=>collect(), 'attendance'=>collect()])
    @elseif(in_array($section, ['modul', 'tugas']))
        <section x-data="{ expanded: false }" style="flex-shrink:0;border:1px solid var(--c-border);border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.04);background:#fff;overflow:hidden">
            <button type="button" class="mp-grade-card-header" @click="expanded = !expanded" :aria-expanded="expanded" style="width:100%;border:0;background:transparent;text-align:left;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;cursor:pointer;color:inherit">
                <span><span style="display:block;font-size:16px;font-weight:700;color:#111827;margin-bottom:4px">Tugas Akhir</span><span style="display:block;font-size:13px;color:#666D80">Tipe tugas akhir: {{ $published ? (['individu'=>'Individu','kelompok'=>'Kelompok','kelompok_besar'=>'Kelompok besar'][$ta->tipe] ?? 'Belum ditentukan') : 'Belum dipublikasikan' }}</span>
                @if($published && $section === 'modul')<span style="display:block;font-size:12px;color:#6B7280;margin-top:4px">{{ $ta->created_at->translatedFormat('d M Y, H:i') }}@if($ta->updated_at->gt($ta->created_at)) <em>(Diedit {{ $ta->updated_at->translatedFormat('d M Y, H:i') }})</em>@endif</span>@endif
                </span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" style="transition:transform .2s;flex-shrink:0; margin-right:4px" :style="expanded ? 'transform:rotate(180deg)' : ''"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <x-eoffice::manajemen-praktikum.ui.grade-accordion state="expanded">
                @if(!$published)
                    <div style="padding:20px 24px 24px;border-top:1px solid #DFE1E7;font-size:14px;color:#6B7280;font-style:italic">{{ $section === 'modul' ? 'Ketentuan tugas akhir belum dipublikasikan.' : 'Tahapan tugas akhir belum dipublikasikan.' }}</div>
                @elseif($section === 'modul')
                    <div style="padding:20px 24px 24px;border-top:1px solid #DFE1E7">
                        <div style="font-size:14px;color:#374151;white-space:pre-wrap;line-height:1.5;margin-bottom:20px">{{ $ta->ketentuan }}</div>
                        @include('eoffice::manajemen-praktikum.tugas-akhir.attachments', ['files'=>$ta->lampiran ?? []])
                    </div>
                @else
                    @if($role === 'mahasiswa')
                        @php
                            $student=$praktikum->daftarPraktikan()->where('user_id',auth()->id())->first();
                            $submissions=$student ? \Modules\EOffice\Models\TaPengumpulan::where('tugas_akhir_id',$ta->id)->where('praktikan_id',$student->id)->get() : collect();
                            $stageGrades=$student ? \Modules\EOffice\Models\TaNilai::where('tugas_akhir_id',$ta->id)->where('praktikan_id',$student->id)->get() : collect();
                        @endphp
                    @endif
                    @foreach($ta->tahap as $stage)
                        <div x-data="{ open: false }" style="border-top:1px solid #DFE1E7">
                            <button type="button" @click="open = !open" :aria-expanded="open" style="width:100%;padding:14px 24px;border:0;background:#fff;display:flex;align-items:center;justify-content:space-between;gap:16px;text-align:left;cursor:pointer">
                                <span style="display:flex;align-items:center;gap:12px;flex-wrap:wrap"><span style="font-size:13px;font-weight:700">{{ $stage['nama'] }}</span>
                                    @if($role==='mahasiswa' && $student)<x-eoffice::manajemen-praktikum.ui.student-stage-status :ta="$ta" :stage="$stage" :submission="$submissions->firstWhere('tahap_id',$stage['id'])" :grade="$stageGrades->firstWhere('praktikan_id',$student->id)" />@endif
                                </span>
                                <span style="display:flex;align-items:center;gap:20px"><span style="font-size:12px;color:#9CA3AF">{{ ($stage['pengumpulan'] ?? true) && !empty($stage['deadline']) ? 'Tenggat: '.\Illuminate\Support\Carbon::parse($stage['deadline'])->format('d/m/Y, H:i') : 'Tanpa pengumpulan' }}</span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" :style="open ? 'transform:rotate(180deg)' : ''"><path d="m6 9 6 6 6-6"/></svg></span>
                            </button>
                            <x-eoffice::manajemen-praktikum.ui.grade-accordion state="open"><div style="border-top:1px solid #DFE1E7;padding:20px 24px 16px;background:#FAFBFC">
                                <div style="font-size:13px;color:#374151;white-space:pre-wrap;margin-bottom:20px">{{ $stage['ketentuan'] }}</div>
                                @if($role === 'mahasiswa')
                                    @include('eoffice::manajemen-praktikum.tugas-akhir.attachments',['files'=>$stage['lampiran']??[]])
                                    @if($stage['revisi_aktif']??false)@include('eoffice::manajemen-praktikum.tugas-akhir.revision-summary')@endif
                                    @if($student)@include('eoffice::manajemen-praktikum.tugas-akhir.student-submission')@endif
                                @else
                                @if($stage['revisi_aktif'] ?? false)
                                <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:20px;flex-wrap:wrap">
                                    <div style="flex:1;min-width:240px">@include('eoffice::manajemen-praktikum.tugas-akhir.revision-summary',['compact'=>true])</div>
                                <div style="display:flex;justify-content:flex-end;gap:8px;margin-left:auto"><a class="mp-btn primary sm" style="text-decoration:none;padding:8px 16px" href="{{ route('eoffice.manprak.'.$role.'.tugas-akhir.tugas', ['praktikum'=>$praktikum->id, 'tahap_id'=>$stage['id']]) }}">{{ $role === 'mahasiswa' ? 'Lihat Tugas' : (($stage['pengumpulan'] ?? true) ? 'Lihat Pengumpulan' : ($role === 'asprak' ? 'Input Nilai' : 'Lihat Nilai')) }}</a></div>
                                </div>
                                @if(count($stage['lampiran'] ?? []))<div style="margin-top:16px">@include('eoffice::manajemen-praktikum.tugas-akhir.attachments',['files'=>$stage['lampiran']])</div>@endif
                                @else
                                <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:20px;flex-wrap:wrap;margin-top:16px">
                                @include('eoffice::manajemen-praktikum.tugas-akhir.attachments', ['files'=>$stage['lampiran'] ?? []])
                                <div style="display:flex;justify-content:flex-end;gap:8px;margin-left:auto"><a class="mp-btn primary sm" style="text-decoration:none;padding:8px 16px" href="{{ route('eoffice.manprak.'.$role.'.tugas-akhir.tugas', ['praktikum'=>$praktikum->id, 'tahap_id'=>$stage['id']]) }}">{{ $role === 'mahasiswa' ? 'Lihat Tugas' : (($stage['pengumpulan'] ?? true) ? 'Lihat Pengumpulan' : ($role === 'asprak' ? 'Input Nilai' : 'Lihat Nilai')) }}</a></div>
                                </div>
                                @endif
                                @endif
                            </div></x-eoffice::manajemen-praktikum.ui.grade-accordion>
                        </div>
                    @endforeach
                @endif
            </x-eoffice::manajemen-praktikum.ui.grade-accordion>
        </section>
    @elseif($published)
        <div x-data="{ expanded: false }" style="background:white">
            <button type="button" class="mp-grade-card-header" @click="expanded=!expanded" :aria-expanded="expanded" style="width:100%;padding:16px 20px;border:0;background:white;display:flex;align-items:center;justify-content:space-between;text-align:left;cursor:pointer">
                <span><span style="display:block;font-size:15px;font-weight:700;color:#0D0D12">Tugas Akhir</span><span style="display:block;font-size:13px;color:#666D80;margin-top:3px">Tipe Tugas: {{ ['individu'=>'Individu','kelompok'=>'Kelompok','kelompok_besar'=>'Kelompok Besar'][$ta->tipe] ?? 'Belum ditentukan' }}</span></span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" style="transition:transform .2s" :style="expanded ? 'transform:rotate(180deg)' : ''"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <x-eoffice::manajemen-praktikum.ui.grade-accordion :separator="true">
                @php
                    $praktikan = $students ?? $praktikum->daftarPraktikan()->with('user.student')->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")->orderBy('created_at')->get();
                    $praktikan->loadMissing('user.student');
                    $grades = \Modules\EOffice\Models\TaNilai::where('tugas_akhir_id',$ta->id)->whereIn('praktikan_id',$praktikan->pluck('id'))->when($role === 'mahasiswa',fn($q) => $q->where('disetujui_koor',true)->where('disetujui_dosen',true))->get()->keyBy('praktikan_id');
                    $attendance = \Modules\EOffice\Models\TaAbsensi::where('tugas_akhir_id',$ta->id)->whereIn('praktikan_id',$praktikan->pluck('id'))->get()->keyBy('praktikan_id');
                    $submissions = \Modules\EOffice\Models\TaPengumpulan::where('tugas_akhir_id',$ta->id)->whereIn('praktikan_id',$praktikan->pluck('id'))->get();
                    $base = 'eoffice.manprak.'.$role.'.tugas-akhir.';
                    $routeArgs = ['praktikum'=>$praktikum->id];
                @endphp
                @if($role==='asprak')
                    @include('eoffice::manajemen-praktikum.tugas-akhir.rekap-asprak')
                @elseif(in_array($role,['koor','dosen','mahasiswa']))
                    @include('eoffice::manajemen-praktikum.tugas-akhir.rekap-inline')
                @else
                    @include('eoffice::manajemen-praktikum.tugas-akhir.penilaian', ['embedded'=>true])
                @endif
            </x-eoffice::manajemen-praktikum.ui.grade-accordion>
        </div>
    @endif
@endif
