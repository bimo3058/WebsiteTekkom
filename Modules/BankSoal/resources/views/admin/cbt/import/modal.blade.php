<!-- Modal Import Data Ujian Komprehensif -->
<div x-show="importOpen" x-cloak @keydown.escape.window="closeImport()"
    class="fixed inset-0 z-[80] flex items-center justify-center p-4 sm:p-6"
    style="display: none;">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeImport()"></div>

    <div class="relative flex max-h-[90vh] w-full max-w-xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
        <!-- Header -->
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
            <div>
                <h3 class="text-[18px] font-semibold tracking-tight text-slate-800">Import Data Ujian Komprehensif</h3>
                <p class="mt-1.5 text-[12px] leading-relaxed text-slate-500">
                    Unggah rekap hasil tes dari sistem lama. Periode, jadwal, peserta, dan riwayat dibuat otomatis.
                </p>
            </div>
            <button type="button" @click="closeImport()" :disabled="importExecuting"
                class="text-slate-400 transition-colors hover:text-slate-600 disabled:opacity-50"
                aria-label="Tutup">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="flex-1 space-y-6 overflow-y-auto px-6 py-5">
            <template x-if="importError">
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-[13px] text-rose-700" x-text="importError"></div>
            </template>

            <!-- Step 1: upload -->
            <div x-show="!importPreview && !importResult" class="space-y-6">
                <section>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h4 class="text-[14px] font-semibold text-slate-800">Unggah file</h4>
                        <a href="{{ route('banksoal.admin.cbt.import.template-kosong') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-[12px] font-semibold text-primary transition-colors hover:bg-primary/5">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Unduh Template
                        </a>
                    </div>

                    <label x-show="!importFile" class="mt-3 flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/50 px-6 py-7 text-center transition-colors hover:border-primary/50 hover:bg-primary/5">
                        <svg class="mb-2 h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.9A5.5 5.5 0 1117 8a3.5 3.5 0 010 7h-1M12 12v9m0-9l-3 3m3-3l3 3" />
                        </svg>
                        <span class="text-[13px] font-semibold text-slate-700">Pilih file Excel</span>
                        <span class="mt-0.5 text-[11px] text-slate-400">.xlsx atau .xls, maksimal 10 MB</span>
                        <input type="file" accept=".xlsx,.xls" class="hidden" x-ref="importFileInput" @change="onImportFiles($event)">
                    </label>

                    <template x-if="importFile">
                        <div class="mt-3">
                            <div class="flex items-center gap-3 rounded-xl border px-4 py-3 transition-colors"
                                 :class="importFileError ? 'border-rose-300 bg-rose-50/30' : 'border-slate-200 bg-white'">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-[10px] font-bold tracking-wide text-rose-600">
                                    XLSX
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[13px] font-semibold text-slate-800" x-text="importFile.name"></p>
                                    <p class="mt-0.5 text-[12px] text-slate-500" x-text="formatFileSize(importFile.size)"></p>
                                </div>
                                <button type="button" @click="removeImportFile()"
                                    class="shrink-0 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-[12px] font-semibold text-slate-700 transition-colors hover:bg-slate-50">
                                    Hapus
                                </button>
                            </div>

                            <p x-show="importFileError" class="mt-2 text-[12px] font-medium text-rose-600" x-text="importFileError"></p>
                        </div>
                    </template>
                </section>

                <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-[11px] text-slate-400"
                       x-text="!importFile ? 'Pilih file untuk melanjutkan.' : (importFileError ? 'Hapus file untuk memilih file lain.' : 'File siap dipreview.')"></p>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="closeImport()"
                            class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-[13px] font-semibold text-slate-700 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="button" @click="previewImport()"
                            :disabled="importLoading || !importFile || importFileError"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-[13px] font-semibold text-white hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50">
                            <svg x-show="importLoading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="importLoading ? 'Memproses...' : 'Preview Data'"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: preview -->
            <div x-show="importPreview" class="space-y-4">
                <div>
                    <h4 class="text-[14px] font-semibold text-slate-800">Preview Import</h4>
                    <p class="mt-0.5 text-[12px] text-slate-500">
                        Periksa ringkasan berikut sebelum menyimpan. Data duplikat/tidak valid akan dilewati.
                    </p>
                </div>

                <template x-for="(file, index) in (importPreview?.files || [])" :key="index">
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <p class="text-[13px] font-semibold text-slate-800" x-text="file.name"></p>
                                <p class="mt-0.5 text-[11px] uppercase tracking-wide text-slate-400"
                                   x-text="file.type === 'legacy_rekap' ? 'Rekap Lama' : (file.type === 'template' ? 'Template Baru' : 'Tidak Dikenali')"></p>
                            </div>
                            <span x-show="file.already_imported"
                                  class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[10px] font-semibold text-amber-700">
                                Sudah pernah diimport
                            </span>
                        </div>

                        <template x-if="file.error">
                            <p class="mt-2 text-[12px] font-medium text-rose-600" x-text="file.error"></p>
                        </template>

                        <template x-for="(period, pIndex) in (file.periods || [])" :key="pIndex">
                            <div class="mt-3 rounded-lg border border-slate-100 bg-slate-50/60 p-3">
                                <p class="text-[13px] font-semibold text-slate-800" x-text="period.name"></p>
                                <p class="mt-0.5 text-[11px] text-slate-500">
                                    Sesi: <span x-text="period.session || '-'"></span>
                                </p>
                                <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-[11px] text-slate-600 sm:grid-cols-4">
                                    <span x-text="period.rows + ' baris'"></span>
                                    <span x-text="period.existing_students + ' terhubung akun'"></span>
                                    <span x-text="period.historical_students + ' peserta historis'"></span>
                                    <span x-text="period.pass + ' lulus / ' + period.fail + ' tidak lulus'"></span>
                                    <span x-text="period.blank + ' baris kosong'"></span>
                                    <span x-text="period.mismatch + ' status tidak konsisten'"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" @click="resetImport()"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-[13px] font-semibold text-slate-700 hover:bg-slate-50">
                        Kembali
                    </button>
                    <button type="button" @click="executeImport()" :disabled="importExecuting"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-[13px] font-semibold text-white hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50">
                        <svg x-show="importExecuting" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="importExecuting ? 'Menyimpan...' : 'Import Sekarang'"></span>
                    </button>
                </div>
            </div>

            <!-- Step 3: result -->
            <div x-show="importResult" class="space-y-4">
                <div>
                    <h4 class="text-[14px] font-semibold text-slate-800">Hasil Import</h4>
                    <p class="mt-0.5 text-[12px] text-slate-500">Ringkasan proses import per file.</p>
                </div>

                <template x-for="(result, index) in (importResult?.results || [])" :key="index">
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-[13px] font-semibold text-slate-800" x-text="result.file"></p>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold"
                                  :class="result.success ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
                                  x-text="result.success ? (result.skipped_file ? 'Dilewati' : 'Berhasil') : 'Gagal'"></span>
                        </div>
                        <template x-if="result.message">
                            <p class="mt-1 text-[12px] text-slate-500" x-text="result.message"></p>
                        </template>
                        <template x-if="result.success && !result.skipped_file">
                            <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-1 text-[11px] text-slate-600 sm:grid-cols-4">
                                <span x-text="result.imported + ' sesi diimport'"></span>
                                <span x-text="result.skipped + ' dilewati'"></span>
                                <span x-text="result.failed + ' gagal'"></span>
                                <span x-text="result.linked_participants + ' terhubung akun'"></span>
                                <span x-text="result.historical_participants + ' peserta historis'"></span>
                                <span x-text="result.periods_created + ' periode baru'"></span>
                                <span x-text="result.jadwals_created + ' jadwal baru'"></span>
                                <span x-text="result.mismatch + ' status tidak konsisten'"></span>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="flex justify-end border-t border-slate-100 pt-4">
                    <button type="button" @click="closeImport(); window.location.reload()"
                        class="rounded-lg bg-primary px-4 py-2 text-[13px] font-semibold text-white hover:bg-primary/90">
                        Tutup &amp; Muat Ulang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
