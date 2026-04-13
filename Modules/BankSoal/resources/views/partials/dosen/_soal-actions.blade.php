{{-- Shared action buttons for soal table rows --}}
<div class="row-actions">
    @can('banksoal.view')
        <button class="action-btn" title="Lihat Detail"><i class="fas fa-eye"></i></button>
    @endcan

    @can('banksoal.edit')
        <button class="action-btn" title="Edit Soal"><i class="fas fa-pen"></i></button>
    @else
        <span class="action-btn" style="opacity: 0.3; cursor: not-allowed;" title="Tidak ada izin edit">
            <i class="fas fa-pen"></i>
        </span>
    @endcan

    @can('banksoal.delete')
        <button class="action-btn del" title="Hapus Soal"><i class="fas fa-trash"></i></button>
    @else
        <span class="action-btn" style="opacity: 0.3; cursor: not-allowed;" title="Tidak ada izin hapus">
            <i class="fas fa-trash"></i>
        </span>
    @endcan
</div>
