with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Fix cur-month font size (was not replaced - let's check the actual string)
text = text.replace(
    '.flatpickr-current-month .cur-month {\n          font-size: 14px !important;\n          font-weight: 600 !important;\n          color: #353849 !important;\n          padding-top: 0px !important;\n          cursor: pointer;\n      }',
    '.flatpickr-current-month .cur-month {\n          font-size: 12px !important;\n          font-weight: 600 !important;\n          color: #353849 !important;\n          padding-top: 0px !important;\n          cursor: pointer;\n      }'
)

# 2. Fix cur-year font size
text = text.replace(
    '    .flatpickr-current-month input.cur-year {\n          font-size: 14px !important;',
    '    .flatpickr-current-month input.cur-year {\n          font-size: 12px !important;'
)

# 3. Add show/hide animation to custom-month-dropdown 
old_dropdown_css = """    .custom-month-dropdown {
        position: absolute;
        background: #fff;
        border: 1px solid #DFE1E7;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(11, 38, 110, 0.12);
        z-index: 99999;"""

new_dropdown_css = """    .custom-month-dropdown {
        position: absolute;
        background: #fff;
        border: 1px solid #DFE1E7;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(11, 38, 110, 0.12);
        z-index: 99999;
        display: none;
        opacity: 0;
        transform: translateY(-6px);
        transition: opacity 0.18s ease, transform 0.18s ease;"""

text = text.replace(old_dropdown_css, new_dropdown_css)

# Add the .show state style after the dropdown block 
text = text.replace(
    '.custom-month-item.active {',
    '''.custom-month-dropdown.show {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    .custom-month-item.active {'''
)

# 4. Fix arrow animation CSS
old_arr = """    .custom-month-arrow {
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        transition: transform 0.25s ease;
    }
    .custom-month-arrow.open {
        transform: rotate(180deg);
    }"""
new_arr = """    .custom-month-arrow {
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .custom-month-arrow.open {
        transform: rotate(180deg);
    }"""
text = text.replace(old_arr, new_arr)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("calendar css fixed")