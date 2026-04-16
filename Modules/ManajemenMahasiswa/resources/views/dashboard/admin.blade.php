<x-manajemenmahasiswa::layouts.master>
    <div class="flex justify-between items-center">
        <h1>Hello Admin</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">{{ __('Log Out') }}</button>
        </form>
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