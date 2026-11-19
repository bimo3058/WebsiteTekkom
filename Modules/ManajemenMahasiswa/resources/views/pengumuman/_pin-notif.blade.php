{{--
    Pop-up hasil Pin Pribadi / Pin Global pengumuman.

    PengumumanController::personalPin() & pin() mengirim flash `pin_notif`
    ({title, message, variant}) lalu redirect kembali; partial ini memunculkannya
    lewat dialog global mkNotify() (components/ui/dialog, dipasang di tiap layout).
    Dipasang di semua halaman yang punya tombol pin: daftar & detail pengumuman
    (admin, pengurus, mahasiswa/alumni).
--}}
@if(session('pin_notif'))
    @push('scripts')
        <script>
            (function () {
                var data = @json(session('pin_notif'));
                var percobaan = 0;

                // mkNotify() baru memakai dialog SITKOM setelah Alpine menginisialisasi
                // komponennya (window.__mkDialog); sebelum itu ia jatuh ke alert() bawaan
                // browser. Tunggu sebentar sampai dialognya siap (maks. ±3 detik).
                function tampilkan() {
                    if (window.__mkDialog && typeof window.mkNotify === 'function') {
                        window.mkNotify(data);
                    } else if (percobaan++ < 60) {
                        setTimeout(tampilkan, 50);
                    }
                }

                document.addEventListener('DOMContentLoaded', tampilkan);
            })();
        </script>
    @endpush
@endif
