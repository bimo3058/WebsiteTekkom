import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

old_wrapper = """<div x-data="{
                                    open: false,
                                    options: [5, 10, 20],
                                    perPage: '{{ request('per_page', 10) }}'
                                }"
                                class="custom-month-wrapper relative text-[13px]"
                                :class="open ? 'is-open' : ''"
                                :style="open ? 'margin-bottom: 110px !important' : ''"
                                @click.away="open = false"
                                style="font-family: inherit; transition: margin-bottom 0.1s ease;">
                                
                                <button type="button" @click="open = !open" 
                                        style="display: flex; align-items: center; justify-content: space-between; height: 32px; border: 1px solid #75839C; border-radius: 6px; padding: 0 12px; background: white; white-space: nowrap; cursor: pointer; gap: 8px; width: 100%;">
                                    <span style="color: #666D80; font-size: 13px; font-weight: 400;">Per halaman <span style="font-weight: 400; color: #0B266E; margin-left: 4px;" x-text="perPage"></span></span>
                                    <svg class="custom-month-chevron" style="color: #0B266E; width: 12px; height: 12px; stroke-width: 1.5; margin-top:2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                                
                                <div class="custom-month-dropdown" style="top: calc(100% + 4px); left: 0; transform: none; min-width: 100%; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding: 4px; display: flex; flex-direction: column; gap: 2px;">"""

new_wrapper = """<div x-data="{
                                    open: false,
                                    options: [5, 10, 20],
                                    perPage: '{{ request('per_page', 10) }}'
                                }"
                                class="custom-month-wrapper relative text-[13px]"
                                :class="open ? 'is-open' : ''"
                                :style="open ? 'margin-bottom: 115px !important' : ''"
                                @click.away="open = false"
                                style="font-family: inherit; transition: margin-bottom 0.1s ease;">
                                
                                <button type="button" @click="open = !open" 
                                        :style="open ? 'border: 1px solid #0B266E; background: #F8FAFC;' : 'border: 1px solid #E5E7EB; background: white;'"
                                        style="display: flex; align-items: center; justify-content: space-between; height: 32px; border-radius: 6px; padding: 0 12px; white-space: nowrap; cursor: pointer; gap: 8px; width: 100%; transition: all 0.2s;">
                                    <span :style="open ? 'color: #666D80;' : 'color: #9CA3AF;'" style="font-size: 13px; font-weight: 400; transition: color 0.1s;">
                                        Per halaman <span :style="open ? 'color: #0B266E; font-weight: 600;' : 'color: #111827; font-weight: 500;'" style="margin-left: 4px;" x-text="perPage"></span>
                                    </span>
                                    <svg class="custom-month-chevron" :style="open ? 'color: #0B266E;' : 'color: #111827;'" style="width: 12px; height: 12px; stroke-width: 1.5; margin-top:2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                                
                                <div class="custom-month-dropdown" style="top: calc(100% + 4px); left: 0; transform: none; min-width: 100%; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding: 4px; display: flex; flex-direction: column; gap: 2px;">"""

text = text.replace(old_wrapper, new_wrapper)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")