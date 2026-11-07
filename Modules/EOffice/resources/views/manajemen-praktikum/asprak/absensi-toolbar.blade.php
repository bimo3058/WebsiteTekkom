@once
<style>
@media(max-width:900px){[data-absensi-toolbar]{flex-wrap:wrap!important}.mp-absensi-toolbar-controls{flex-wrap:wrap!important;flex-shrink:1!important;max-width:100%}}
</style>
@endonce
                {{-- Filter Header Row --}}
                <div data-absensi-toolbar
                    style="background:white;border:1px solid #DFE1E7;border-radius:12px 12px 0 0;margin-top:15px;padding:16px 20px; display:flex; flex-direction:row; flex-wrap:nowrap; align-items:center; justify-content:space-between; gap:16px; border-bottom:1px solid #DFE1E7;">

                    <div style="font-size: 15px; font-weight: 700; color: #0D0D12; white-space: nowrap; flex-shrink: 0;">
                        Absensi &amp; Nilai
                    </div>

                    <div class="mp-absensi-toolbar-controls"
                        style="display: flex; flex-direction: row; flex-wrap: nowrap; align-items: center; gap: 10px; flex-shrink: 0;">

                        <div style="position: relative;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); pointer-events:none;">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input type="text" x-model="globalSearch" placeholder="Cari nama / NIM..." class="mp-input"
                                style="padding-left: 32px; height: 36px; width: 210px; font-size: 13px;">
                        </div>

                        <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="globalKelompok" label="Semua Kelompok" searchPlaceholder="Cari kelompok..." />
                        <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="globalShift" label="Semua Shift" searchPlaceholder="Cari shift..." />

                        {{-- Simpan Button --}}
                        <button type="button" @click="simpanSemua" :disabled="!isDirty || isSaving" :style="{cursor:!isDirty || isSaving ? 'not-allowed' : 'pointer',pointerEvents:'auto'}"
                            :class="isDirty && !isSaving ? 'bg-[#0B266E] text-[#fff] hover:bg-[#081b4d]' : 'bg-[#F9FAFB] text-[#0B266E] border-[#DFE1E7]'"
                            style="height: 36px; padding: 0 16px; border-radius: 8px; border: 1px solid transparent; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                            <span x-text="isSaving ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>

                        <x-eoffice::manajemen-praktikum.ui.nilai-csv-picker :praktikum="$praktikum" :moduls="$modulDiampu" role="asprak" />

                    </div>
                </div>


