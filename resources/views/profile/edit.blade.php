{{-- resources/views/profile/edit.blade.php --}}

<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-[#F8F9FA]">
        <div class="p-4 sm:p-6">
            <div class="max-w-full mx-auto">

                {{-- Header --}}
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 tracking-tight flex items-center gap-2">
                            <span class="w-1 h-5 bg-blue-600 rounded-full"></span>
                            Profil Akun
                        </h2>
                        <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-0.5">
                            Kelola identitas dan keamanan akses Anda
                        </p>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 text-amber-700">
                        <span class="material-symbols-outlined text-[14px]">lock</span>
                        <p class="text-[11px] font-medium">Data SSO tidak dapat diubah via aplikasi ini</p>
                    </div>
                </div>

                {{-- ── WARNING DEFAULT PASSWORD ── --}}
                @php
                    $namaDepan       = explode(' ', auth()->user()->name)[0];
                    $nim             = auth()->user()->student?->student_number ?? '';
                    $defaultPassword = $namaDepan . $nim;
                    $isDefaultPw     = auth()->user()->password && 
                                    \Illuminate\Support\Facades\Hash::check($defaultPassword, auth()->user()->password);
                @endphp

                @if ($isDefaultPw)
                    <div class="mb-4 flex items-start gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-2xl">
                        <span class="material-symbols-outlined text-yellow-500 text-[20px] shrink-0 mt-0.5">warning</span>
                        <div>
                            <p class="text-sm font-semibold text-yellow-700">Harap ubah password Anda</p>
                            <p class="text-xs text-yellow-600 mt-0.5">
                                Password Anda masih menggunakan password default. Segera ubah melalui kolom <strong>Keamanan</strong> di sebelah kanan untuk melindungi akun Anda.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Grid 3 kolom — items-stretch agar rata bawah --}}
                <div class="grid grid-cols-1 lg:grid-cols-[270px_1fr_290px] gap-4 items-stretch pb-6">

                    {{-- ── KOLOM 1: Avatar + Info + Aktivitas ── --}}
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                        <div class="flex flex-col items-center pt-6 pb-4 px-4">
                            @php
                                $user = auth()->user();
                                $name = $user->name;
                                $initials = strtoupper(substr($name, 0, 1));
                                $sp = strpos($name, ' ');
                                if ($sp !== false) $initials .= strtoupper(substr($name, $sp + 1, 1));
                                
                                $rc = $user->hasRole('superadmin') ? 'bg-red-100 text-red-700'
                                    : ($user->hasRole('dosen')     ? 'bg-purple-100 text-purple-700'
                                    : ($user->hasRole('mahasiswa') ? 'bg-blue-100 text-blue-700'
                                    : 'bg-amber-100 text-amber-700'));
                            @endphp

                            {{-- Container Avatar --}}
                            <div class="relative group">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold {{ $rc }} shadow-inner overflow-hidden border-2 border-white bg-white">
                                    @if($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" id="currentAvatar" alt="Avatar" class="w-full h-full object-cover">
                                    @else
                                        <span id="avatarInitials">{{ $initials }}</span>
                                    @endif
                                </div>

                                {{-- Trigger Modal Kelola Foto --}}
                                <button type="button" onclick="openManagePhotoModal()" 
                                    class="absolute -bottom-1 -right-1 w-6 h-6 bg-white border border-slate-200 rounded-full flex items-center justify-center shadow-sm cursor-pointer hover:text-blue-600 hover:border-blue-200 transition-all z-10">
                                    <span class="material-symbols-outlined !text-[14px]">photo_camera</span>
                                </button>
                            </div>

                            <p class="mt-3 text-[14px] font-bold text-slate-800 text-center leading-tight">{{ $user->name }}</p>
                            <p class="text-[11px] text-slate-400 mt-1 text-center truncate w-full px-2">{{ $user->email }}</p>
                            
                            <div class="mt-3 flex flex-wrap gap-1 justify-center">
                                @foreach($user->roles as $role)
                                <span class="text-[9px] font-semibold px-2 py-0.5 rounded-lg border uppercase tracking-tighter
                                    @if(str_contains($role->name,'superadmin')) bg-red-50 text-red-600 border-red-100
                                    @elseif(str_contains($role->name,'dosen'))  bg-purple-50 text-purple-600 border-purple-100
                                    @elseif(str_contains($role->name,'mahasiswa')) bg-blue-50 text-blue-600 border-blue-100
                                    @else bg-amber-50 text-amber-600 border-amber-100 @endif">
                                    {{ $role->name }}
                                </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t border-slate-100 px-4 py-5 space-y-5 flex-1">
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-3">Data Akademik</p>
                                <div class="space-y-1">
                                    @if($user->hasRole('mahasiswa') && $user->student)
                                        @include('profile.partials._info-row', ['label' => 'NIM', 'value' => $user->student->student_number])
                                        @include('profile.partials._info-row', ['label' => 'Angkatan', 'value' => $user->student->cohort_year])
                                    @elseif($user->hasRole('dosen') && $user->lecturer)
                                        @include('profile.partials._info-row', ['label' => 'NIP', 'value' => $user->lecturer->employee_number])
                                    @endif
                                    @include('profile.partials._info-row', [
                                        'label' => 'Akses Terakhir',
                                        'value' => $user->last_login?->diffForHumans() ?? 'Baru saja'
                                    ])
                                </div>
                            </div>
                            <div class="pt-4 border-t border-slate-100">
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mb-3">Jejak Aktivitas</p>
                                @include('profile.partials.activity-log')
                            </div>
                        </div>
                    </div>

                    {{-- ── KOLOM 2: Pengaturan Akun ── --}}
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2 flex-shrink-0">
                            <span class="material-symbols-outlined text-blue-600 text-[18px]">manage_accounts</span>
                            <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-widest">Pengaturan Akun</h3>
                            <span class="ml-auto text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">Bisa diubah</span>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- ── KOLOM 3: Keamanan ── --}}
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2 flex-shrink-0">
                            <span class="material-symbols-outlined text-purple-600 text-[18px]">lock_person</span>
                            <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-widest">Keamanan</h3>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                {{-- CV Builder Section (Mahasiswa & Alumni Only) --}}
                @if(auth()->user()->hasAnyRole(['mahasiswa', 'alumni']))
                <div class="mt-4 mb-6">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 shadow-md text-white flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold mb-1 flex items-center gap-2">
                                <span class="material-symbols-outlined">description</span>
                                CV Builder Cerdas
                            </h3>
                            <p class="text-sm opacity-90">Buat CV profesional semi-otomatis menggunakan data profil, riwayat akademik, dan kegiatan Anda.</p>
                        </div>
                        <div class="relative z-10 shrink-0">
                            <a href="{{ route('profile.cv.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-indigo-600 font-bold text-sm rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                                Mulai Buat CV
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── MODALS SECTION ── --}}

    {{-- MODAL 1: Pilihan Kelola Foto --}}
    <div id="modalManagePhoto" class="hidden fixed inset-0 z-[100] overflow-y-auto bg-slate-900/60">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl animate-in fade-in zoom-in-95 duration-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-widest">Foto Profil</h3>
                    <button type="button" onclick="closeManagePhotoModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="p-4 space-y-2">
                    <label for="avatarInput" class="w-full flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 hover:border-blue-200 cursor-pointer transition-all group">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined">upload</span>
                        </div>
                        <div class="text-left">
                            <p class="text-[11px] font-semibold text-slate-700 uppercase tracking-tight">Unggah Foto Baru</p>
                            <p class="text-[10px] text-slate-400">JPG, PNG atau WebP (Max. 2MB)</p>
                        </div>
                    </label>
                    <input type="file" id="avatarInput" class="hidden" accept="image/jpeg,image/png,image/webp">

                    @if($user->avatar_url)
                    <button type="button" onclick="openConfirmDeleteModal()" class="w-full flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-red-50 hover:border-red-200 transition-all group">
                        <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined">delete</span>
                        </div>
                        <div class="text-left">
                            <p class="text-[11px] font-semibold text-red-700 uppercase tracking-tight">Hapus Foto Sekarang</p>
                            <p class="text-[10px] text-slate-400">Kembali ke inisial nama</p>
                        </div>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 2: Cropper --}}
    <div id="modalCrop" class="hidden fixed inset-0 z-[110] overflow-y-auto bg-slate-900/60">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl animate-in fade-in zoom-in-95 duration-200">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-widest">Sesuaikan Foto</h3>
                    <button type="button" onclick="closeCropModal()" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="p-6 text-center">
                    <div class="max-h-[400px] overflow-hidden rounded-xl bg-slate-50 border border-slate-100">
                        <img id="imageToCrop" class="block max-w-full mx-auto">
                    </div>
                    <p class="text-[9px] text-slate-400 font-bold uppercase mt-4 tracking-tighter">Gunakan mouse/jari untuk menggeser atau memperbesar foto</p>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeCropModal()" class="px-4 py-2 text-[10px] font-semibold text-slate-500 uppercase hover:bg-slate-200 rounded-xl transition-all">Batal</button>
                    <button type="button" id="btnSaveCrop" class="bg-[#5E53F4] text-white px-6 py-2 text-[10px] font-semibold uppercase rounded-xl shadow-lg shadow-blue-100 hover:bg-[#4e44e0] transition-all">Simpan Foto</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 3: Konfirmasi Hapus --}}
    <div id="modalDeleteAvatar" class="hidden fixed inset-0 z-[120] overflow-y-auto bg-slate-900/60">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl animate-in fade-in zoom-in-95 duration-200">
                <div class="p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-red-600">delete_forever</span>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800 uppercase tracking-widest mb-2">Hapus Foto Profil?</h3>
                    <p class="text-[11px] text-slate-500 font-medium leading-relaxed">Foto profil Anda akan dihapus dan kembali menggunakan inisial nama. Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeDeleteAvatarModal()" class="flex-1 px-4 py-2 text-[10px] font-semibold text-slate-500 uppercase hover:bg-slate-200 rounded-xl transition-all">Batal</button>
                    <button type="button" id="btnConfirmDeleteAvatar" class="flex-1 bg-red-600 text-white px-4 py-2 text-[10px] font-semibold uppercase rounded-xl shadow-lg shadow-red-100 hover:bg-red-700 transition-all">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>

    {{-- LOGIC SCRIPT --}}
    <script>
    let cropper;
    const avatarInput = document.getElementById('avatarInput');
    const imageToCrop = document.getElementById('imageToCrop');
    const modalManage = document.getElementById('modalManagePhoto');
    const modalCrop   = document.getElementById('modalCrop');
    const modalDelete = document.getElementById('modalDeleteAvatar');

    function openManagePhotoModal() { 
        modalManage.classList.remove('hidden'); 
        document.body.style.overflow = 'hidden'; 
    }
    function closeManagePhotoModal() { 
        modalManage.classList.add('hidden'); 
        document.body.style.overflow = ''; 
    }

    function openConfirmDeleteModal() {
        closeManagePhotoModal();
        modalDelete.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteAvatarModal() { 
        modalDelete.classList.add('hidden'); 
        document.body.style.overflow = ''; 
    }

    avatarInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const reader = new FileReader();
            reader.onload = function (event) {
                closeManagePhotoModal();
                imageToCrop.src = event.target.result;
                modalCrop.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                if (cropper) cropper.destroy();
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    guides: false,
                    center: true,
                    highlight: false,
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                });
            };
            reader.readAsDataURL(files[0]);
        }
    });

    function closeCropModal() {
        modalCrop.classList.add('hidden');
        document.body.style.overflow = '';
        avatarInput.value = '';
    }

    document.getElementById('btnSaveCrop').addEventListener('click', function () {
        const btn = this;
        const originalFile = avatarInput.files[0];
        const canvas = cropper.getCroppedCanvas({ width: 400, height: 400 });
        btn.disabled = true;
        btn.innerHTML = 'MEMPROSES...';

        canvas.toBlob(function (blob) {
            const formData = new FormData();
            formData.append('avatar', blob, 'avatar.webp');
            formData.append('avatar_original', originalFile); 

            fetch("{{ route('profile.avatar.update') }}", {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') window.location.reload();
                else { alert(data.message || 'Gagal upload'); btn.disabled = false; btn.innerHTML = 'SIMPAN FOTO'; }
            })
            .catch(err => { console.error(err); btn.disabled = false; btn.innerHTML = 'SIMPAN FOTO'; });
        }, 'image/webp', 0.8);
    });

    document.getElementById('btnConfirmDeleteAvatar')?.addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = 'MENGHAPUS...';

        fetch("{{ route('profile.avatar.destroy') }}", {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') window.location.reload();
            else { alert('Gagal menghapus foto'); btn.disabled = false; btn.innerHTML = 'YA, HAPUS'; }
        })
        .catch(err => { console.error(err); btn.disabled = false; btn.innerHTML = 'YA, HAPUS'; });
    });
    </script>
</x-sidebar>
</x-app-layout>