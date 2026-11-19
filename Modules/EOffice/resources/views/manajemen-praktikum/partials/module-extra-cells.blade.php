@foreach($praktikum->komponen_modul ?? [] as $extra)
    <td class="mp-grade-history-cell {{ ($extraMode??'')==='asprak'?'mp-bulk-grade-cell':'' }}" @if(($extraMode ?? '')==='asprak') :class="{ 'is-selected': isCellSelected(r, '{{ $extra['id'] }}') }" @mousedown="startBulkSelection(r, '{{ $extra['id'] }}')" @mouseenter="updateBulkSelection(r, '{{ $extra['id'] }}')" @endif style="position:relative;padding:12px 32px 12px 16px;text-align:center;font-size:13px;background:{{ $loop->first ? '#F5F3FF' : '#F0FDFA' }}">
        @if(($extraMode ?? '')==='asprak')
            <x-eoffice::manajemen-praktikum.ui.grade-score-input :integer="true" :model="'r.'.$extra['id']" :label="'Nilai '.$extra['nama']" :disabled="!$praktikum->is_active" />
        @elseif(($extraMode ?? '')==='asprak-show')
            <input type="number" name="nilai[{{ $p->id }}][{{ $extra['id'] }}]" x-model="extras.{{ $extra['id'] }}" :disabled="!['hadir','terlambat'].includes(status)" min="0" max="100" step="0.01" aria-label="Nilai {{ $extra['nama'] }}" class="mp-input" style="width:80px;text-align:center">
        @else {{ number_format($njMap[$extra['id']] ?? 0,0,',','') }} @endif
        @if(($extraMode??'')==='asprak')<x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$modul->id" client-student="r.dp_id" :score-key="$extra['id']" :score-label="$extra['nama']" />
        @elseif(in_array($extraMode??'',['koor','dosen']))<x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$modul->id" :student-id="$dp->id" :score-key="$extra['id']" :score-label="$extra['nama']" :student-name="$dp->user?->name ?? 'Mahasiswa'" />@endif
        @if(($extraMode??'')==='asprak-show')<x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$modul->id" :student-id="$p->id" :score-key="$extra['id']" :score-label="$extra['nama']" :student-name="$p->user?->name ?? 'Mahasiswa'" />@endif
    </td>
@endforeach
