import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# 1. Search Input
old_search = """<input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa..." style="width: 100%; border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px 12px 10px 36px; font-size: 13px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#0B266E'" onblur="this.style.borderColor='#D1D5DB'">"""
new_search = """<input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa..." style="width: 100%; height: 38px; box-sizing: border-box; border: 1px solid #D1D5DB; border-radius: 8px; padding: 0 12px 0 36px; font-size: 13px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#0B266E'" onblur="this.style.borderColor='#D1D5DB'">"""
text = text.replace(old_search, new_search)

# 2. Selects
svg_arrow = "background-image: url('data:image/svg+xml;utf8,<svg width=\"12\" height=\"12\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%23353849\" stroke-width=\"2.5\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"></polyline></svg>'); background-repeat: no-repeat; background-position: right 14px center; background-size: 14px;"

old_sort = """<select name="sort" style="border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px 12px; font-size: 13px; outline:none; background:white; min-width: 140px; appearance: auto; cursor: pointer;">"""
new_sort = f"""<select name="sort" style="height: 38px; box-sizing: border-box; border: 1px solid #D1D5DB; border-radius: 8px; padding: 0 36px 0 12px; font-size: 13px; outline:none; background-color:white; min-width: 130px; appearance: none; cursor: pointer; {svg_arrow}">"""
text = text.replace(old_sort, new_sort)

old_status = """<select name="status_dosen" style="border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px 12px; font-size: 13px; outline:none; background:white; min-width: 140px; appearance: auto; cursor: pointer;">"""
new_status = f"""<select name="status_dosen" style="height: 38px; box-sizing: border-box; border: 1px solid #D1D5DB; border-radius: 8px; padding: 0 36px 0 12px; font-size: 13px; outline:none; background-color:white; min-width: 130px; appearance: none; cursor: pointer; {svg_arrow}">"""
text = text.replace(old_status, new_status)

# 3. Filter Button
old_btn = """<button type="submit" style="background:#0B266E; color:white; border-radius: 8px; padding: 10px 16px; font-weight:600; font-size:13px; display:flex; gap:6px; align-items:center; border:none; cursor:pointer;" onmouseover="this.style.background='#081b4f'" onmouseout="this.style.background='#0B266E'">"""
new_btn = """<button type="submit" style="height: 38px; box-sizing: border-box; background:#0B266E; color:white; border-radius: 8px; padding: 0 16px; font-weight:600; font-size:13px; display:flex; gap:6px; align-items:center; justify-content:center; border:none; cursor:pointer;" onmouseover="this.style.background='#081b4f'" onmouseout="this.style.background='#0B266E'">"""
text = text.replace(old_btn, new_btn)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")