<x-eoffice::manajemen-ruangan.layout pageTitle="Edit User">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Edit User</h1>
            <p class="mp-page-sub">Ubah profil, reset password, atau atur ulang role untuk akun {{ $user->name }}.</p>
        </div>
        <div>
            <a href="{{ route('eoffice.peminjaman.admin.user.index') }}" class="mp-btn secondary">Kembali</a>
        </div>
    </div>

    <div class="mp-card" style="padding: 24px; max-width: 600px;">
        <form action="{{ route('eoffice.peminjaman.admin.user.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label class="mp-label" style="display:block; margin-bottom:8px; font-weight:500;">Nama Lengkap</label>
                <input type="text" name="name" class="mp-input" style="width:100%;"
                    value="{{ old('name', $user->name) }}" required>
                @error('name') <span style="color:#D92D20; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label class="mp-label" style="display:block; margin-bottom:8px; font-weight:500;">Email</label>
                <input type="email" name="email" class="mp-input" style="width:100%;"
                    value="{{ old('email', $user->email) }}" required>
                @error('email') <span style="color:#D92D20; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label class="mp-label" style="display:block; margin-bottom:8px; font-weight:500;">Ubah Password</label>
                <input type="password" name="password" class="mp-input" style="width:100%;"
                    placeholder="Abaikan jika tidak ingin mengubah password" minlength="8">
                <small style="color:#666D80; font-size:12px; display:inline-block; margin-top:4px;">Isi hanya jika ingin
                    mereset/mengganti password akun ini.</small>
                @error('password') <span style="color:#D92D20; font-size:12px; display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label class="mp-label" style="display:block; margin-bottom:8px; font-weight:500;">Role / Hak
                    Akses</label>
                <select name="role" class="mp-input" style="width:100%;">
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->roles->contains('name', $role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role') <span style="color:#D92D20; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="mp-btn primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

</x-eoffice::manajemen-ruangan.layout>