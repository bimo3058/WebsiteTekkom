@php
    $taWeightsLocked=$grades->contains(fn($grade)=>$grade->disetujui_koor || $grade->disetujui_dosen);
    $palette=[['#EEF2FF','#EEF2FF','#4338CA'],['#F0FDF4','#F0FDF4','#008B32'],['#FEFCE8','#FEFCE8','#A16207'],['#FFF7ED','#FFF7ED','#EA580C']];
    $columns=collect($ta->tahap)->flatMap(fn($s)=>$ta->componentsFor($s['id']))->values();
    $validWeights=$ta->validWeights();
@endphp
@if(!$validWeights)
    <div role="alert" style="padding:12px 20px;background:#FFFBEB;color:#92400E;font-size:13px">Bobot penilaian belum lengkap atau totalnya belum 100%. Koordinator perlu mengatur dan menyimpan bobot sebelum rata-rata dihitung dan nilai dipublikasikan.</div>
@endif
<div x-data="{page:1,perPage:10,get count(){return this.visibleRows.length},rows:{{ json_encode($praktikan->values()->map(fn($s,$i)=>['id'=>$i,'kel'=>$s->kelompok ?? '','shf'=>$s->shift ?? '','search'=>mb_strtolower(($s->user?->name ?? '').' '.($s->user?->student?->student_number ?? ''))])->all()) }},get totalPages(){return Math.max(1,Math.ceil(this.count/this.perPage))},get visibleRows(){return this.rows.filter(r=>(typeof globalSearch==='undefined'||r.search.includes(globalSearch.toLowerCase())) &amp;&amp; (typeof globalKelompok==='undefined'||globalKelompok===''||r.kel===globalKelompok) &amp;&amp; (typeof globalShift==='undefined'||globalShift===''||r.shf===globalShift))},get paginatedRows(){return this.visibleRows.slice((this.page-1)*this.perPage,this.page*this.perPage)},init(){['globalSearch','globalKelompok','globalShift'].forEach(key=>{if(typeof this[key]!=='undefined')this.$watch(key,()=>this.page=1)})},span(index,type){const rows=this.paginatedRows;const position=rows.findIndex(r=>r.id===index);if(position<0)return 0;const same=(a,b)=>a.shf===b.shf &amp;&amp; (type==='shf'||a.kel===b.kel);if(position>0 &amp;&amp; same(rows[position-1],rows[position]))return 0;let size=1;while(position+size<rows.length &amp;&amp; same(rows[position],rows[position+size]))size++;return size}}">
    <div style="overflow-x:auto"><table class="mp-table" style="min-width:1100px">
        <x-eoffice::manajemen-praktikum.ui.ta-grade-head :ta="$ta" :role="$role" :weights-locked="$taWeightsLocked" />
        <tbody>@forelse($praktikan as $student)
            @php
                $grade=$grades->get($student->id);
            @endphp
            @php
                $presence=$attendance->get($student->id);
            @endphp
            <tr class="mp-tr group" style="border-bottom:1px solid #DFE1E7" x-show="paginatedRows.some(r=>r.id==={{ $loop->index }})">
                <td class="group-hover:bg-gray-50 transition-all" style="padding:12px 16px;color:#666D80;font-size:13px;text-align:center;font-weight:700">{{ $loop->iteration }}</td>
                <td class="group-hover:bg-gray-50 transition-all" style="padding:12px 16px"><div style="display:flex;align-items:center;gap:10px"><div class="mp-av sky" style="width:28px;height:28px;font-size:11px;flex-shrink:0">{{ strtoupper(substr($student->user?->name ?? 'M',0,2)) }}</div><div><div style="font-weight:600;color:#0D0D12;font-size:13px">{{ $student->user?->name ?? '-' }}</div><div style="font-size:11.5px;color:#666D80;margin-top:2px">{{ $student->user?->email ?? '-' }}</div></div></div></td>
                <td class="group-hover:bg-gray-50 transition-all" style="padding:12px 16px;color:#0D0D12;font-size:12px;font-weight:600">{{ $student->user?->student?->student_number ?? $student->user?->email ?? '-' }}</td>
                <td x-show="span({{ $loop->index }},'kel')>0" :rowspan="span({{ $loop->index }},'kel')" class="group-hover:bg-gray-50 transition-all" style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;vertical-align:middle">{{ $student->kelompok ?? '—' }}</td>
                <td x-show="span({{ $loop->index }},'shf')>0" :rowspan="span({{ $loop->index }},'shf')" class="group-hover:bg-gray-50 transition-all" style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;vertical-align:middle">{{ $student->shift ?? '—' }}</td>
                <td class="group-hover:bg-gray-50 transition-all" style="padding:12px 16px;text-align:center;font-size:12px;color:#A4ABB8">{{ $presence ? ucwords(str_replace('_',' ',$presence->status)) : '—' }}</td>
                @foreach($columns as $assessmentColumn)
                    @php
                        $color=$palette[$loop->index % count($palette)];
                    @endphp
                    <td class="mp-grade-history-cell group-hover:brightness-95 transition-all" style="position:relative;padding:12px 32px 12px 16px;text-align:center;font-size:13px;color:#0D0D12;background:{{ $color[1] }}">
                        {{ isset($grade?->skor[$assessmentColumn['id']]) ? str_replace('.',',',(string)$grade->skor[$assessmentColumn['id']]) : '—' }}
                        @if(in_array($role,['koor','dosen']))<x-eoffice::manajemen-praktikum.ui.grade-history :entries="$grade?->riwayat ?? []" :score-key="$assessmentColumn['id']" :score-label="$assessmentColumn['nama']" :label="'Riwayat '.ucfirst(collect($ta->tahap)->firstWhere('id',$assessmentColumn['tahap_id'] ?? null)['nama'] ?? 'Tugas Akhir').' '.($student->user?->name ?? '')" />@endif
                    </td>
                @endforeach
                <td class="group-hover:bg-gray-50 transition-all" style="padding:12px 16px;font-size:12px;color:#666D80;border-left:1px solid #DFE1E7;text-align:{{ filled($presence?->keterangan) ? 'left' : 'center' }}">{{ filled($presence?->keterangan) ? $presence->keterangan : '—' }}</td>
                <td class="group-hover:bg-gray-50 transition-all" style="padding:12px 16px;text-align:center;color:#0D0D12;font-size:13px;font-weight:600;border-left:1px solid #DFE1E7" title="{{ $validWeights ? 'Nilai yang belum diisi dihitung sebagai 0.' : 'Bobot belum valid.' }}">{{ $validWeights && ($role!=='mahasiswa' || $grade) ? number_format($ta->average($grade?->skor ?? []),2,',','') : '—' }}</td>
            </tr>
        @empty <tr><td colspan="{{ count($ta->komponen)+8 }}" style="padding:32px;text-align:center">Belum ada praktikan.</td></tr>@endforelse
        <tr x-show="visibleRows.length===0" x-cloak><td colspan="{{ $columns->count()+8 }}" style="padding:24px 16px;text-align:center;font-size:13px;color:#666D80">Data tidak ditemukan.</td></tr></tbody>
    </table></div>
    <x-eoffice::manajemen-praktikum.ui.client-pagination />
</div>
