import sys

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# Replace padding transition condition
text = text.replace(":style=\"open ? { 'padding-bottom': '120px' } : {}\"", ":style=\"open ? { 'padding-bottom': '120px' } : { 'padding-bottom': '12px' }\"")
text = text.replace('transition: padding-bottom 0.2s ease-out;', 'transition: padding-bottom 0.25s cubic-bezier(0.4, 0, 0.2, 1); will-change: padding-bottom;')

# Replace dropdown alpine transition with pure CSS 
old_popup = """<div x-show="open" style="position: absolute; z-index: 9999; top: calc(100% + 4px); left: 0; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 4px; display: flex; flex-direction: column; gap: 2px; transform-origin: top;" x-transition.opacity.duration.200ms>"""

new_popup = """<div :style="open ? { 'opacity': '1', 'visibility': 'visible', 'transform': 'translateY(0) scaleY(1)' } : { 'opacity': '0', 'visibility': 'hidden', 'transform': 'translateY(-10px) scaleY(0.95)' }" style="position: absolute; z-index: 9999; top: calc(100% + 4px); left: 0; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 4px; display: flex; flex-direction: column; gap: 2px; transform-origin: top; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); pointer-events: auto;" :class="open ? 'pointer-events-auto' : 'pointer-events-none'">"""

if old_popup in text:
    text = text.replace(old_popup, new_popup)
else:
    # Try more robust replacement
    import re
    text = re.sub(r'<div x-show="open".*?x-transition.*?>', new_popup.replace(':class="open ? \'pointer-events-auto\' : \'pointer-events-none\'">', '>'), text, flags=re.DOTALL)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print('Smoothed FINALLY!')