@props([
    'id',
    'title' => '',
    'subtitle' => '',
    'maxWidth' => 'max-w-xl'
])

<div id="{{ $id }}" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeModal('{{ $id }}')"></div>

    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full {{ $maxWidth }} transform overflow-hidden rounded-3xl bg-white shadow-2xl transition-all animate-popup">
            <!-- Modal Header -->
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-white relative z-10">
                <div>
                    @if($title)
                        <h3 class="text-xl font-bold text-slate-900" id="modal-title">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="text-sm text-slate-500 mt-1">{{ $subtitle }}</p>
                    @endif
                </div>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-50 hover:text-slate-600 transition-all" onclick="closeModal('{{ $id }}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="px-8 py-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
