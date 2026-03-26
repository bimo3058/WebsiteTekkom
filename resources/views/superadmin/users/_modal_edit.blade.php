<div id="modalEditRoles" class="fixed inset-0 hidden z-50 flex items-center justify-center p-4 z-50">
    <div class="fixed inset-0 bg-slate-900/60" onclick="closeModal('modalEditRoles')"></div>
    
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-purple-50 p-2.5 rounded-xl border border-purple-100">
                    <span class="material-symbols-outlined text-purple-600" style="font-size:20px">manage_accounts</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-800">Edit User Roles</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Roles untuk <span id="editRolesUserName" class="text-blue-600 font-medium"></span></p>
                </div>
            </div>
            <button onclick="closeModal('modalEditRoles')" class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors p-1.5 rounded-lg">
                <span class="material-symbols-outlined" style="font-size:20px">close</span>
            </button>
        </div>

        <form id="formEditRoles" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
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
                        <label class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all role-option {{ $isSuperadmin ? 'superadmin-role-edit' : '' }}">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 edit-role-cb" data-role-name="{{ strtolower($role->name) }}" onchange="onEditRoleChange(this)">
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

                <div id="editFieldDosen" class="hidden bg-green-50 border border-green-200 rounded-xl p-4 space-y-3">
                    <p class="text-green-600 text-xs font-semibold uppercase tracking-wide flex items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size:14px">school</span>
                        Data Dosen
                    </p>
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-600">Nomor Pegawai</label>
                        <input type="text" name="employee_number" id="editEmpNumber"
                            class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none transition-all"
                            placeholder="Contoh: EMP-001">
                    </div>
                </div>

                <div id="editFieldMahasiswa" class="hidden bg-orange-50 border border-orange-200 rounded-xl p-4 space-y-3">
                    <p class="text-orange-600 text-xs font-semibold uppercase tracking-wide flex items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size:14px">groups</span>
                        Data Mahasiswa
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-600">NIM</label>
                            <input type="text" name="student_number" id="editStudentNumber"
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-sm focus:border-orange-400 focus:ring-2 focus:ring-orange-100 outline-none transition-all"
                                placeholder="Contoh: 2021001">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-600">Angkatan</label>
                            <input type="number" name="cohort_year" id="editCohortYear" min="2000" max="{{ date('Y') + 1 }}"
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-800 text-sm focus:border-orange-400 focus:ring-2 focus:ring-orange-100 outline-none transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('modalEditRoles')"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-700 bg-white border border-slate-200 hover:border-slate-300 px-4 py-2 rounded-lg transition-all">
                    <span class="material-symbols-outlined" style="font-size:16px">close</span>
                    Cancel
                </button>
                <button type="submit" id="submitEditRolesBtn"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm transition-colors shadow-sm">
                    <span class="material-symbols-outlined" style="font-size:16px">save</span>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Superadmin Warning Modal for Edit --}}
<div id="superadminWarningModalEdit" class="fixed inset-0 hidden items-center justify-center p-4 z-[60]">
    <div class="fixed inset-0 bg-slate-900/80" onclick="closeSuperadminWarningModalEdit()"></div>
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
                <button onclick="closeSuperadminWarningModalEdit()" 
                    class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium py-2.5 px-4 rounded-lg transition-all">
                    Batalkan
                </button>
                <button id="confirmSuperadminEdit" 
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all shadow-sm">
                    Saya Mengerti Risiko
                </button>
            </div>
        </div>
    </div>
</div>