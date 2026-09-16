<x-eoffice::layouts.koordinator title="Persyaratan Dokumen">
    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Persyaratan Dokumen</span>
    @endsection

    @push('styles')
        <style>
            .sticky-header th {
                position: sticky;
                top: 0;
                z-index: 10;
            }

            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush

    <script>
        window.persyaratanAllStats = @json($groupedTemplates);
    </script>
    <div x-data="{
        selectedPeriode: '{{ $selectedPeriodeId }}',
        searchQuery: '',
        toast: { show: {{ session('success') || session('warning') ? 'true' : 'false' }}, message: '{{ session('success') ?? session('warning') ?? '' }}', type: '{{ session('success') ? 'success' : 'warning' }}' },
    }" x-init="if(toast.show) setTimeout(() => toast.show = false, 4000)">

        <!-- Toast Notification -->
        <div x-show="toast.show" x-cloak x-transition
            class="fixed top-20 right-6 lg:right-10 z-50 bg-white border shadow-lg rounded-2xl flex items-center gap-3 px-5 py-4 min-w-[300px]"
            :class="toast.type === 'success' ? 'border-emerald-100' : 'border-amber-100'">
            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                :class="toast.type === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'">
                <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg x-show="toast.type !== 'success'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-700" x-text="toast.message"></p>
            <button @click="toast.show = false" class="ml-auto text-slate-400 hover:text-slate-600"><svg class="w-4 h-4"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg></button>
        </div>

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 mb-8">
            <div>
                <h1 style="font-family:'Inter Tight',sans-serif; font-size:20px; font-weight:600; color:#0D0D12;">
                    Persyaratan Dokumen
                </h1>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <div style="min-width:220px;">
                    <div class="relative">
                        <select x-model="selectedPeriode"
                            @change="window.location.href = '?periode_id=' + selectedPeriode"
                            class="w-full bg-white border border-slate-200 text-slate-700 py-2.5 px-4 rounded-xl leading-tight focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-colors shadow-sm text-sm font-medium cursor-pointer">
                            @foreach ($periodes as $p)
                                <option value="{{ $p->id }}" {{ $p->is_active ? 'selected' : '' }}>Semester
                                    {{ $p->semester }} {{ $p->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <form action="{{ route('eoffice.kp.koordinator.persyaratan_dokumen.apply_default') }}" method="POST"
                    class="inline">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $selectedPeriodeId }}">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 bg-primary-500 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors cursor-pointer active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Gunakan konfigurasi bawaan
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Toolbar -->
            <div
                class="flex flex-col sm:flex-row justify-between items-center px-6 py-5 border-b border-slate-100 gap-4">
                <h2 class="text-sm font-bold text-slate-800">Tabel Dokumen</h2>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input x-model="searchQuery" type="text"
                            class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg whitespace-nowrap text-sm bg-slate-50 hover:bg-slate-100 focus:outline-none focus:bg-white focus:ring-1 focus:ring-primary-500/20 focus:border-primary-500 transition-colors"
                            placeholder="Search">
                    </div>

                    <!-- Filter & Sort Buttons -->
                    <div class="flex items-center gap-2 w-full sm:w-auto pt-1 sm:pt-0">
                        <button type="button"
                            class="flex-1 sm:flex-none inline-flex justify-center items-center px-3 py-2 border border-slate-200 shadow-sm text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors focus:outline-none focus:ring-1 focus:ring-primary-500/20 w-fit">
                            <svg class="mr-2 h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                        <button type="button"
                            class="flex-1 sm:flex-none inline-flex justify-center items-center px-3 py-2 border border-slate-200 shadow-sm text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors focus:outline-none focus:ring-1 focus:ring-primary-500/20 w-fit">
                            <svg class="mr-2 h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                            </svg>
                            Sort by
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table content will go here -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/50 sticky-header">
                        <tr>
                            <th scope="col"
                                class="py-3 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-16">
                                No</th>
                            <th scope="col"
                                class="py-3 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Fase</th>
                            <th scope="col"
                                class="py-3 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Syarat Dokumen</th>
                            <th scope="col"
                                class="py-3 px-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                Tipe Dokumen</th>
                            <th scope="col"
                                class="py-3 px-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-20">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach($groupedTemplates as $phaseKey => $item)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-4 whitespace-nowrap text-sm font-semibold text-slate-700">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <p class="text-sm font-bold text-slate-800">{{ strtoupper($item->tahap) }}</p>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold text-slate-700">{{ $item->syarat_count }} Dokumen</p>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <p class="text-sm text-slate-600">{{ $item->dokumens }}</p>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap text-center">
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                        <button type="button" @click="open = !open" @click.outside="open = false"
                                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                            </svg>
                                        </button>

                                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-50">
                                            <a href="{{ route('eoffice.kp.koordinator.persyaratan_dokumen.edit', ['phase' => $phaseKey, 'periode_id' => $selectedPeriodeId]) }}"
                                                class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                            <form method="POST"
                                                action="{{ route('eoffice.kp.koordinator.persyaratan_dokumen.destroy', $phaseKey) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus semua persyaratan dokumen fase {{ strtoupper($item->tahap) }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="periode_id" value="{{ $selectedPeriodeId }}">
                                                <button type="submit"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        @if(collect($groupedTemplates)->isEmpty())
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <svg class="h-10 w-10 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="text-sm font-semibold text-slate-600">Belum ada template</h3>
                                        <p class="mt-1 text-sm text-slate-500">Pilih "Gunakan konfigurasi bawaan" untuk
                                            mulai menyusun syarat dokumen.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Table Footer (No pagination needed for phase aggregation) -->
            <div
                class="px-6 py-4 flex flex-col sm:flex-row items-center justify-between border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-600 font-medium">Menampilkan {{ count($groupedTemplates) }} dari
                        {{ count($groupedTemplates) }} fase KP</span>
                </div>
            </div>
        </div>
    </div>
</x-eoffice::layouts.koordinator>