{{-- MODAL: Tambah User Baru --}}
<div id="modalAddUser" class="fixed inset-0 hidden z-[50] flex items-center justify-center p-4 bg-slate-900/40">
    <div class="fixed inset-0" onclick="closeModal('modalAddUser')"></div>
    
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-[var(--c-primary-subtle)] flex items-center justify-between bg-[rgba(11,38,110,0.06)]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[var(--c-primary)] flex items-center justify-center">
                    <span class="material-symbols-outlined text-white" style="font-size:20px">person_add</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#1A1C1E] uppercase tracking-tight">Tambah User Baru</h3>
                    <p class="text-[10px] text-[var(--c-primary)] font-medium">Daftarkan akun pengguna baru ke sistem</p>
                </div>
            </div>
            <button onclick="closeModal('modalAddUser')" class="text-[var(--c-primary)] hover:text-[var(--c-primary-hover)] hover:bg-[var(--c-primary-subtle)] transition-colors p-1.5 rounded-lg">
                <span class="material-symbols-outlined" style="font-size:20px">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('superadmin.users.store') }}" id="addUserForm">
            @csrf
            <div class="p-6 space-y-5 max-h-[60vh] overflow-y-auto custom-scrollbar">

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Nama lengkap..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-4 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                        <input type="email" name="email" required placeholder="nama@undip.ac.id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-4 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">External SSO ID</label>
                    <input type="text" name="external_id" required placeholder="Contoh: NIP atau NIM SSO"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-4 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                </div>

                <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password</label>
                            <input type="password" name="password" id="addPassword" required minlength="8"
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:border-blue-400 outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Konfirmasi</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:border-blue-400 outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-1 px-1">
                        <div class="flex items-center gap-2">
                            <div id="passStrengthBar" class="h-1 flex-1 bg-slate-200 rounded-full overflow-hidden transition-all">
                                <div id="passStrengthFill" class="h-full w-0 transition-all duration-300"></div>
                            </div>
                            <span id="passStrengthText" class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Weak</span>
                        </div>
                        <p id="passReqWarning" class="hidden text-[8px] text-red-500 font-medium">Min. 8 karakter dibutuhkan</p>
                    </div>
                </div>

                {{-- Dynamic Fields for Lecturer --}}
                <div id="addFieldDosen" class="hidden p-4 bg-blue-50/50 border border-blue-100 rounded-2xl space-y-3 animate-in slide-in-from-top-2 duration-200">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-blue-600" style="font-size:16px">badge</span>
                        <span class="text-[10px] font-black text-blue-700 uppercase tracking-widest">Data Dosen</span>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-blue-600/70 uppercase tracking-widest ml-1">NIP / Nomor Pegawai</label>
                        <input type="text" name="employee_number" id="addEmployeeNumber" placeholder="Masukkan NIP..."
                            class="w-full bg-white border border-blue-200 rounded-xl px-3 py-2 text-xs focus:ring-4 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                        <p id="nipWarning" class="hidden text-[8px] text-red-500 font-medium">NIP harus tepat 18 digit angka</p>
                    </div>
                </div>

                {{-- Dynamic Fields for Student --}}
                <div id="addFieldMahasiswa" class="hidden p-4 bg-blue-50/50 border border-blue-100 rounded-2xl space-y-3 animate-in slide-in-from-top-2 duration-200">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-blue-600" style="font-size:16px">school</span>
                        <span class="text-[10px] font-black text-blue-700 uppercase tracking-widest">Data Mahasiswa</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-blue-600/70 uppercase tracking-widest ml-1">NIM</label>
                            <input type="text" name="student_number" id="addStudentNumber" placeholder="Masukkan NIM..."
                                class="w-full bg-white border border-blue-200 rounded-xl px-3 py-2 text-xs focus:ring-4 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                            <p id="nimWarning" class="hidden text-[8px] text-red-500 font-medium">NIM harus tepat 14 digit angka</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-blue-600/70 uppercase tracking-widest ml-1">Tahun Angkatan</label>
                            <input type="number" name="cohort_year" placeholder="Contoh: 2023"
                                class="w-full bg-white border border-blue-200 rounded-xl px-3 py-2 text-xs focus:ring-4 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Pilih Roles</label>

                    {{-- Primary Roles Grid --}}
                    <div class="grid grid-cols-2 gap-4">
                        @php
                            $primaryRoles = [
                                'Mahasiswa' => ['mahasiswa'],
                                'Dosen' => ['dosen'],
                            ];
                        @endphp
                        @foreach($primaryRoles as $catName => $catRoles)
                            <div class="bg-white border-2 border-blue-100 rounded-xl overflow-hidden shadow-sm flex flex-col">
                                <div class="px-3 py-2 bg-blue-50/50 border-b border-blue-100">
                                    <div class="text-[9px] font-black text-blue-600 uppercase tracking-wider flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-blue-600"></span>
                                        {{ $catName }} (Primary)
                                    </div>
                                </div>
                                <div class="p-3 flex flex-wrap gap-2">
                                    @foreach($roles as $role)
                                        @if(in_array(strtolower($role->name), $catRoles))
                                            @php
                                                $roleColors = match(strtolower($role->name)) {
                                                    'superadmin' => 'peer-checked:bg-purple-50 peer-checked:text-purple-600 peer-checked:border-purple-200 text-slate-500 bg-white border-slate-200',
                                                    'dosen' => 'peer-checked:bg-green-50 peer-checked:text-green-600 peer-checked:border-green-200 text-slate-500 bg-white border-slate-200',
                                                    'mahasiswa' => 'peer-checked:bg-orange-50 peer-checked:text-orange-600 peer-checked:border-orange-200 text-slate-500 bg-white border-slate-200',
                                                    default => 'peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:border-blue-200 text-slate-500 bg-white border-slate-200',
                                                };
                                            @endphp
                                            <label class="relative cursor-pointer">
                                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                    class="peer sr-only add-role-cb"
                                                    data-role-name="{{ strtolower($role->name) }}">
                                                <div class="px-2.5 py-1 rounded-lg border text-[9px] font-bold uppercase transition-all {{ $roleColors }}">
                                                    {{ $role->name }}
                                                </div>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Secondary Roles Grid --}}
                    <div class="grid grid-cols-2 gap-4">
                        @php
                            $categories = [
                                'Admin' => ['superadmin', 'admin_banksoal', 'admin_capstone', 'admin_eoffice', 'admin_kemahasiswaan'],
                                'Bank Soal' => ['gpm'],
                                'Capstone' => ['admin_capstone'],
                                'Kemahasiswaan' => ['pengurus_himpunan', 'ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'staff_himpunan', 'ketua_departemen', 'dpm', 'bendahara'],
                                'EOffice' => ['koor_prak', 'asprak', 'praktikan', 'koor_kp'],
                            ];
                        @endphp
                        @foreach($categories as $catName => $catRoles)
                            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col">
                                <div class="px-3 py-2 bg-slate-50 border-b border-slate-100">
                                    <div class="text-[9px] font-black text-slate-500 uppercase tracking-wider flex items-center gap-2">
                                        <span class="w-1 h-1 rounded-full bg-slate-400"></span>
                                        {{ $catName }}
                                    </div>
                                </div>
                                <div class="p-3 flex flex-wrap gap-2">
                                    @foreach($roles as $role)
                                        @if(in_array(strtolower($role->name), $catRoles))
                                            @php
                                                $roleColors = match(strtolower($role->name)) {
                                                    'superadmin' => 'peer-checked:bg-purple-50 peer-checked:text-purple-600 peer-checked:border-purple-200 text-slate-500 bg-white border-slate-200',
                                                    'dosen' => 'peer-checked:bg-green-50 peer-checked:text-green-600 peer-checked:border-green-200 text-slate-500 bg-white border-slate-200',
                                                    'mahasiswa' => 'peer-checked:bg-orange-50 peer-checked:text-orange-600 peer-checked:border-orange-200 text-slate-500 bg-white border-slate-200',
                                                    default => 'peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:border-blue-200 text-slate-500 bg-white border-slate-200',
                                                };
                                            @endphp
                                            <label class="relative cursor-pointer">
                                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                    class="peer sr-only add-role-cb"
                                                    data-role-name="{{ strtolower($role->name) }}">
                                                <div class="px-2.5 py-1 rounded-lg border text-[9px] font-bold uppercase transition-all {{ $roleColors }}">
                                                    {{ $role->name }}
                                                </div>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('modalAddUser')"
                    class="text-[11px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest px-4 py-2 transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSaveUser"
                    class="bg-[var(--c-primary)] hover:bg-[var(--c-primary-hover)] text-white text-[11px] font-bold uppercase tracking-widest px-6 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:16px">person_add</span>
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Peringatan Superadmin (Add) --}}
<div id="superadminWarningModal" class="fixed inset-0 hidden z-[70] flex items-center justify-center p-4 bg-slate-900/60">
    <div class="fixed inset-0" onclick="closeSuperadminWarningModal()"></div>
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-red-100 overflow-hidden animate-in zoom-in-95 duration-200">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-red-100">
                <span class="material-symbols-outlined text-red-600 animate-pulse" style="font-size:32px">warning</span>
            </div>
            <h3 class="text-sm font-black text-red-800 uppercase tracking-tight">Peringatan Kritis!</h3>
            <p class="text-[11px] text-slate-500 mt-2 leading-relaxed px-4">
                Anda mencoba memberikan akses <strong class="text-red-700">Superadmin</strong>. Role ini memiliki otoritas mutlak yang tidak direkomendasikan untuk akun operasional biasa.
            </p>
        </div>

        <div class="px-6 pb-6 space-y-4">
            <div class="bg-red-50 border border-red-100 rounded-xl p-4 space-y-3">
                <label class="block text-[9px] font-black text-red-700 uppercase tracking-widest text-center">
                    Ketik "SUPERADMIN" untuk konfirmasi
                </label>
                <input type="text" id="confirmSuperadminAddText" placeholder="..."
                    class="w-full bg-white border border-red-200 rounded-lg px-3 py-2 text-xs font-bold text-red-700 placeholder:text-red-200 text-center focus:ring-4 focus:ring-red-100 focus:border-red-400 outline-none transition-all uppercase">
            </div>

            <div class="flex gap-3">
                <button onclick="closeModal('superadminWarningModal')"
                    class="flex-1 text-[10px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest py-3 transition-colors">
                    Batalkan
                </button>
                <button id="confirmSuperadminAdd" disabled
                    class="flex-1 bg-slate-200 text-slate-400 cursor-not-allowed text-[10px] font-black uppercase tracking-widest py-3 rounded-xl transition-all shadow-sm">
                    Saya Mengerti Risiko
                </button>
            </div>
        </div>
    </div>
</div>