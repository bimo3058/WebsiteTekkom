<x-eoffice::layouts.koordinator title="Balancing Dosen">
    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Balancing Dosen</span>
    @endsection

    @push('styles')
        <style>
            .drag-over {
                background-color: #f1f5f9;
                border-color: #3b82f6;
            }

            .dragging {
                opacity: 0.5;
            }

            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush

    <div x-data="balancingApp()" x-cloak>

        <!-- Toast Notification -->
        <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-[-10px]" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-20 right-4 sm:right-8 z-50 bg-white border shadow-lg rounded-xl flex items-center gap-3 px-4 py-3 min-w-[300px]"
            :class="toast.type === 'success' ? 'border-emerald-200' : 'border-red-200'">
            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                :class="toast.type === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'">
                <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg x-show="toast.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-slate-900" x-text="toast.title"></p>
                <p class="text-xs text-slate-500" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false"
                class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Header & Actions -->
        <form action="{{ route('eoffice.kp.koordinator.balancing.store') }}" method="POST" id="balancingForm"
            @submit.prevent="submitForm">
            @csrf
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 tracking-tight">Balancing Dosen</h1>
                    <p class="text-sm text-slate-500 mt-1">Kelola pembagian mahasiswa KP ke dosen pembimbing secara
                        efisien.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="confirmAutoBalance()"
                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Auto Balancing
                    </button>
                    <button type="button" @click="submitForm('finalize')"
                        class="inline-flex items-center justify-center px-5 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors"
                        :disabled="isSaving" :class="{'opacity-75 cursor-not-allowed': isSaving}">
                        <svg x-show="!isSaving" class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg x-show="isSaving" class="animate-spin w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="isSaving ? 'Menyimpan...' : 'Finalisasi Balancing'"></span>
                    </button>
                </div>
            </div>

            <!-- Dosen Quota Cards -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Pengaturan Kuota Dosen</h2>
                    <div class="relative w-64">
                        <input type="text" x-model="searchDosen" placeholder="Cari dosen..."
                            class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm">
                        <svg class="absolute left-3 top-2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex overflow-x-auto pb-4 gap-4 snap-x">
                    <template x-for="dosen in filteredDosens" :key="dosen.id">
                        <div class="snap-start min-w-[240px] bg-white border rounded-xl p-4 shadow-sm flex-shrink-0 transition-all"
                            :class="getDosenStatusColor(dosen).border">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 border border-slate-200">
                                    <span class="text-sm font-bold text-slate-600"
                                        x-text="dosen.name.substring(0,2).toUpperCase()"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate" :title="dosen.name"
                                        x-text="dosen.name"></p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">NIDN: -</p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-lg font-bold" :class="getDosenStatusColor(dosen).text"
                                        x-text="dosen.mahasiswas.length"></span>
                                    <span class="text-xs font-medium text-slate-500">/ <span
                                            x-text="dosen.kuota_maksimal"></span> Mhs</span>
                                </div>
                                <div class="w-16">
                                    <input type="number" min="1" x-model.number="dosen.kuota_maksimal"
                                        class="w-full text-center py-1 px-2 text-xs border border-slate-200 rounded-md focus:ring-blue-500 focus:border-blue-500 bg-slate-50">
                                </div>
                            </div>
                            <div class="w-full h-1.5 bg-slate-100 rounded-full mt-3 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300"
                                    :class="getDosenStatusColor(dosen).bg"
                                    :style="`width: ${Math.min((dosen.mahasiswas.length / dosen.kuota_maksimal) * 100, 100)}%`">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Auto Balancing Banner -->
            <div
                class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-5 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <h3 class="font-bold text-blue-900">Sistem Auto Balancing</h3>
                    </div>
                    <p class="text-sm text-blue-700">Sistem akan membagi mahasiswa secara otomatis berdasarkan kuota
                        dosen yang tersedia.</p>
                    <div class="flex items-center gap-4 mt-3 text-xs font-medium">
                        <span
                            class="px-2.5 py-1 bg-white rounded-md text-blue-800 border border-blue-200 shadow-sm"><span
                                x-text="unassignedStudents.length"></span> Mahasiswa belum mendapat dosen</span>
                        <span
                            class="px-2.5 py-1 bg-white rounded-md text-indigo-800 border border-indigo-200 shadow-sm"><span
                                x-text="totalSisaKuota"></span> Total kuota tersedia</span>
                    </div>
                </div>
                <button type="button" @click="confirmAutoBalance()"
                    class="shrink-0 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center">
                    Jalankan Auto Balancing
                </button>
            </div>

            <!-- Main Balancing Workspace (2 Columns) -->
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Left Col: Unassigned Students -->
                <div class="w-full lg:w-1/3 flex flex-col gap-4">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col h-[600px]">
                        <div class="p-4 border-b border-slate-100">
                            <h3 class="font-bold text-slate-800 flex items-center justify-between">
                                Mahasiswa Belum Dapat Dosen
                                <span class="bg-slate-100 text-slate-600 py-0.5 px-2.5 rounded-full text-xs"
                                    x-text="unassignedStudents.length"></span>
                            </h3>
                            <div class="mt-3 relative">
                                <input type="text" x-model="searchStudent" placeholder="Cari nama/NIM..."
                                    class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 overflow-y-auto p-3 bg-slate-50/50 space-y-2"
                            @dragover.prevent="dragOverTarget = 'unassigned'" @dragleave="dragOverTarget = null"
                            @drop.prevent="onDropUnassigned()"
                            :class="{'bg-blue-50 border-2 border-dashed border-blue-300 rounded-lg m-2': dragOverTarget === 'unassigned'}">
                            <template x-if="filteredUnassigned.length === 0">
                                <div
                                    class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400">
                                    <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm font-medium">Semua mahasiswa yang dicari sudah ter-assign.</p>
                                </div>
                            </template>
                            <template x-for="mhs in filteredUnassigned" :key="mhs.id">
                                <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm cursor-move hover:border-blue-300 hover:shadow-md transition-all group"
                                    draggable="true" @dragstart="startDrag(mhs, 'unassigned', null)"
                                    @dragend="endDrag()">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors"
                                                x-text="mhs.nama_mahasiswa"></p>
                                            <p class="text-xs text-slate-500 mt-0.5" x-text="mhs.nim"></p>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium"
                                            :class="getBadgeClass(mhs.status)" x-text="getBadgeText(mhs.status)"></span>
                                    </div>
                                    <p class="text-[11px] font-semibold text-blue-700 mt-2 line-clamp-1 border-t border-slate-50 pt-2"
                                        :title="mhs.rencana_tempat"
                                        x-text="mhs.rencana_tempat || 'Belum ada tempat KP'"></p>
                                    <p class="text-[10px] text-slate-500 line-clamp-1" :title="mhs.rencana_judul"
                                        x-text="mhs.rencana_judul || 'Belum ada rencana judul'"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Right Col: Dosen Drop Targets -->
                <div class="w-full lg:w-2/3">
                    <div class="bg-slate-100/50 rounded-xl p-4 h-[600px] overflow-y-auto border border-slate-200/60">
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            <template x-for="dosen in filteredDosens" :key="dosen.id">
                                <div class="bg-white rounded-xl border shadow-sm flex flex-col h-72 transition-all"
                                    :class="[getDosenStatusColor(dosen).border, dragOverTarget === dosen.id ? 'ring-2 ring-blue-400 shadow-md scale-[1.02]' : '']"
                                    @dragover.prevent="dragOverTarget = dosen.id" @dragleave="dragOverTarget = null"
                                    @drop.prevent="onDropDosen(dosen)">
                                    <div
                                        class="p-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 rounded-t-xl">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div
                                                class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-bold text-slate-600"
                                                    x-text="dosen.name.substring(0,2).toUpperCase()"></span>
                                            </div>
                                            <div class="truncate">
                                                <p class="text-sm font-bold text-slate-800 truncate" :title="dosen.name"
                                                    x-text="dosen.name"></p>
                                                <div class="flex items-center gap-1 mt-0.5">
                                                    <div class="w-1.5 h-1.5 rounded-full"
                                                        :class="getDosenStatusColor(dosen).bg"></div>
                                                    <p class="text-[10px] text-slate-500 font-medium"><span
                                                            x-text="dosen.mahasiswas.length"></span> / <span
                                                            x-text="dosen.kuota_maksimal"></span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-1 p-2 overflow-y-auto space-y-2 bg-slate-50/30">
                                        <template x-if="dosen.mahasiswas.length === 0">
                                            <div class="h-full flex items-center justify-center">
                                                <p
                                                    class="text-[11px] text-slate-400 text-center px-4 border-2 border-dashed border-slate-200 rounded-lg w-full py-6">
                                                    Tarik mahasiswa ke sini</p>
                                            </div>
                                        </template>
                                        <template x-for="mhs in dosen.mahasiswas" :key="mhs.id">
                                            <div class="bg-white p-2.5 rounded-lg border border-slate-200 shadow-sm text-sm cursor-move hover:border-slate-300 transition-colors group relative"
                                                draggable="true" @dragstart="startDrag(mhs, 'dosen', dosen.id)"
                                                @dragend="endDrag()">
                                                <div class="flex justify-between items-start pr-6">
                                                    <div class="truncate">
                                                        <p class="text-xs font-bold text-slate-800 truncate"
                                                            :title="mhs.nama_mahasiswa" x-text="mhs.nama_mahasiswa"></p>
                                                        <div class="flex items-center gap-2 mt-0.5">
                                                            <p class="text-[10px] text-slate-500" x-text="mhs.nim"></p>
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider"
                                                                :class="getBadgeClass(mhs.status)"
                                                                x-text="getBadgeText(mhs.status)"></span>
                                                        </div>
                                                        <p class="text-[10px] text-blue-600 font-semibold truncate mt-1"
                                                            :title="mhs.rencana_tempat" x-text="mhs.rencana_tempat"></p>
                                                    </div>
                                                </div>
                                                <button type="button" @click.stop="removeFromDosen(dosen.id, mhs)"
                                                    class="absolute top-2 right-2 text-slate-400 hover:text-red-500 transition-colors"
                                                    title="Hapus dari dosen ini">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                                <input type="hidden" :name="`assignments[${mhs.id}][kp_id]`"
                                                    :value="mhs.id">
                                                <input type="hidden" :name="`assignments[${mhs.id}][dosen_id]`"
                                                    :value="dosen.id">
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Statistics -->
            <div class="mt-8 grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs text-slate-500 font-medium mb-1">Total Mhs KP</p>
                    <p class="text-xl font-bold text-slate-800" x-text="totalMahasiswa"></p>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs text-slate-500 font-medium mb-1">Sudah Penempatan</p>
                    <p class="text-xl font-bold text-blue-600" x-text="totalAssigned"></p>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs text-slate-500 font-medium mb-1">Belum Penempatan</p>
                    <p class="text-xl font-bold text-orange-500" x-text="unassignedStudents.length"></p>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs text-slate-500 font-medium mb-1">Total Dosen</p>
                    <p class="text-xl font-bold text-slate-800" x-text="dosens.length"></p>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs text-slate-500 font-medium mb-1">Sisa Kuota Global</p>
                    <p class="text-xl font-bold text-emerald-600" x-text="totalSisaKuota"></p>
                </div>
            </div>

            <!-- Modal Auto Balance Confirmation -->
            <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showConfirmModal = false"
                    x-transition.opacity></div>
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative z-10"
                    x-show="showConfirmModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="p-6">
                        <div
                            class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 text-center mb-2">Jalankan Auto Balancing?</h3>
                        <p class="text-sm text-slate-500 text-center mb-6">Sistem akan mendistribusikan <span
                                class="font-bold text-slate-800" x-text="unassignedStudents.length"></span> mahasiswa ke
                            dosen yang kuotanya masih tersedia.</p>
                        <div class="flex gap-3">
                            <button type="button" @click="showConfirmModal = false"
                                class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-colors">Batal</button>
                            <button type="button" @click="executeAutoBalance()"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">Jalankan</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div
                class="mt-6 flex justify-end gap-3 sticky bottom-0 bg-slate-50/80 backdrop-blur-sm p-4 border-t border-slate-200 -mx-6 lg:-mx-8">
                <button type="button" @click="submitForm('finalize')"
                    class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-sm transition-all text-sm"
                    :disabled="isSaving" :class="{'opacity-75 cursor-not-allowed': isSaving}">
                    <span x-text="isSaving ? 'Menyimpan...' : 'Finalisasi Balancing'"></span>
                </button>
            </div>

        </form>

        <!-- Confirm Finalize Modal -->
        <div x-show="showFinalizeModal" class="relative z-50" aria-labelledby="modal-title" role="dialog"
            aria-modal="true" style="display: none;">
            <div x-show="showFinalizeModal" x-transition.opacity
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showFinalizeModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        @click.away="showFinalizeModal = false"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-base font-bold leading-6 text-slate-900" id="modal-title">Finalisasi
                                        Balancing</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-slate-500">Mahasiswa yang sudah difinalisasi akan tampil
                                            pada role dosen pembimbing dan mahasiswa. Lanjutkan?</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100">
                            <button type="button" @click="submitForm('finalize'); showFinalizeModal = false"
                                class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">Lanjutkan</button>
                            <button type="button" @click="showFinalizeModal = false"
                                class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function balancingApp() {
                return {
                    sidebarOpen: true,
                    unassignedStudents: @json($unassignedStudents),
                    dosens: @json($dosens),
                    searchStudent: '',
                    searchDosen: '',
                    isSaving: false,
                    draggedStudent: null,
                    sourceType: null,
                    sourceId: null,
                    dragOverTarget: null,
                    showConfirmModal: false,
                    showFinalizeModal: false,
                    toast: { show: false, type: 'success', title: '', message: '' },

                    init() {
                        this.dosens.forEach(d => {
                            d.kuota_maksimal = parseInt(d.kuota_maksimal) || 10;
                            if (!d.mahasiswas) d.mahasiswas = [];
                        });
                    },

                    showToast(type, title, message) {
                        this.toast.type = type;
                        this.toast.title = title;
                        this.toast.message = message;
                        this.toast.show = true;
                        setTimeout(() => { this.toast.show = false; }, 3000);
                    },

                    get filteredUnassigned() {
                        if (!this.searchStudent) return this.unassignedStudents;
                        const lowerSearch = this.searchStudent.toLowerCase();
                        return this.unassignedStudents.filter(s =>
                            (s.nama_mahasiswa && s.nama_mahasiswa.toLowerCase().includes(lowerSearch)) ||
                            (s.nim && s.nim.toLowerCase().includes(lowerSearch))
                        );
                    },

                    get filteredDosens() {
                        if (!this.searchDosen) return this.dosens;
                        const lowerSearch = this.searchDosen.toLowerCase();
                        return this.dosens.filter(d => d.name.toLowerCase().includes(lowerSearch));
                    },

                    get totalMahasiswa() {
                        let total = this.unassignedStudents.length;
                        this.dosens.forEach(d => total += d.mahasiswas.length);
                        return total;
                    },

                    get totalAssigned() {
                        let total = 0;
                        this.dosens.forEach(d => total += d.mahasiswas.length);
                        return total;
                    },

                    get totalSisaKuota() {
                        let sisa = 0;
                        this.dosens.forEach(d => {
                            let s = parseInt(d.kuota_maksimal) - d.mahasiswas.length;
                            if (s > 0) sisa += s;
                        });
                        return sisa;
                    },

                    getDosenStatusColor(dosen) {
                        const max = parseInt(dosen.kuota_maksimal);
                        const current = dosen.mahasiswas.length;
                        const ratio = current / max;
                        if (ratio >= 1) return { border: 'border-red-200', bg: 'bg-red-500', text: 'text-red-600' };
                        if (ratio >= 0.8) return { border: 'border-amber-200', bg: 'bg-amber-400', text: 'text-amber-600' };
                        return { border: 'border-emerald-200', bg: 'bg-emerald-500', text: 'text-emerald-600' };
                    },

                    startDrag(mhs, type, srcId) { this.draggedStudent = mhs; this.sourceType = type; this.sourceId = srcId; },
                    endDrag() { this.draggedStudent = null; this.sourceType = null; this.sourceId = null; this.dragOverTarget = null; },

                    onDropUnassigned() {
                        if (!this.draggedStudent || this.sourceType === 'unassigned') { this.endDrag(); return; }
                        if (this.sourceType === 'dosen') {
                            const srcDosen = this.dosens.find(d => d.id === this.sourceId);
                            if (srcDosen) { srcDosen.mahasiswas = srcDosen.mahasiswas.filter(s => s.id !== this.draggedStudent.id); this.draggedStudent.status = 'belum'; this.unassignedStudents.push(this.draggedStudent); }
                        }
                        this.endDrag();
                    },

                    onDropDosen(targetDosen) {
                        if (!this.draggedStudent) { this.endDrag(); return; }
                        if (targetDosen.mahasiswas.length >= parseInt(targetDosen.kuota_maksimal)) { this.showToast('error', 'Kuota Penuh', `Kuota untuk dosen ${targetDosen.name} sudah penuh!`); this.endDrag(); return; }
                        if (this.sourceType === 'unassigned') { this.unassignedStudents = this.unassignedStudents.filter(s => s.id !== this.draggedStudent.id); this.draggedStudent.status = 'pending'; targetDosen.mahasiswas.push(this.draggedStudent); }
                        else if (this.sourceType === 'dosen') {
                            if (this.sourceId === targetDosen.id) { this.endDrag(); return; }
                            const srcDosen = this.dosens.find(d => d.id === this.sourceId);
                            if (srcDosen) { srcDosen.mahasiswas = srcDosen.mahasiswas.filter(s => s.id !== this.draggedStudent.id); this.draggedStudent.status = 'pending'; targetDosen.mahasiswas.push(this.draggedStudent); }
                        }
                        this.endDrag();
                    },

                    removeFromDosen(dosenId, mhs) {
                        const dosen = this.dosens.find(d => d.id === dosenId);
                        if (dosen) { dosen.mahasiswas = dosen.mahasiswas.filter(s => s.id !== mhs.id); mhs.status = 'belum'; this.unassignedStudents.push(mhs); }
                    },

                    confirmAutoBalance() {
                        if (this.unassignedStudents.length === 0) { this.showToast('error', 'Tidak ada mahasiswa', 'Semua mahasiswa sudah mendapatkan dosen pembimbing.'); return; }
                        if (this.totalSisaKuota < this.unassignedStudents.length) { this.showToast('error', 'Kuota tidak mencukupi', `Sisa kuota (${this.totalSisaKuota}) kurang dari jumlah mahasiswa (${this.unassignedStudents.length}). Silakan tambah kuota dosen.`); return; }
                        this.showConfirmModal = true;
                    },

                    executeAutoBalance() {
                        this.showConfirmModal = false;
                        let unassigned = [...this.unassignedStudents];
                        let assignedCount = 0;

                        const findDosenWithSameCompany = (companyName) => {
                            if (!companyName || companyName === 'Belum ada tempat KP' || companyName.trim() === '') return null;
                            const normalizedCompany = companyName.toLowerCase().trim();
                            for (let d of this.dosens) {
                                if (d.mahasiswas.length >= parseInt(d.kuota_maksimal)) continue;
                                let hasCompany = d.mahasiswas.some(m => m.rencana_tempat && m.rencana_tempat.toLowerCase().trim() === normalizedCompany);
                                if (hasCompany) return d;
                            }
                            return null;
                        };

                        while (unassigned.length > 0) {
                            let student = unassigned.shift();
                            let targetDosen = findDosenWithSameCompany(student.rencana_tempat);

                            if (!targetDosen) {
                                let availableDosens = [...this.dosens].filter(d => d.mahasiswas.length < parseInt(d.kuota_maksimal));
                                if (availableDosens.length === 0) {
                                    unassigned.unshift(student);
                                    break;
                                }
                                availableDosens.sort((a, b) => {
                                    return (parseInt(b.kuota_maksimal) - b.mahasiswas.length) - (parseInt(a.kuota_maksimal) - a.mahasiswas.length);
                                });
                                targetDosen = availableDosens[0];
                            }

                            student.status = 'draft';
                            targetDosen.mahasiswas.push(student);
                            this.unassignedStudents = this.unassignedStudents.filter(s => s.id !== student.id);
                            assignedCount++;
                        }
                        if (assignedCount > 0) { this.showToast('success', 'Auto Balancing Selesai', `${assignedCount} mahasiswa berhasil didistribusikan secara cerdas. Klik "Finalisasi" untuk menyimpan.`); }
                    },

                    getBadgeClass(status) {
                        if (status === 'finalized') return 'bg-indigo-100 text-indigo-700 border-indigo-200 border';
                        if (status === 'pending') return 'bg-amber-100 text-amber-700 border-amber-200 border';
                        return 'bg-slate-100 text-slate-600 border-slate-200 border';
                    },

                    getBadgeText(status) {
                        if (status === 'finalized') return 'Ter-plot';
                        if (status === 'pending') return 'Belum Disimpan';
                        return 'Belum';
                    },

                    submitForm(action = 'draft') {
                        this.isSaving = true;
                        const payload = { dosens: JSON.stringify(this.dosens), action: action, _token: document.querySelector('meta[name="csrf-token"]').content };
                        fetch(document.getElementById('balancingForm').action, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) })
                            .then(response => response.json())
                            .then(data => {
                                this.isSaving = false;
                                if (data.success) { this.showToast('success', 'Berhasil', data.message || 'Perubahan berhasil disimpan!'); if (action === 'finalize') { setTimeout(() => window.location.reload(), 1500); } }
                                else { this.showToast('error', 'Gagal', data.message || 'Terjadi kesalahan saat menyimpan.'); }
                            })
                            .catch(error => { this.isSaving = false; console.error('Error saving:', error); this.showToast('error', 'Gagal', 'Terjadi kesalahan sistem saat menghubungi server.'); });
                    },
                }
            }
        </script>

        @if(session('success'))
            <script>
                document.addEventListener('alpine:init', () => {
                    setTimeout(() => {
                        const app = document.querySelector('[x-data="balancingApp()"]').__x.$data;
                        app.showToast('success', 'Berhasil', '{{ session('success') }}');
                    }, 100);
                });
            </script>
        @endif
    @endpush

</x-eoffice::layouts.koordinator>