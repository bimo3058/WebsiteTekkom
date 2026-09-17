import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Wrap the contents inside a scrollable div
# Find the insertion point right after @else (for !praktikum)
parts = text.split('@else', 1)
if len(parts) == 2:
    # Wrap everything after @else up to the last @endif
    tail = parts[1]
    
    # We will just replace specific flex-1 min-h-0 strings to let the page scroll naturally
    # First, Kelola modul grid
    tail = tail.replace('<div class="grid grid-cols-[360px_1fr] gap-[14px] flex-1 min-h-0">', '<div class="grid grid-cols-[360px_1fr] gap-[14px] items-start mb-8">')
    
    # Kelola modul list card
    tail = tail.replace('<div class="mp-card min-h-0">', '<div class="mp-card">')
    
    # Sec-head margin
    tail = tail.replace('<div class="sec-head">', '<div class="sec-head" style="margin-top: 2rem;">')
    
    # Distribusi flex container
    tail = tail.replace('<div class="flex gap-[14px] flex-1 min-h-0">', '<div class="flex gap-[14px] items-start mb-8">')
    
    # Panel Kiri
    tail = tail.replace('<div class="mp-card flex-1 min-h-0">', '<div class="mp-card">')
    
    # Panel Kanan flex wrapper
    tail = tail.replace('<div class="flex flex-col gap-[14px] flex-1 min-w-0 overflow-y-auto">', '<div class="flex flex-col gap-[14px] flex-1 min-w-0">')
    
    # Re-assemble
    text = parts[0] + '@else\n<div class="flex flex-col flex-1 overflow-y-auto pr-2 pb-6 min-h-0">\n' + tail
    
    # Find last @endif before layout closing
    if "</x-eoffice::manajemen-praktikum.layout>" in text:
        text = text.replace("</x-eoffice::manajemen-praktikum.layout>", "</div>\n</x-eoffice::manajemen-praktikum.layout>")

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Fixed layout overlap!")
