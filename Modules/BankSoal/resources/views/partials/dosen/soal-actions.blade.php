{{-- Shared action buttons for soal table rows --}}
<div class="flex items-center gap-1">
    @can('banksoal.view')
        <a href="{{ route('banksoal.soal.dosen.show', $soal->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 hover:text-blue-600 rounded-lg transition-colors" title="Lihat Detail">
            <i class="fas fa-eye text-sm"></i>
        </a>
    @endcan

    @can('banksoal.edit')
        <a href="{{ route('banksoal.soal.dosen.edit', $soal->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 hover:text-amber-600 rounded-lg transition-colors" title="Edit Soal">
            <i class="fas fa-pen text-sm"></i>
        </a>
    @else
        <span class="inline-flex items-center justify-center w-8 h-8 text-slate-300 cursor-not-allowed" title="Tidak ada izin edit">
            <i class="fas fa-pen text-sm"></i>
        </span>
    @endcan

    @can('banksoal.delete')
        <form action="{{ route('banksoal.destroy', $soal->id) }}" method="POST" class="m-0 inline-block" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 hover:text-red-600 rounded-lg transition-colors border-0 cursor-pointer bg-transparent" title="Hapus Soal">
                <i class="fas fa-trash text-sm"></i>
            </button>
        </form>
    @else
        <span class="inline-flex items-center justify-center w-8 h-8 text-slate-300 cursor-not-allowed" title="Tidak ada izin hapus">
            <i class="fas fa-trash text-sm"></i>
        </span>
    @endcan
</div>
