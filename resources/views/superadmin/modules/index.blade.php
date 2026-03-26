<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-8">
        <div class="max-w-6m mx-auto">

            {{-- Header --}}
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Manajemen Modul</h1>
                    <p class="text-slate-500 text-sm mt-0.5">Kelola konfigurasi dan status modul sistem</p>
                </div>
                <a href="{{ route('superadmin.dashboard') }}"
                   class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:border-slate-300 px-4 py-2 rounded-lg transition-all font-medium shadow-sm">
                    <span class="material-symbols-outlined" style="font-size:16px">arrow_back</span>
                    Kembali
                </a>
            </div>

            {{-- Module Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($modules as $module)
                    {{-- Panggil Card dari file terpisah --}}
                    @include('superadmin.modules._card', ['module' => $module])
                @endforeach
            </div>
        </div>
    </div>

    {{-- Render Modals (Uncomment ini agar modal berfungsi) --}}
    @foreach($modules as $module)
        @include('superadmin.modules._modal_manage', ['module' => $module]) 
    @endforeach

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if(modal) {
                modal.classList.remove('hidden');
                modal.classList.add('modal-active');
                document.body.classList.add('modal-open');
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if(modal) {
                modal.classList.add('hidden');
                modal.classList.remove('modal-active');
                document.body.classList.remove('modal-open');
            }
        }
    </script>
</x-sidebar>
</x-app-layout>