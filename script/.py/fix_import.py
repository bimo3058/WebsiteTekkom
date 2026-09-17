
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/admin/praktikum-detail.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

replacement = """<div class="mb-5" x-data="{ 
                isDragging: false, 
                fileName: '',
                handleDrop(e) {
                    this.isDragging = false;
                    if(e.dataTransfer.files.length > 0) {
                        this.$refs.fileInput.files = e.dataTransfer.files;
                        this.fileName = e.dataTransfer.files[0].name;
                    }
                },
                handleChange(e) {
                    if(e.target.files.length > 0) {
                        this.fileName = e.target.files[0].name;
                    } else {
                        this.fileName = '';
                    }
                }
            }">
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">File Excel (.xlsx) / CSV <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed rounded-[12px] p-6 text-center transition-colors cursor-pointer"
                     :class="isDragging ? 'border-[#0B266E] bg-[#EEF1FA]' : 'border-[#DFE1E7] hover:bg-[#F6F8FA]'"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="handleDrop($event)"
                     onclick="document.getElementById('fileExcel').click()">
                     
                    <input type="file" name="file" id="fileExcel" x-ref="fileInput" @change="handleChange($event)" accept=".csv,.xlsx,.xls" required class="hidden">
                    
                    <svg x-show="!fileName" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" class="mx-auto mb-2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5 5-5-5M12 13V3"/></svg>
                    <svg x-show="fileName" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5" stroke-linecap="round" class="mx-auto mb-2" style="display:none;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><polyline points="9 15 12 18 16 13"></polyline></svg>

                    <div x-show="!fileName" class="text-[13px] font-semibold text-[#0B266E]">Pilih file atau drag kemari</div>
                    <div x-show="!fileName" class="text-[11px] text-[#A4ABB8] mt-1">Maksimal 2MB</div>
                    
                    <div x-show="fileName" class="text-[13px] font-semibold text-[#0D0D12]" style="display:none;" x-text="fileName"></div>
                    <div x-show="fileName" class="text-[11px] text-[#059669] mt-1" style="display:none;">File siap diunggah</div>
                </div>"""

# Replace the input block
text = re.sub(
    r'<div class="mb-5">\s*<label class="block text-\[12px\] font-semibold text-\[\#353849\] mb-1">File Excel \(\.xlsx\) / CSV <span class="text-red-500">\*</span></label>\s*<div class="border-2 border-dashed border-\[\#DFE1E7\].*?</div>\s*<div class="mt-3 flex items-start gap-2 bg-\[\#F6F8FA\] p-3 rounded-\[8px\]">',
    replacement + '\n                <div class="mt-3 flex items-start gap-2 bg-[#F6F8FA] p-3 rounded-[8px]">\',
    text, flags=re.DOTALL
)

btn_replacement = """<div class="flex gap-3 justify-end pt-2 border-t border-[#DFE1E7] mt-2">
                <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')" class="mp-btn secondary md px-5">Batal</button>
                <button type="submit" class="mp-btn primary md px-5">Import</button>
            </div>"""

text = re.sub(
    r'<div class="flex gap-3 justify-end pt-2 border-t border-\[\#DFE1E7\] mt-2">\s*<button type="button" onclick="document\.getElementById\('modalImport'\)\.classList\.add\('hidden'\)"\s*Import\s*</button>\s*</div>',
    btn_replacement,
    text, flags=re.DOTALL
)

with open("Modules/EOffice/resources/views/manajemen-praktikum/admin/praktikum-detail.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("Modification done!")

