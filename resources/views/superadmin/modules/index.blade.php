<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-6">
        <div class="max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">System Modules</h1>
                    <p class="text-slate-500 text-[11px] mt-0.5 font-medium">
                        Total <span class="text-blue-600">{{ $modules->count() }}</span> modul terintegrasi dalam ekosistem
                    </p>
                </div>
                <a href="{{ route('superadmin.dashboard') }}"
                   class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 font-bold px-3 py-1.5 rounded-lg transition-all text-[11px] border border-slate-200 shadow-sm">
                    <span class="material-symbols-outlined" style="font-size:16px">arrow_back</span>
                    Dashboard
                </a>
            </div>

            {{-- Module Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach($modules as $module)
                    @include('superadmin.modules._card', ['module' => $module])
                @endforeach
            </div>
        </div>
    </div>

    {{-- Render Modals --}}
    @foreach($modules as $module)
        @include('superadmin.modules._modal_manage', ['module' => $module]) 
    @endforeach

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if(modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if(modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto';
            }
        }
    </script>
</x-sidebar>
</x-app-layout>