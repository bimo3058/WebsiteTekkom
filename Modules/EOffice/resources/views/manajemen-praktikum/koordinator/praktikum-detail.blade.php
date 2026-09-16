<x-eoffice::manajemen-praktikum.layout pageTitle="{{ $praktikum->nama }} / Dashboard">
    
    <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />

    {{-- Content: Pengumuman --}}

    <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 4px;">
        @forelse($pengumumans as $pg)
            @php
                $nameParts = explode(' ', $pg->user?->name ?? 'SY');
                $initials  = strtoupper(substr($nameParts[0] ?? 'S', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'Y', 0, 1));
                $isSistem  = $pg->tipe_sistem !== null;
            @endphp
            <div style="background: #fff; border: 1px solid var(--c-border); border-radius: 8px; padding: 16px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                
                {{-- User Info & Timestamp --}}
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #D1D5DB; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #4B5563; font-size: 12px;">
                        @if($isSistem)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <div>
                        <div style="font-size: 13px; font-weight: 600; color: #111827;">{{ $isSistem ? 'Sistem' : ($pg->user?->name ?? '—') }}</div>
                        <div style="font-size: 11px; color: #6B7280; margin-top: 1px;">{{ $pg->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}</div>
                    </div>
                </div>

                {{-- Heading & Body --}}
                <h3 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0 0 3px 0;">{{ $pg->judul }}</h3>
                <div style="font-size: 13px; color: #374151; line-height: 1.5; white-space: pre-wrap;">{{ $pg->konten }}</div>
                
            </div>
        @empty
            <div style="padding: 48px; text-align: center; border: 1px solid var(--c-border); border-radius: 8px; background: #fff;">
                <div style="font-size: 14px; font-weight: 600; color: #111827;">Belum Ada Pengumuman</div>
                <div style="font-size: 13px; color: #6B7280; margin-top: 4px;">Belum ada pengumuman yang diterbitkan pada praktikum ini.</div>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($pengumumans->hasPages())
            <div style="padding: 16px 0;">
                {{ $pengumumans->links() }}
            </div>
        @endif
    </div>

</x-eoffice::manajemen-praktikum.layout>
