{{--
    Dropdown Alpine.js bergaya shell global SITKOM (superadmin).

    Menggantikan <select> HTML bawaan di seluruh modul ini. Bentuknya disamakan dengan
    dropdown filter dashboard superadmin (resources/views/superadmin/users/_search_filter.blade.php):
    tombol putih h-32 radius 8, cincin fokus --c-primary-subtle, panel melayang dengan
    bayangan lembut, opsi terpilih tebal berwarna primary.

    Ditulis sebagai CSS biasa (bukan utilitas Tailwind milik versi global) karena layout
    modul ini memuat Bootstrap + CSS kustom, bukan Tailwind. Tiap token warna diberi nilai
    bawaan yang sama dengan palet global, jadi komponen ini tetap benar di halaman yang
    tidak mendeklarasikan token --c-* (mis. Pengaduan).

    ── Cara pakai ──────────────────────────────────────────────────────────────────────
    Isi komponen ini persis <option> yang dulu ada di dalam <select>-nya. Semua logika
    Blade (@foreach, @selected, old(), <optgroup>) dipakai apa adanya:

        <x-manajemenmahasiswa::ui.select name="bidang" id="filterBidang">
            <option value="semua">Semua Bidang</option>
            @foreach($bidangList as $b)
                <option value="{{ $b->id }}" @selected(request('bidang') == $b->id)>{{ $b->nama_bidang }}</option>
            @endforeach
        </x-manajemenmahasiswa::ui.select>

    ── Cara kerja ──────────────────────────────────────────────────────────────────────
    Yang terlihat dan bisa diklik sepenuhnya Alpine. Di belakangnya tetap ada <select>
    transparan (tidak bisa difokus tab, pointer-events:none) yang menjadi sumber data
    sekaligus lapisan kompatibilitas:

      • pengiriman form, atribut required/disabled, dan pesan validasi bawaan browser
        tetap jalan seperti sebelumnya;
      • JS lama yang membaca document.getElementById('x').value atau memasang
        addEventListener('change', ...) tidak perlu diubah — setiap pemilihan
        men-dispatch event 'change' asli yang ikut menjalankan atribut onchange;
      • daftar opsi dibaca Alpine dari <select> tersebut saat init, jadi markup opsi
        tidak digandakan.

    Kalau ada JS yang menyusun ulang <option> saat runtime (mis. dropdown bertingkat),
    beri tahu komponen supaya daftarnya ikut diperbarui:

        el.dispatchEvent(new CustomEvent('mk-select:refresh'));

    ── Props ───────────────────────────────────────────────────────────────────────────
      name      nama field yang dikirim form
      id        id elemen <select> di belakangnya; dipakai JS lama & atribut <label for>
      size      sm (32px, bawaan) | md (36px) | lg (44px)
      block     true (bawaan) melebar penuh; false mengikuti lebar isinya
      minWidth  lebar minimum tombol dalam px; berguna saat block=false
      invalid   true menandai field bermasalah; dipakai bersama @error(...)
      drop      down (bawaan) | up — arah panel terbuka, pakai "up" di footer tabel
      maxHeight tinggi maksimum panel dalam px sebelum di-scroll; bawaan 192

    class dan style menempel di pembungkus komponen (untuk keperluan tata letak, mis.
    "flex-shrink-0" atau "mb-3"). Atribut lain (required, disabled, onchange, data-*)
    diteruskan ke <select> di belakangnya.
--}}
@props([
    'name' => null,
    'id' => null,
    'size' => 'sm',
    'block' => true,
    'minWidth' => null,
    'invalid' => false,
    'drop' => 'down',
    'maxHeight' => 192,
])

