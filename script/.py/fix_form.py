import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# 1. Fix Margin and Title
text = text.replace(
    """<div class="mp-card" style="margin-top: 24px; flex-shrink: 0;">
                <div class="mp-card-header">
                    <span class="mp-card-title">Pendaftaran Koordinator</span>""",
    """<div class="mp-card" style="margin-top: 9px; flex-shrink: 0;">
                <div class="mp-card-header">
                    <span class="mp-card-title">Form Pendaftaran Koordinator</span>"""
)

# 2. Fix Textarea Deskripsi
text = text.replace(
    """<textarea name="deskripsi" class="mp-input w-full" rows="3" placeholder="Tuliskan deskripsi atau link soal tes..." style="border-radius: 8px; font-size:13px; padding: 10px 14px;"></textarea>""",
    """<textarea name="deskripsi" class="mp-input w-full" rows="1" placeholder="Tuliskan deskripsi atau link soal tes..." style="border-radius: 8px; font-size:13px; padding: 10px 14px; resize: vertical; min-height: 42px; max-height: 120px;"></textarea>"""
)

# 3. Apply classes to Date inputs for flatpickr targeting
text = text.replace(
    """<input type="datetime-local" name="dibuka_pada" class="mp-input w-full" style="border-radius: 8px; font-size:13px; padding: 10px 14px; color: #6B7280;">""",
    """<input type="text" name="dibuka_pada" class="mp-input w-full date-picker" placeholder="Pilih tanggal dan waktu..." style="border-radius: 8px; font-size:13px; padding: 10px 14px; color: #6B7280; background-color: #fff;">"""
)

text = text.replace(
    """<input type="datetime-local" name="ditutup_pada" class="mp-input w-full" style="border-radius: 8px; font-size:13px; padding: 10px 14px; color: #6B7280;" required>""",
    """<input type="text" name="ditutup_pada" class="mp-input w-full date-picker" placeholder="Pilih tanggal dan waktu..." style="border-radius: 8px; font-size:13px; padding: 10px 14px; color: #6B7280; background-color: #fff;" required>"""
)

# 4. Inject Flatpickr CSS & JS before closing section
flatpickr_setup = """

            <!-- Flatpickr Setup -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
            <style>
                .flatpickr-calendar { font-family: inherit; font-size: 13px; }
                .flatpickr-day.selected { background: #0B266E; border-color: #0B266E; }
                .flatpickr-monthDropdown-months .flatpickr-monthDropdown-month {
                    color: inherit;
                }
                .flatpickr-monthDropdown-months {
                    background: transparent;
                }
                select.flatpickr-monthDropdown-months:focus {
                    outline: none;
                }
                .flatpickr-monthDropdown-months option:checked {
                    background-color: #0B266E;
                    color: #fff;
                }
            </style>
            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    flatpickr(".date-picker", {
                        enableTime: true,
                        dateFormat: "Y-m-d H:i",
                        time_24hr: true
                    });
                });
            </script>
            {{-- ORIGINAL SECTION: Filter Pendaftaran --}}
"""

text = text.replace("{{-- ORIGINAL SECTION: Filter Pendaftaran --}}", flatpickr_setup)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")