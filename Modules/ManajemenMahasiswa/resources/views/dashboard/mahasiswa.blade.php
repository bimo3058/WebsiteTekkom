<x-manajemenmahasiswa::layouts.master>
    <div class="flex justify-between items-center">
        <h1>Hello Mahasiswa</h1>
        <div class="flex items-center gap-3">

            {{-- Switch mode — hanya muncul kalau punya role lain --}}
            @if(auth()->user()->hasRole('pengurus_himpunan'))
                <form method="POST" action="{{ route('manajemenmahasiswa.switch.mode') }}">
                    @csrf
                    <input type="hidden" name="mode" value="pengurus_himpunan">
                    <button type="submit"
                        class="text-sm font-medium text-purple-600 hover:text-purple-800 border border-purple-200 hover:border-purple-400 px-3 py-1.5 rounded-lg transition-all">
                        Lihat sebagai Pengurus
                    </button>
                </form>
            @endif

            @if(auth()->user()->hasRole('alumni'))
                <form method="POST" action="{{ route('manajemenmahasiswa.switch.mode') }}">
                    @csrf
                    <input type="hidden" name="mode" value="alumni">
                    <button type="submit"
                        class="text-sm font-medium text-green-600 hover:text-green-800 border border-green-200 hover:border-green-400 px-3 py-1.5 rounded-lg transition-all">
                        Lihat sebagai Alumni
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>

    <p>Module: {!! config('manajemenmahasiswa.name') !!}</p>

    {{-- Permission Status --}}
    <div style="margin-top: 20px; padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
        <p style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Permission Status</p>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <span style="font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;
                {{ auth()->user()->can('kemahasiswaan.view') ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;' }}">
                <i class="fas fa-{{ auth()->user()->can('kemahasiswaan.view') ? 'check' : 'times' }}" style="margin-right:3px;"></i> View
            </span>
            <span style="font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;
                {{ auth()->user()->can('kemahasiswaan.edit') ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;' }}">
                <i class="fas fa-{{ auth()->user()->can('kemahasiswaan.edit') ? 'check' : 'times' }}" style="margin-right:3px;"></i> Edit
            </span>
            <span style="font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600;
                {{ auth()->user()->can('kemahasiswaan.delete') ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;' }}">
                <i class="fas fa-{{ auth()->user()->can('kemahasiswaan.delete') ? 'check' : 'times' }}" style="margin-right:3px;"></i> Delete
            </span>
        </div>
    </div>
</x-manajemenmahasiswa::layouts.master>