<x-eoffice::manajemen-praktikum.layout pageTitle="Pendaftaran Asisten Praktikum">

<div>
    <div class="text-[20px] font-bold text-[#0D0D12]">Pendaftaran Asisten Praktikum</div>
    <div class="text-[12px] text-[#666D80] mt-[2px]">Daftarkan diri sebagai calon asisten untuk semester depan</div>
</div>

@if($statusPendaftaran)
{{-- Status Card --}}
<div class="bg-white border border-[#DFE1E7] rounded-[14px] p-6 shadow-[0_1px_2px_rgba(228,229,231,.24)] flex-shrink-0">
    <div class="flex items-center gap-4">
        @if($statusPendaftaran->status === 'pending')
        <div class="w-[48px] h-[48px] rounded-full bg-[#F9ECCB] flex items-center justify-center flex-shrink-0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#D39C3D" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="text-[15px] font-bold text-[#0D0D12]">Pendaftaran Sedang Diproses</div>
            <div class="text-[12px] text-[#666D80] mt-1">Praktikum: {{ $statusPendaftaran->praktikum?->nama ?? '—' }}</div>
            <div class="text-[11px] text-[#A4ABB8] mt-1">Didaftarkan: {{ $statusPendaftaran->created_at?->format('d M Y') }}</div>
        </div>
        @elseif($statusPendaftaran->status === 'approved')
        <div class="w-[48px] h-[48px] rounded-full bg-[#DDF2EE] flex items-center justify-center flex-shrink-0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#40C4AA" stroke-width="2.2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <div class="text-[15px] font-bold text-[#0D0D12]">Pendaftaran Diterima!</div>
            <div class="text-[12px] text-[#666D80] mt-1">Praktikum: {{ $statusPendaftaran->praktikum?->nama ?? '—' }}</div>
            <div class="text-[12px] text-[#40C4AA] font-semibold mt-1">Selamat! Anda telah diterima sebagai Asisten Praktikum.</div>
        </div>
        @else
        <div class="w-[48px] h-[48px] rounded-full bg-[#FADAE1] flex items-center justify-center flex-shrink-0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DF1C41" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </div>
        <div>
            <div class="text-[15px] font-bold text-[#0D0D12]">Pendaftaran Ditolak</div>
            <div class="text-[12px] text-[#666D80] mt-1">Praktikum: {{ $statusPendaftaran->praktikum?->nama ?? '—' }}</div>
            <div class="text-[12px] text-[#DF1C41] mt-1">{{ $statusPendaftaran->alasan_penolakan ?? 'Tidak ada keterangan.' }}</div>
        </div>
        @endif
    </div>
</div>
@endif

@if(!$statusPendaftaran || $statusPendaftaran->status === 'rejected')
{{-- Form Pendaftaran --}}
<div class="bg-white border border-[#DFE1E7] rounded-[14px] p-6 shadow-[0_1px_2px_rgba(228,229,231,.24)]">
    <div class="font-bold text-[15px] text-[#0D0D12] mb-4">Form Pendaftaran Asisten Praktikum</div>
    <form method="POST" action="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Praktikum <span class="text-red-500">*</span></label>
                <select name="praktikum_id" required class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#0B266E]">
                    <option value="">— Pilih Praktikum —</option>
                    @foreach($praktikumList ?? [] as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->kode }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">IPK Terakhir <span class="text-red-500">*</span></label>
                <input type="number" name="ipk" step="0.01" min="0" max="4" required placeholder="3.50"
                       class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#0B266E]">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pengalaman / Motivasi <span class="text-red-500">*</span></label>
                <textarea name="motivasi" rows="4" required placeholder="Tuliskan pengalaman dan motivasi Anda mendaftar sebagai asprak..."
                          class="w-full border border-[#DFE1E7] rounded-[8px] px-3 py-[9px] text-[13px] focus:outline-none focus:border-[#0B266E] resize-none"></textarea>
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">CV / Portofolio (PDF)</label>
                <input type="file" name="cv" accept=".pdf,.docx"
                       class="w-full text-[13px] border border-[#DFE1E7] rounded-[8px] px-3 py-[8px] focus:outline-none focus:border-[#0B266E]">
                <div class="text-[11px] text-[#A4ABB8] mt-1">Opsional. Maks. 5MB.</div>

                <div class="mt-4">
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jadwal Ketersediaan</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="jadwal[]" value="{{ $hari }}" class="accent-[#0B266E]">
                            <span class="text-[12px] text-[#353849]">{{ $hari }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="mt-4 bg-[#FADAE1] border border-[#DF1C41] rounded-[8px] px-4 py-3">
            @foreach($errors->all() as $e)
            <div class="text-[12px] text-[#7C1028]">• {{ $e }}</div>
            @endforeach
        </div>
        @endif

        <div class="flex justify-end mt-5">
            <button type="submit"
                    class="px-6 py-[10px] rounded-[10px] bg-[#0B266E] text-white text-[13px] font-semibold border-none cursor-pointer hover:bg-[#0a1f5c]">
                Kirim Pendaftaran
            </button>
        </div>
    </form>
</div>
@endif

{{-- Syarat & Ketentuan --}}
<div class="bg-white border border-[#DFE1E7] rounded-[14px] p-5 shadow-[0_1px_2px_rgba(228,229,231,.24)]">
    <div class="font-bold text-[14px] text-[#0D0D12] mb-3">Syarat & Ketentuan Asprak</div>
    <div class="grid grid-cols-2 gap-3">
        @foreach([
            'IPK minimal 3.00',
            'Pernah mengikuti praktikum yang didaftar',
            'Tidak sedang mengambil praktikum yang sama',
            'Bersedia hadir sesuai jadwal yang ditentukan',
            'Menyetujui peraturan asisten praktikum',
            'Aktif sebagai mahasiswa UNDIP',
        ] as $syarat)
        <div class="flex items-center gap-2">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#40C4AA" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
            <span class="text-[12px] text-[#353849]">{{ $syarat }}</span>
        </div>
        @endforeach
    </div>
</div>

</x-eoffice::manajemen-praktikum.layout>
