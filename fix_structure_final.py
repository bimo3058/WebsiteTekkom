import sys

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# I am going to extract the x-data block correctly.
# Find the start of x-data
start_idx = text.find('<div x-data="{')
if start_idx == -1:
    print("Could not find x-data")
    sys.exit(1)

# The x-data is currently on the flex container. I will separate them!
old_wrapper_start = """<div x-data="{
                            open: false,
                            options: [5, 10, 20],
                            perPage: '{{ request('per_page', 10) }}'
                        }"
                        style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; padding: 12px 20px; border-top: 1px solid #E5E7EB; width: 100%; transition: padding-bottom 0.25s cubic-bezier(0.4, 0, 0.2, 1); "
                        :style="open ? { 'padding-bottom': '105px' } : { 'padding-bottom': '12px' }">"""

new_wrapper_start = """<div x-data="{
                            open: false,
                            options: [5, 10, 20],
                            perPage: '{{ request('per_page', 10) }}'
                        }">
                        <div style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; padding: 12px 20px; border-top: 1px solid #E5E7EB; width: 100%;">"""

if old_wrapper_start in text:
    text = text.replace(old_wrapper_start, new_wrapper_start)
else:
    # Let's try replacing with regex to ignore whitespace variations
    import re
    text = re.sub(
        r'<div x-data="\{.*?perPage:.*?\}".*?padding-bottom:.*?12px.*?\}">', 
        new_wrapper_start, 
        text, 
        flags=re.DOTALL
    )

# Now, before the final </div> of this block, I need to insert the spacer.
# Find the end of the x-data block (which is before the @endif that closes the hasPages condition)
end_marker = "                        </div>\n                    </div>\n                @endif"

if end_marker in text:
    new_end_marker = """                        </div>
                        </div>
                        {{-- Spacer to push card boundary down securely without padding bugs --}}
                        <div :style="open ? 'height: 110px;' : 'height: 0px;'" style="transition: height 0.25s cubic-bezier(0.4, 0, 0.2, 1); width: 100%;"></div>
                    </div>
                @endif"""
    text = text.replace(end_marker, new_end_marker)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print('Structural fix applied!')