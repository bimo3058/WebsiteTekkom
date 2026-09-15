import sys

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace('transition: padding-bottom 0.1s ease;', 'transition: padding-bottom 0.3s cubic-bezier(0.4, 0, 0.2, 1);')

text = text.replace('gap: 2px;" x-transition>', 'gap: 2px; transform-origin: top;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2 scale-y-95" x-transition:enter-end="opacity-100 translate-y-0 scale-y-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 scale-y-100" x-transition:leave-end="opacity-0 -translate-y-2 scale-y-95">')

text = text.replace('transition: transform 0.2s;', 'transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);')

text = text.replace('cursor: pointer; user-select: none;"', 'cursor: pointer; user-select: none; transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;"')

text = text.replace(':style="open ? { \'color\': \'#666D80\' } : { \'color\': \'#9CA3AF\' }" style="font-weight: 400; color: #9CA3AF;"', ':style="open ? { \'color\': \'#666D80\' } : { \'color\': \'#9CA3AF\' }" style="font-weight: 400; color: #9CA3AF; transition: color 0.3s ease;"')
text = text.replace(':style="open ? { \'color\': \'#0B266E\', \'font-weight\': \'600\' } : { \'color\': \'#111827\', \'font-weight\': \'500\' }" style="color: #111827; font-weight: 500;"', ':style="open ? { \'color\': \'#0B266E\', \'font-weight\': \'600\' } : { \'color\': \'#111827\', \'font-weight\': \'500\' }" style="color: #111827; font-weight: 500; transition: color 0.3s ease;"')

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print('Smoothed!')