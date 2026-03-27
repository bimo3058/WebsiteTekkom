<x-app-layout>
<x-sidebar :user="auth()->user()">
    {{-- Tambahkan overflow-y-auto agar tidak menumpuk --}}
    <div class="h-screen overflow-y-auto bg-slate-50 p-6 custom-scrollbar">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                    <span class="w-1 h-6 bg-blue-600 rounded-full"></span>
                    System Monitor
                </h2>
            </div>

            {{-- Panggil Livewire Pulse di sini --}}
            <livewire:pulse />
        </div>
    </div>
</x-sidebar>
</x-app-layout>