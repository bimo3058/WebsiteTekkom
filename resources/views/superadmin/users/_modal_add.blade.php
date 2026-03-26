<div id="modalAddUser" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/60" onclick="closeModal('modalAddUser')"></div>
    
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-blue-50 p-2.5 rounded-xl border border-blue-100">
                    <span class="material-symbols-outlined text-blue-600" style="font-size:20px">person_add</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-800">Tambah User Baru</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Buat akun user baru</p>
                </div>
            </div>
            <button onclick="closeModal('modalAddUser')" class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors p-1.5 rounded-lg">
                <span class="material-symbols-outlined" style="font-size:20px">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('superadmin.users.store') }}" id="addUserForm">
            @csrf
            <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                <div class="space-y-1.5">
                    <label class="text-xs font-medium text-slate-600">Nama Lengkap <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">badge</span>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full bg-white border border-slate-200 rounded-lg pl-9 pr-3 py-2.5 text-slate-800 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                            placeholder="Nama lengkap">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-medium text-slate-600">Email <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">mail</span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-white border border-slate-200 rounded-lg pl-9 pr-3 py-2.5 text-slate-800 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                            placeholder="email@example.com">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-medium text-slate-600">External ID <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">fingerprint</span>
                        <input type="text" name="external_id" value="{{ old('external_id') }}" required
                            class="w-full bg-white border border-slate-200 rounded-lg pl-9 pr-3 py-2.5 text-slate-800 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                            placeholder="ID dari SSO">
                    </div>
                    <p class="text-slate-400 text-xs mt-1">ID unik dari sistem SSO</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-600">Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">lock</span>
                            <input type="password" name="password" required minlength="8"
                                class="w-full bg-white border border-slate-200 rounded-lg pl-9 pr-3 py-2.5 text-slate-800 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                placeholder="Min. 8 karakter">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-600">Konfirmasi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px">check_circle</span>
                            <input type="password" name="password_confirmation" required
                                class="w-full bg-white border border-slate-200 rounded-lg pl-9 pr-3 py-2.5 text-slate-800 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-slate-400" style="font-size:14px">shield</span>
                        Assign Role
                    </label>
                    <div class="space-y-2">
                        @foreach($roles as $role)
                        @php
                            $roleIcon = match(strtolower($role->name)) {
                                'superadmin' => 'star',
                                'dosen' => 'school',
                                'mahasiswa' => 'person',
                                'admin_banksoal' => 'quiz',
                                'admin_capstone' => 'task',
                                default => 'shield',
                            };
                            $isSuperadmin = strtolower($role->name) === 'superadmin';
                        @endphp
                        <label class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all role-option {{ $isSuperadmin ? 'superadmin-role' : '' }}">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 add-role-cb" data-role-name="{{ strtolower($role->name) }}" {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                            <span class="material-symbols-outlined text-slate-500" style="font-size:20px">{{ $roleIcon }}</span>
                            <div>
                                <p class="text-sm font-medium text-slate-700">{{ ucfirst($role->name) }}</p>
                                @if($role->module !== 'global')
                                <p class="text-slate-400 text-xs">{{ $role->module }}</p>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div id="addFieldDosen" class="hidden bg-green-50 border border-green-200 rounded-xl p-4 space-y-3">
                    <p class="text-green-600 text-xs font-semibold uppercase tracking-wide flex items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size:14px">school</span>
                        Data Dosen
                    </p>
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-600">Nomor Pegawai</label>
                        <input type="text" name="employee_number" value="{{ old('employee_number') }}"
                            class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none transition-all"
                            placeholder="Contoh: EMP-001">
                    </div>
                </div>

                <div id="addFieldMahasiswa" class="hidden bg-orange-50 border border-orange-200 rounded-xl p-4 space-y-3">
                    <p class="text-orange-600 text-xs font-semibold uppercase tracking-wide flex items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size:14px">groups</span>
                        Data Mahasiswa
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-600">NIM</label>
                            <input type="text" name="student_number" value="{{ old('student_number') }}"
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none transition-all"
                                placeholder="Contoh: 2021001">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-600">Angkatan</label>
                            <input type="number" name="cohort_year" value="{{ old('cohort_year', date('Y')) }}" min="2000" max="{{ date('Y') + 1 }}"
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('modalAddUser')"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-700 bg-white border border-slate-200 hover:border-slate-300 px-4 py-2 rounded-lg transition-all">
                    <span class="material-symbols-outlined" style="font-size:16px">close</span>
                    Batal
                </button>
                <button type="submit" id="submitAddUserBtn"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm transition-colors shadow-sm">
                    <span class="material-symbols-outlined" style="font-size:16px">save</span>
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Superadmin Warning Modal --}}
<div id="superadminWarningModal" class="fixed inset-0 hidden items-center justify-center p-4 z-[60]">
    <div class="fixed inset-0 bg-slate-900/80" onclick="closeSuperadminWarningModal()"></div>
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3 bg-red-50">
            <div class="bg-red-100 p-2.5 rounded-xl">
                <span class="material-symbols-outlined text-red-600" style="font-size:24px">warning</span>
            </div>
            <div>
                <h3 class="text-base font-semibold text-red-800">Peringatan Kritis!</h3>
                <p class="text-xs text-red-600 mt-0.5">Role Superadmin memiliki akses penuh</p>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="text-amber-800 text-sm font-medium mb-2">⚠️ Perhatian!</p>
                <p class="text-amber-700 text-sm">Role <strong class="font-semibold">Superadmin</strong> memiliki otoritas yang sangat tinggi terhadap sistem, termasuk:</p>
                <ul class="mt-2 space-y-1 text-amber-700 text-sm list-disc pl-5">
                    <li>Akses penuh ke semua data dan konfigurasi sistem</li>
                    <li>Kemampuan untuk menghapus atau memodifikasi data kritis</li>
                    <li>Akses ke semua modul tanpa batasan</li>
                </ul>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-red-700 text-sm font-medium">❗ Tidak direkomendasikan memberikan role Superadmin kecuali benar-benar diperlukan.</p>
                <p class="text-red-600 text-xs mt-2">Pastikan Anda telah mempertimbangkan risiko keamanan sebelum melanjutkan.</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button onclick="closeSuperadminWarningModal()" 
                    class="flex-1 bg-white hover:bg-slate-50 text-slate-600 font-medium py-2.5 px-4 rounded-lg transition-all border border-slate-200">
                    <span class="material-symbols-outlined align-middle mr-1" style="font-size:16px">close</span>
                    Batalkan
                </button>
                <button id="confirmSuperadminAdd" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all shadow-sm">
                    <span class="material-symbols-outlined align-middle mr-1" style="font-size:16px">check_circle</span>
                    Saya Mengerti Risiko
                </button>
            </div>
        </div>
    </div>
</div>