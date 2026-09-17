import sys

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# Change dropdown transition to pure opacity to prevent scale-jitter
text = text.replace('x-transition>', 'x-transition.opacity.duration.200ms>')

# Remove problematic will-change on padding-bottom which forces CPU/GPU stalling
text = text.replace('will-change: padding-bottom;', '')

# Make the padding transition snappier so it doesn't drag out the reflow
text = text.replace('transition: padding-bottom 0.3s cubic-bezier(0.4, 0, 0.2, 1);', 'transition: padding-bottom 0.2s ease-out;')

# Remove will-change on transform
text = text.replace('will-change: transform;', '')
text = text.replace('transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);', 'transition: transform 0.2s ease-out;')

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print('Smoothed v3!')