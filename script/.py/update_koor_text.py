with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# Replace the specific old text with the new one
old_str = "belum memiliki koordinator. Silahkan buka pendaftaran koordinator di menu Periode, atau tunjuk koordinator via form di samping."
new_str = "belum memiliki koordinator. Silahkan buka pendaftaran koordinator atau tunjuk koordinator via form di samping."
text = text.replace(old_str, new_str)

# Remove the max-width restriction to make it fill the available width
old_div = '<div style="font-size: 13px; color: #666D80; line-height: 1.6; max-width: 400px;">'
new_div = '<div style="font-size: 13px; color: #666D80; line-height: 1.6;">'
text = text.replace(old_div, new_div)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("done text updates")