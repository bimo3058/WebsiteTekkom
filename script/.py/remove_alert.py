import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# Pattern for the Info Alert block
alert_pattern = r'\{\{-- Info Alert --\}\}\s*<div class="mp-alert info flex-shrink-0">\s*<strong>Alur:</strong>.*?\s*</div>'

text = re.sub(alert_pattern, '', text)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("Alert removed")