@php
    // id wajib ada supaya tombol Alpine bisa menunjuk <select>-nya lewat aria-controls.
    $mkSelectId = $id ?: 'mk-select-' . substr(md5(($name ?? 'select') . uniqid('', true)), 0, 8);
    $mkIsBlock  = filter_var($block, FILTER_VALIDATE_BOOLEAN);
    $mkDropUp   = $drop === 'up';

    // class & style milik pemanggil dipakai untuk tata letak, jadi menempel di pembungkus.
    // Sisanya (required, disabled, onchange, data-*) milik <select> di belakangnya.
    $mkWrapperClass = trim('mk-select mk-select--' . $size
        . ($mkIsBlock ? ' mk-select--block' : '')
        . ($mkDropUp ? ' mk-select--up' : '')
        . (filter_var($invalid, FILTER_VALIDATE_BOOLEAN) ? ' mk-select--invalid' : '')
        . ' ' . $attributes->get('class', ''));

    $mkWrapperStyle = trim(($minWidth ? 'min-width: ' . (int) $minWidth . 'px;' : '')
        . ' ' . $attributes->get('style', ''));

    $mkSelectAttributes = $attributes->except(['class', 'style']);
@endphp

@once
    <style>
        .mk-select {
            --mks-h: 32px;
            --mks-radius: 8px;
            --mks-font: 12px;
            --mks-pad: 12px;
            position: relative;
            display: inline-block;
            vertical-align: top;
            text-align: left;
        }
        .mk-select--block { display: block; width: 100%; }
        .mk-select--md { --mks-h: 36px; --mks-font: 13px; }
        .mk-select--lg { --mks-h: 44px; --mks-radius: 10px; --mks-font: 13.5px; --mks-pad: 14px; }

        /* <select> asli: tetap dirender (bukan display:none) supaya pesan validasi bawaan
           browser punya tempat menempel, tapi tidak terlihat & tidak bisa diklik. */
        .mk-select-native {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: var(--mks-h);
            margin: 0;
            padding: 0;
            border: 0;
            opacity: 0;
            pointer-events: none;
            -webkit-appearance: none;
            appearance: none;
        }

        .mk-select-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            height: var(--mks-h);
            padding: 0 var(--mks-pad);
            background: #ffffff;
            border: 1px solid var(--c-border, #DFE1E7);
            border-radius: var(--mks-radius);
            color: var(--c-fg, #0D0D12);
            font-family: inherit;
            font-size: var(--mks-font);
            font-weight: 500;
            line-height: 1;
            text-align: left;
            white-space: nowrap;
            cursor: pointer;
            box-sizing: border-box;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .mk-select-btn:hover:not(:disabled) { border-color: var(--c-border-strong, #C1C7CF); }
        .mk-select-btn.is-open,
        .mk-select-btn:focus-visible {
            border-color: var(--c-primary, #0B266E);
            box-shadow: 0 0 0 3px var(--c-primary-subtle, rgba(11, 38, 110, 0.08));
            outline: none;
        }
        .mk-select-btn:disabled {
            background: var(--c-bg, #F6F8FA);
            color: var(--c-fg-placeholder, #808897);
            cursor: not-allowed;
        }
        /* Pengganti .is-invalid milik Bootstrap saat validasi server menolak field ini. */
        .mk-select--invalid .mk-select-btn { border-color: var(--c-error, #DF1C41); }
        .mk-select--invalid .mk-select-btn.is-open,
        .mk-select--invalid .mk-select-btn:focus-visible {
            box-shadow: 0 0 0 3px var(--c-error-subtle, #FADAE1);
        }

        .mk-select-value { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        /* Opsi bernilai kosong ("Pilih kategori…") tampil sebagai placeholder, sama seperti
           <select> bawaan yang belum dipilih. */
        .mk-select-value.is-placeholder { color: var(--c-fg-placeholder, #808897); font-weight: 400; }

        .mk-select-caret { flex-shrink: 0; color: var(--c-fg-placeholder, #808897); transition: transform 0.2s; }
        .mk-select-btn.is-open .mk-select-caret { transform: rotate(180deg); }

        .mk-select-menu {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 4px);
            z-index: 60;
            min-width: 100%;
            padding: 4px 0;
            background: #ffffff;
            border: 1px solid var(--c-border, #DFE1E7);
            border-radius: var(--mks-radius);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            overscroll-behavior: contain;
        }
        .mk-select--up .mk-select-menu { top: auto; bottom: calc(100% + 4px); }

        .mk-select-group {
            padding: 6px 12px 2px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--c-fg-muted, #666D80);
        }

        .mk-select-opt {
            display: block;
            width: 100%;
            padding: 6px 12px;
            background: none;
            border: none;
            color: var(--c-fg-sec, #353849);
            font-family: inherit;
            font-size: var(--mks-font);
            font-weight: 400;
            line-height: 1.45;
            text-align: left;
            cursor: pointer;
            transition: background 0.12s;
        }
        .mk-select-opt:hover:not(:disabled),
        .mk-select-opt.is-active:not(:disabled) { background: var(--c-bg, #F6F8FA); }
        .mk-select-opt.is-selected {
            font-weight: 600;
            color: var(--c-primary, #0B266E);
            background: rgba(11, 38, 110, 0.04);
        }
        .mk-select-opt:disabled { color: var(--c-fg-placeholder, #808897); cursor: not-allowed; }

        .mk-select-empty {
            padding: 8px 12px;
            font-size: var(--mks-font);
            color: var(--c-fg-muted, #666D80);
        }
    </style>

    <script>
        document.addEventListener('alpine:init', function () {
            /**
             * Dropdown yang menggantikan <select> HTML.
             *
             * Daftar opsi dan nilai terpilih selalu dibaca dari <select> di belakangnya
             * ($refs.native), jadi satu-satunya sumber kebenaran tetap DOM form — bukan
             * salinan data di sisi Alpine.
             */
            Alpine.data('mkSelect', function () {
                return {
                    open: false,
                    value: '',
                    options: [],
                    disabled: false,
                    activeIndex: -1,

                    init() {
                        this.syncFromNative();

                        // Dipanggil JS halaman setelah menyusun ulang <option> saat runtime
                        // (mis. dropdown bertingkat penyelenggara → capaian).
                        this.$refs.native.addEventListener('mk-select:refresh', () => this.syncFromNative());

                        // JS lain yang mengubah nilai <select> secara langsung lalu men-dispatch
                        // 'change' juga ikut tercermin di tampilan.
                        this.$refs.native.addEventListener('change', () => {
                            if (this.value !== this.$refs.native.value) this.syncFromNative();
                        });
                    },

                    /** Membaca ulang seluruh <option> dan nilai terpilih dari <select> asli. */
                    syncFromNative() {
                        const el = this.$refs.native;

                        this.options = Array.from(el.options).map(function (opt) {
                            const parent = opt.parentElement;

                            return {
                                value: opt.value,
                                label: opt.textContent.trim(),
                                disabled: opt.disabled,
                                group: parent && parent.tagName === 'OPTGROUP' ? parent.label : null,
                            };
                        });

                        this.value = el.value;

                        // Dibaca ke state supaya tombol ikut berubah saat JS halaman
                        // mengaktifkan/menonaktifkan <select>-nya (dropdown bertingkat).
                        this.disabled = el.disabled;
                    },

                    /** Label yang tampil di tombol; kosong bila <select> tidak punya opsi. */
                    get selectedLabel() {
                        const match = this.options.find((opt) => opt.value === this.value);

                        return match ? match.label : '';
                    },

                    /** Opsi bernilai kosong diperlakukan sebagai placeholder, seperti <select> bawaan. */
                    get isPlaceholder() {
                        return this.value === '';
                    },

                    /** Judul grup hanya dicetak sekali, di opsi pertama tiap <optgroup>. */
                    groupLabelFor(index) {
                        const current = this.options[index].group;

                        if (!current) return null;

                        return index === 0 || this.options[index - 1].group !== current ? current : null;
                    },

                    toggle() {
                        if (this.disabled) return;

                        this.open ? this.close() : this.show();
                    },

                    show() {
                        this.open = true;
                        this.activeIndex = this.options.findIndex((opt) => opt.value === this.value);

                        this.$nextTick(() => this.scrollActiveIntoView());
                    },

                    close() {
                        this.open = false;
                        this.activeIndex = -1;
                    },

                    /**
                     * Menetapkan pilihan, lalu men-dispatch 'change' asli pada <select> supaya
                     * atribut onchange dan listener JS lama tetap berjalan.
                     */
                    choose(option) {
                        if (option.disabled) return;

                        this.value = option.value;
                        this.$refs.native.value = option.value;
                        this.close();
                        this.$refs.button.focus();
                        this.$refs.native.dispatchEvent(new Event('change', { bubbles: true }));
                    },

                    /** Navigasi papan ketik: ↑/↓ berpindah, Enter memilih, Esc menutup. */
                    onKeydown(event) {
                        if (this.disabled) return;

                        if (!this.open) {
                            if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(event.key)) {
                                event.preventDefault();
                                this.show();
                            }

                            return;
                        }

                        switch (event.key) {
                            case 'ArrowDown':
                                event.preventDefault();
                                this.move(1);
                                break;
                            case 'ArrowUp':
                                event.preventDefault();
                                this.move(-1);
                                break;
                            case 'Home':
                                event.preventDefault();
                                this.activeIndex = 0;
                                this.scrollActiveIntoView();
                                break;
                            case 'End':
                                event.preventDefault();
                                this.activeIndex = this.options.length - 1;
                                this.scrollActiveIntoView();
                                break;
                            case 'Enter':
                            case ' ':
                                event.preventDefault();
                                if (this.options[this.activeIndex]) this.choose(this.options[this.activeIndex]);
                                break;
                            case 'Escape':
                                event.preventDefault();
                                this.close();
                                break;
                            case 'Tab':
                                this.close();
                                break;
                        }
                    },

                    /** Memindahkan sorotan ke opsi aktif berikutnya, melewati yang disabled. */
                    move(step) {
                        const total = this.options.length;

                        if (!total) return;

                        let next = this.activeIndex;

                        for (let i = 0; i < total; i++) {
                            next = (next + step + total) % total;

                            if (!this.options[next].disabled) break;
                        }

                        this.activeIndex = next;
                        this.scrollActiveIntoView();
                    },

                    scrollActiveIntoView() {
                        const el = this.$refs.menu ? this.$refs.menu.querySelector('.is-active') : null;

                        if (el) el.scrollIntoView({ block: 'nearest' });
                    },
                };
            });
        });
    </script>
@endonce

<div class="{{ $mkWrapperClass }}"
     @if($mkWrapperStyle) style="{{ $mkWrapperStyle }}" @endif
     x-data="mkSelect"
     @click.outside="close()"
     @keydown.escape.window="close()">

    {{-- <select> asli: pembawa nilai form, target validasi browser, dan sumber daftar opsi. --}}
    <select {{ $mkSelectAttributes->merge(['class' => 'mk-select-native']) }}
            @if($name) name="{{ $name }}" @endif
            id="{{ $mkSelectId }}"
            x-ref="native"
            tabindex="-1"
            aria-hidden="true">
        {{ $slot }}
    </select>

    <button type="button"
            class="mk-select-btn"
            :class="{ 'is-open': open }"
            x-ref="button"
            role="combobox"
            aria-haspopup="listbox"
            :aria-expanded="open"
            aria-controls="{{ $mkSelectId }}-menu"
            :disabled="disabled"
            @click="toggle()"
            @keydown="onKeydown($event)">

        <span class="mk-select-value" :class="{ 'is-placeholder': isPlaceholder }" x-text="selectedLabel"></span>

        <svg class="mk-select-caret" width="12" height="12" fill="none" stroke="currentColor"
             viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 9l6 6 6-6"/>
        </svg>
    </button>

    <div class="mk-select-menu"
         id="{{ $mkSelectId }}-menu"
         role="listbox"
         x-ref="menu"
         x-show="open"
         style="display: none; max-height: {{ (int) $maxHeight }}px;"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <template x-for="(option, index) in options" :key="option.value + '-' + index">
            <div>
                <template x-if="groupLabelFor(index)">
                    <div class="mk-select-group" x-text="groupLabelFor(index)"></div>
                </template>

                <button type="button"
                        class="mk-select-opt"
                        role="option"
                        :class="{
                            'is-selected': option.value === value,
                            'is-active': index === activeIndex,
                        }"
                        :aria-selected="option.value === value"
                        :disabled="option.disabled"
                        @click="choose(option)"
                        @mouseenter="activeIndex = index"
                        x-text="option.label"></button>
            </div>
        </template>

        <template x-if="options.length === 0">
            <div class="mk-select-empty">Tidak ada pilihan</div>
        </template>
    </div>
</div>
