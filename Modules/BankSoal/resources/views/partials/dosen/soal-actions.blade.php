{{-- Shared action menu for soal table rows (Three-dots dropdown) --}}
@php
    $canView = auth()->user()?->can('banksoal.view');
    $canEdit = auth()->user()?->can('banksoal.edit');
    $canDelete = auth()->user()?->can('banksoal.delete');
    $actionCount = collect([$canView, $canEdit, $canDelete])->filter()->count();
@endphp

@if ($actionCount > 1)
    {{-- Multiple actions: use three-dots menu --}}
    <x-ui.action-menu align="right">
        @can('banksoal.view')
            <a href="{{ route('banksoal.soal.dosen.show', $soal->id) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                <i class="fas fa-eye w-4 text-gray-500"></i>
                <span>Lihat Detail</span>
            </a>
        @endcan

        @can('banksoal.edit')
            <a href="{{ route('banksoal.soal.dosen.edit', $soal->id) }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                <i class="fas fa-pen w-4 text-gray-500"></i>
                <span>Edit Soal</span>
            </a>
        @endcan

        @can('banksoal.delete')
            <x-ui.dropdown-separator />
            <form action="{{ route('banksoal.destroy', $soal->id) }}" method="POST" class="m-0 block" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 border-0 cursor-pointer bg-transparent text-left">
                    <i class="fas fa-trash w-4"></i>
                    <span>Hapus Soal</span>
                </button>
            </form>
        @endcan
    </x-ui.action-menu>
@else
    {{-- Single action: show as direct link/button --}}
    <div class="inline-block">
        @can('banksoal.view')
            <a href="{{ route('banksoal.soal.dosen.show', $soal->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 hover:text-primary rounded-lg transition-colors" title="Lihat Detail">
                <i class="fas fa-eye text-sm"></i>
            </a>
        @endcan

        @can('banksoal.edit')
            <a href="{{ route('banksoal.soal.dosen.edit', $soal->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 hover:text-primary rounded-lg transition-colors" title="Edit Soal">
                <i class="fas fa-pen text-sm"></i>
            </a>
        @endcan

        @can('banksoal.delete')
            <form action="{{ route('banksoal.destroy', $soal->id) }}" method="POST" class="m-0 inline-block" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-slate-600 hover:bg-slate-100 hover:text-red-600 rounded-lg transition-colors border-0 cursor-pointer bg-transparent" title="Hapus Soal">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            </form>
        @endcan
    </div>
@endif
