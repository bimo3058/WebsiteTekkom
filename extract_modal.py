import re

with open("temp.txt", "r", encoding="utf-16") as f:
    text = f.read()

# Try to find modal main content
match = re.search(r'\{\{-- MODAL KELOLA KELOMPOK & SHIFT --\}\}(.*?)\{\{-- MODAL SUB: EDIT ANGGOTA KELOMPOK                      --\}\}', text, flags=re.DOTALL)
if match:
    main_modal_full = match.group(0)
    print("Found main modal full, length:", len(main_modal_full))
    # We want to extract just the inside part 
    # Usually inside <div x-show="showMainModal" ... >
    # actually, the user wants the form to be direct.
    
    # We will write the text out to a file so we can inspect it safely
    with open("modal_full.blade.php", "w", encoding="utf-8") as out:
        out.write(main_modal_full)
else:
    print("Not found modal using the separator.")
