import sys

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# Replace complex x-transition with native Alpine V3 shorthand which guarantees smooth rendering without missing tailwind JIT classes
old_trans = 'transform-origin: top;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2 scale-y-95" x-transition:enter-end="opacity-100 translate-y-0 scale-y-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-y-100" x-transition:leave-end="opacity-0 -translate-y-2 scale-y-95">'

new_trans = 'transform-origin: top;" x-transition>'

if old_trans in text:
    text = text.replace(old_trans, new_trans)

# Add will-change to padding transition for max fps
text = text.replace('transition: padding-bottom 0.3s cubic-bezier(0.4, 0, 0.2, 1);', 'transition: padding-bottom 0.3s cubic-bezier(0.4, 0, 0.2, 1); will-change: padding-bottom;')

text = text.replace('transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);', 'transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); will-change: transform;')

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print('Smoothed v2!')