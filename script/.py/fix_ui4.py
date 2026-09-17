import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# Add dynamic margin-bottom to the wrapper when opened!
old_wrapper = """<div x-data="{
                                    open: false,
                                    options: [5, 10, 20],
                                    perPage: '{{ request('per_page', 10) }}'
                                }"
                                class="custom-month-wrapper relative text-[13px]"
                                :class="open ? 'is-open' : ''"
                                @click.away="open = false"
                                style="font-family: inherit;">"""

new_wrapper = """<div x-data="{
                                    open: false,
                                    options: [5, 10, 20],
                                    perPage: '{{ request('per_page', 10) }}'
                                }"
                                class="custom-month-wrapper relative text-[13px]"
                                :class="open ? 'is-open' : ''"
                                :style="open ? 'margin-bottom: 110px !important' : ''"
                                @click.away="open = false"
                                style="font-family: inherit; transition: margin-bottom 0.1s ease;">"""

text = text.replace(old_wrapper, new_wrapper)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")