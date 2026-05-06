{{-- resources/views/profile/edit.blade.php --}}

<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-[#F6F8FA]">
        <div class="p-4 sm:p-6">
            <div class="max-w-full mx-auto">

                {{-- ── PAGE HEADER ── --}}
                <div class="mb-5 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-[18px] font-bold text-[#0D0D12] tracking-tight leading-tight">
                            Profil Akun
                        </h2>
                        <p class="text-[12px] text-[#808897] mt-0.5 font-medium">
                            Kelola identitas dan keamanan akses Anda
                        </p>
                    </div>
                    <div class="hidden sm:flex items-center gap-2 bg-[#F9ECCB] border border-[#D39C3D]/30 rounded-xl px-3 py-2">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#956321" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <p class="text-[11px] font-medium text-[#5B3D1E]">Data SSO tidak dapat diubah via aplikasi ini</p>
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
                    <div class="mb-4 flex items-start gap-3 p-4 bg-[#F9ECCB] border border-[#D39C3D]/30 rounded-2xl">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#956321" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 mt-0.5">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        <div>
                            <p class="text-[13px] font-semibold text-[#5B3D1E]">Harap ubah password Anda</p>
                            <p class="text-[12px] text-[#956321] mt-0.5 leading-relaxed">
                                Password Anda masih menggunakan password default. Segera ubah melalui kolom <strong>Keamanan</strong> di sebelah kanan.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- ── GRID UTAMA: 3 kolom ── --}}
                <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr_290px] gap-4 items-start pb-6">

                    {{-- ── KOLOM 1: Avatar + Info + Stats + Aktivitas ── --}}
                    <div class="flex flex-col gap-4 h-full">

                        {{-- Avatar card --}}
                        <div class="flex-1 bg-white border border-[#DFE1E7] rounded-2xl shadow-[0_1px_2px_rgba(228,229,231,0.24)] overflow-hidden flex flex-col">
                            <div class="flex flex-col items-center pt-6 pb-5 px-5">
                                @php
                                    $user     = auth()->user();
                                    $name     = $user->name;
                                    $initials = strtoupper(substr($name, 0, 1));
                                    $sp       = strpos($name, ' ');
                                    if ($sp !== false) $initials .= strtoupper(substr($name, $sp + 1, 1));
                                @endphp

                                {{-- Avatar --}}
                                <div class="relative group mb-3">
                                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-xl font-bold bg-[#E8EDF7] text-[#0B266E] border-2 border-white shadow-sm overflow-hidden">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" id="currentAvatar" alt="Avatar" class="w-full h-full object-cover">
                                        @else
                                            <span id="avatarInitials">{{ $initials }}</span>
                                        @endif
                                    </div>
                                    <button type="button" onclick="openManagePhotoModal()"
                                        class="absolute -bottom-1 -right-1 w-6 h-6 bg-white border border-[#DFE1E7] rounded-full flex items-center justify-center shadow-sm cursor-pointer hover:border-[#0B266E] hover:text-[#0B266E] transition-all z-10 text-[#808897]">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-[14px] font-bold text-[#0D0D12] text-center leading-tight">{{ $user->name }}</p>
                                <p class="text-[12px] text-[#808897] mt-1 text-center truncate w-full px-2">{{ $user->email }}</p>

                                {{-- Role badges --}}
                                <div class="mt-3 flex flex-wrap gap-1 justify-center">
                                    @foreach($user->roles as $role)
                                    <span class="text-[10px] font-semibold px-2.5 py-0.5 rounded-full border
                                        @if(str_contains($role->name,'superadmin')) bg-[#FADAE1] text-[#710E21] border-[#ED8296]/30
                                        @elseif(str_contains($role->name,'dosen'))  bg-[#E8EDF7] text-[#0B266E] border-[#8FA3D1]/30
                                        @elseif(str_contains($role->name,'mahasiswa')) bg-[#DDF2EE] text-[#174E43] border-[#40C4AA]/30
                                        @else bg-[#F9ECCB] text-[#5B3D1E] border-[#D39C3D]/30 @endif">
                                        {{ $role->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Data akademik --}}
                            <div class="border-t border-[#F0F1F4] px-5 py-4 h-full">
                                <p class="text-[10px] font-semibold text-[#A4ABB8] uppercase tracking-widest mb-3">Data Akademik</p>
                                <div class="space-y-0">
                                    @if($user->hasRole('mahasiswa') && $user->student)
                                        @include('profile.partials._info-row', ['label' => 'NIM',      'value' => $user->student->student_number])
                                        @include('profile.partials._info-row', ['label' => 'Angkatan', 'value' => $user->student->cohort_year])
                                    @elseif($user->hasRole('dosen') && $user->lecturer)
                                        @include('profile.partials._info-row', ['label' => 'NIP', 'value' => $user->lecturer->employee_number])
                                    @endif
                                    @include('profile.partials._info-row', [
                                        'label' => 'Akses Terakhir',
                                        'value' => $user->last_login?->diffForHumans() ?? 'Baru saja'
                                    ])
                                </div>

                                <div class="mt-4 pt-4 border-t border-[#F0F1F4]">
                                    <div class="flex items-center gap-2 mb-3">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#808897" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                                        </svg>
                                        <p class="text-[11px] font-semibold text-[#A4ABB8] uppercase tracking-widest">Jejak Aktivitas</p>
                                    </div>
                                    <div>
                                        @include('profile.partials.activity-log')
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Stats card --}}
                        @include('profile.partials.stats-card')

                    </div>

                    {{-- ── KOLOM 2: Pengaturan Akun ── --}}
                    <div class="bg-white border border-[#DFE1E7] rounded-2xl shadow-[0_1px_2px_rgba(228,229,231,0.24)] overflow-hidden flex flex-col h-full">

                        {{-- Header --}}
                        <div class="px-5 py-4 border-b border-[#F0F1F4] flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-[#E8EDF7] flex items-center justify-center">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <h3 class="text-[12px] font-semibold text-[#0D0D12] tracking-tight">Pengaturan Akun</h3>
                            <span class="ml-auto text-[10px] font-semibold text-[#287F6E] bg-[#DDF2EE] border border-[#40C4AA]/30 px-2 py-0.5 rounded-full">Bisa diubah</span>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- ── KOLOM 3: Keamanan + Hapus Akun ── --}}
                    <div class="flex flex-col gap-4">

                        {{-- Password --}}
                        <div class="bg-white border border-[#DFE1E7] rounded-2xl shadow-[0_1px_2px_rgba(228,229,231,0.24)] overflow-hidden flex flex-col">
                            <div class="px-5 py-4 border-b border-[#F0F1F4] flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-[#E8EDF7] flex items-center justify-center">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </div>
                                <h3 class="text-[12px] font-semibold text-[#0D0D12] tracking-tight">Keamanan</h3>
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── MODAL: Kelola Foto ── --}}
    <div id="modalManagePhoto" class="hidden fixed inset-0 z-[100] overflow-y-auto bg-[#0D0D12]/60 backdrop-blur-[2px]">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-[#DFE1E7]">
                <div class="px-5 py-4 border-b border-[#F0F1F4] flex items-center justify-between">
                    <h3 class="text-[12px] font-semibold text-[#0D0D12] tracking-tight">Foto Profil</h3>
                    <button type="button" onclick="closeManagePhotoModal()" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-[#F6F8FA] text-[#808897] hover:text-[#0D0D12] transition-all">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="p-4 space-y-2">
                    <label for="avatarInput" class="w-full flex items-center gap-3 p-3 rounded-xl border border-[#DFE1E7] hover:bg-[#E8EDF7] hover:border-[#8FA3D1] cursor-pointer transition-all group">
                        <div class="w-9 h-9 rounded-lg bg-[#E8EDF7] flex items-center justify-center text-[#0B266E] group-hover:bg-[#0B266E] group-hover:text-white transition-colors flex-shrink-0">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        </div>
                        <div>
                            <p class="text-[12px] font-semibold text-[#0D0D12]">Unggah Foto Baru</p>
                            <p class="text-[11px] text-[#808897]">JPG, PNG atau WebP (Maks. 2MB)</p>
                        </div>
                    </label>
                    <input type="file" id="avatarInput" class="hidden" accept="image/jpeg,image/png,image/webp">

                    @if($user->avatar_url)
                    <button type="button" onclick="openConfirmDeleteModal()" class="w-full flex items-center gap-3 p-3 rounded-xl border border-[#DFE1E7] hover:bg-[#FADAE1] hover:border-[#ED8296] transition-all group">
                        <div class="w-9 h-9 rounded-lg bg-[#FADAE1] flex items-center justify-center text-[#DF1C41] group-hover:bg-[#DF1C41] group-hover:text-white transition-colors flex-shrink-0">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                        </div>
                        <div>
                            <p class="text-[12px] font-semibold text-[#710E21]">Hapus Foto</p>
                            <p class="text-[11px] text-[#808897]">Kembali ke inisial nama</p>
                        </div>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── MODAL: Cropper ── --}}
    <div id="modalCrop" class="hidden fixed inset-0 z-[110] overflow-y-auto bg-[#0D0D12]/60 backdrop-blur-[2px]">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-[#DFE1E7]">
                <div class="px-5 py-4 border-b border-[#F0F1F4] flex items-center justify-between">
                    <h3 class="text-[12px] font-semibold text-[#0D0D12] tracking-tight">Sesuaikan Foto</h3>
                    <button type="button" onclick="closeCropModal()" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-[#F6F8FA] text-[#808897] transition-all">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="max-h-[400px] overflow-hidden rounded-xl bg-[#F6F8FA] border border-[#DFE1E7]">
                        <img id="imageToCrop" class="block max-w-full mx-auto">
                    </div>
                    <p class="text-[11px] text-[#A4ABB8] text-center mt-3">Gunakan mouse/jari untuk menggeser atau memperbesar foto</p>
                </div>
                <div class="px-5 py-4 bg-[#F6F8FA] border-t border-[#F0F1F4] flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeCropModal()"
                        class="px-4 py-2 text-[12px] font-medium text-[#666D80] bg-white border border-[#DFE1E7] rounded-lg hover:bg-[#F6F8FA] transition-all">
                        Batal
                    </button>
                    <button type="button" id="btnSaveCrop"
                        class="px-5 py-2 text-[12px] font-semibold text-white bg-[#0B266E] hover:bg-[#091958] rounded-lg transition-all">
                        Simpan Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── MODAL: Konfirmasi Hapus Foto ── --}}
    <div id="modalDeleteAvatar" class="hidden fixed inset-0 z-[120] overflow-y-auto bg-[#0D0D12]/60 backdrop-blur-[2px]">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-[#DFE1E7]">
                <div class="p-6 text-center">
                    <div class="w-12 h-12 rounded-xl bg-[#FADAE1] flex items-center justify-center mx-auto mb-4">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DF1C41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/>
                        </svg>
                    </div>
                    <h3 class="text-[14px] font-semibold text-[#0D0D12] mb-2">Hapus Foto Profil?</h3>
                    <p class="text-[12px] text-[#666D80] leading-relaxed">Foto profil Anda akan dihapus dan kembali menggunakan inisial nama. Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="px-5 py-4 bg-[#F6F8FA] border-t border-[#F0F1F4] flex gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeDeleteAvatarModal()"
                        class="flex-1 px-4 py-2.5 text-[12px] font-medium text-[#666D80] bg-white border border-[#DFE1E7] rounded-lg hover:bg-[#F6F8FA] transition-all">
                        Batal
                    </button>
                    <button type="button" id="btnConfirmDeleteAvatar"
                        class="flex-1 px-4 py-2.5 text-[12px] font-semibold text-white bg-[#DF1C41] hover:bg-[#95122B] rounded-lg transition-all">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── SCRIPTS ── --}}
    <script>
    let cropper;
    const avatarInput = document.getElementById('avatarInput');
    const imageToCrop = document.getElementById('imageToCrop');
    const modalManage = document.getElementById('modalManagePhoto');
    const modalCrop   = document.getElementById('modalCrop');
    const modalDelete = document.getElementById('modalDeleteAvatar');

    function openManagePhotoModal()  { modalManage.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeManagePhotoModal() { modalManage.classList.add('hidden');    document.body.style.overflow = ''; }
    function openConfirmDeleteModal()  { closeManagePhotoModal(); modalDelete.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeDeleteAvatarModal()  { modalDelete.classList.add('hidden');  document.body.style.overflow = ''; }

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
                    aspectRatio: 1, viewMode: 1, dragMode: 'move',
                    guides: false, center: true, highlight: false,
                    cropBoxMovable: false, cropBoxResizable: false,
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
        const btn          = this;
        const originalFile = avatarInput.files[0];
        const canvas       = cropper.getCroppedCanvas({ width: 400, height: 400 });
        btn.disabled       = true;
        btn.textContent    = 'Memproses…';

        canvas.toBlob(function (blob) {
            const formData = new FormData();
            formData.append('avatar', blob, 'avatar.webp');
            formData.append('avatar_original', originalFile);

            fetch("{{ route('profile.avatar.update') }}", {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') window.location.reload();
                else { alert(data.message || 'Gagal upload'); btn.disabled = false; btn.textContent = 'Simpan Foto'; }
            })
            .catch(() => { btn.disabled = false; btn.textContent = 'Simpan Foto'; });
        }, 'image/webp', 0.8);
    });

    document.getElementById('btnConfirmDeleteAvatar')?.addEventListener('click', function () {
        const btn       = this;
        btn.disabled    = true;
        btn.textContent = 'Menghapus…';

        fetch("{{ route('profile.avatar.destroy') }}", {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') window.location.reload();
            else { alert('Gagal menghapus foto'); btn.disabled = false; btn.textContent = 'Ya, Hapus'; }
        })
        .catch(() => { btn.disabled = false; btn.textContent = 'Ya, Hapus'; });
    });
    </script>

</x-sidebar>
</x-app-layout>
