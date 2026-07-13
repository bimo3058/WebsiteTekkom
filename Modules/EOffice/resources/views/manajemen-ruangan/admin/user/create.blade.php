<x-eoffice::manajemen-ruangan.layout pageTitle="Tambah User Baru">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Tambah User Baru</h1>
            <p class="mp-page-sub">Buat akun pengguna baru dan berikan hak akses (role) yang sesuai.</p>
        </div>
        <div>
            <a href="{{ route('eoffice.peminjaman.admin.user.index') }}" class="mp-btn secondary">Kembali</a>
        </div>
    </div>

    <div class="mp-card" style="padding: 24px; max-width: 600px;">
        <form action="{{ route('eoffice.peminjaman.admin.user.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px;">
                <label class="mp-label" style="display:block; margin-bottom:8px; font-weight:500;">Nama Lengkap</label>
                <input type="text" name="name" class="mp-input" style="width:100%;" required>
                @error('name') <span style="color:#D92D20; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label class="mp-label" style="display:block; margin-bottom:8px; font-weight:500;">Email</label>
                <input type="email" name="email" class="mp-input" style="width:100%;" required>
                @error('email') <span style="color:#D92D20; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label class="mp-label" style="display:block; margin-bottom:8px; font-weight:500;">Password</label>
                <input type="password" name="password" class="mp-input" style="width:100%;" required minlength="8">
                @error('password') <span style="color:#D92D20; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label class="mp-label" style="display:block; margin-bottom:8px; font-weight:500;">Role / Hak
                    Akses</label>
                <select name="role" class="mp-input" style="width:100%;">
                    <option value="">-- Pilih Role (Opsional) --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role') <span style="color:#D92D20; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="mp-btn primary">Simpan User</button>
            </div>
        </form>
    </div>

</x-eoffice::manajemen-ruangan.layout>