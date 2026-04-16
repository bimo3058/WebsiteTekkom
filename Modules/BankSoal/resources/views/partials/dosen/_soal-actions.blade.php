{{-- Shared action buttons for soal table rows --}}
<div class="row-actions" style="display: flex; gap: 5px; align-items: center;">
    @can('banksoal.view')
        <a href="{{ route('banksoal.soal.dosen.show', $soal->id) }}" class="action-btn" title="Lihat Detail" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none;"><i class="fas fa-eye"></i></a>
    @endcan

    @can('banksoal.edit')
        <a href="{{ route('banksoal.soal.dosen.edit', $soal->id) }}" class="action-btn" title="Edit Soal" style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none;"><i class="fas fa-pen"></i></a>
    @else
        <span class="action-btn" style="opacity: 0.3; cursor: not-allowed; display:inline-flex; align-items:center; justify-content:center;" title="Tidak ada izin edit">
            <i class="fas fa-pen"></i>
        </span>
    @endcan

    @can('banksoal.delete')
        <form action="{{ route('banksoal.destroy', $soal->id) }}" method="POST" style="display:inline-block; margin:0;" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="action-btn del" title="Hapus Soal" style="border:none; cursor:pointer;"><i class="fas fa-trash"></i></button>
        </form>
    @else
        <span class="action-btn" style="opacity: 0.3; cursor: not-allowed; display:inline-flex; align-items:center; justify-content:center;" title="Tidak ada izin hapus">
            <i class="fas fa-trash"></i>
        </span>
    @endcan
</div>
