{{--
    Palette khusus Manajemen Kegiatan.
    Nilai mengikuti token aktif pada shell global SITKOM
    (resources/views/components/sidebar.blade.php), tanpa memengaruhi halaman
    SIMENMA lain yang memakai layout mahasiswa yang sama.
--}}
<style>
    :root {
        --c-primary: #0B266E;
        --c-primary-hover: #091958;
        --c-primary-subtle: rgba(11, 38, 110, 0.08);
        --c-primary-border: #5C78B8;
        --c-primary-shadow: rgba(11, 38, 110, 0.12);
        --c-primary-shadow-strong: rgba(11, 38, 110, 0.30);

        --c-bg: #F6F8FA;
        --c-surface: #FFFFFF;
        --c-surface-subtle: #F8F9FB;
        --c-surface-muted: #ECEFF3;
        --c-fg: #0D0D12;
        --c-fg-sec: #353849;
        --c-fg-muted: #666D80;
        --c-fg-placeholder: #808897;
        --c-border: #DFE1E7;
        --c-border-strong: #C1C7CF;

        --c-success: #287F6E;
        --c-success-subtle: #DDF2EE;
        --c-success-border: rgba(40, 127, 110, 0.25);
        --c-warning: #956321;
        --c-warning-subtle: #F9ECCB;
        --c-error: #DF1C41;
        --c-error-subtle: #FADAE1;
        --c-sky: #0C4D6E;
        --c-sky-subtle: #D1F0F9;

        --shadow-card: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
    }
</style>
