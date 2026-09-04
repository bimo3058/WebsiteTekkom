import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# Replace the custom-month-dropdown styles
old_css = """                .custom-month-dropdown {
                    position: absolute;
                    top: 100%;
                    left: 50%;
                    transform: translateX(-50%);
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    width: 120px;
                    max-height: 200px;
                    overflow-y: auto;
                    z-index: 1000;
                    opacity: 0;
                    visibility: hidden;
                    transition: opacity 0.2s, visibility 0.2s;
                    padding: 8px;
                    display: flex;
                    flex-direction: column;
                    gap: 2px;
                }
                .custom-month-wrapper.is-open .custom-month-dropdown {
                    opacity: 1;
                    visibility: visible;
                }
                
                .custom-month-item {
                    padding: 8px;
                    border-radius: 6px;
                    text-align: center;
                    transition: all 0.2s;
                    color: #0B266E;
                }"""

new_css = """                .custom-month-dropdown {
                    position: absolute;
                    top: 100%;
                    left: 50%;
                    transform: translateX(-50%);
                    background-color: #ffffff !important;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
                    border: 1px solid #e5e7eb;
                    width: 130px;
                    max-height: 220px;
                    overflow-y: auto;
                    z-index: 99999 !important;
                    opacity: 0;
                    visibility: hidden;
                    transition: opacity 0.15s, visibility 0.15s;
                    padding: 8px;
                    display: flex;
                    flex-direction: column;
                    gap: 4px;
                    margin-top: 8px; /* Gap from the header */
                }
                .custom-month-wrapper.is-open .custom-month-dropdown {
                    opacity: 1;
                    visibility: visible;
                }
                
                .custom-month-item {
                    padding: 10px 8px;
                    border-radius: 6px;
                    text-align: center;
                    transition: background 0.1s, color 0.1s;
                    color: #0B266E;
                    font-size: 13px !important;
                    font-weight: 500 !important;
                    line-height: 1 !important;
                }"""

text = text.replace(old_css, new_css)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")