<div>
    <h3 class="text-lg font-bold text-slate-800 mb-6">Keahlian (Skills)</h3>

    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-xl">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-blue-500 text-[20px]">info</span>
            <p class="text-sm text-blue-700">
                Tambahkan keahlian teknis (hard skills) dan non-teknis (soft skills) yang relevan. Keahlian ini sangat penting untuk membantu sistem Applicant Tracking System (ATS) memfilter profil Anda.
            </p>
        </div>
    </div>

    <div class="border-t border-slate-100 pt-6">
        <h4 class="text-sm font-bold text-slate-800 mb-4">Daftar Keahlian</h4>
        
        <!-- Manual Skill List -->
        <div class="flex flex-wrap gap-3 mb-6">
            <template x-for="(skill, index) in data.cv.keahlian" :key="index">
                <div class="bg-white border border-slate-200 rounded-xl pl-4 pr-2 py-2 flex items-center gap-3">
                    <div>
                        <span class="font-bold text-slate-800 text-sm" x-text="skill.nama"></span>
                        <span class="text-[10px] text-slate-500 font-medium ml-2 px-2 py-0.5 bg-slate-100 rounded-full" x-text="skill.level"></span>
                    </div>
                    <button @click="removeSkill(index)" class="text-red-400 hover:text-red-600 transition-colors p-1 bg-red-50 rounded-lg">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>
                </div>
            </template>
        </div>

        <!-- Add Form -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Nama Keahlian</label>
                    <input type="text" x-model="newSkill.nama" placeholder="Contoh: Laravel, Python, Public Speaking" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4]">
                </div>
                <div class="w-full md:w-40">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Level</label>
                    <select x-model="newSkill.level" class="w-full text-sm border-slate-200 rounded-lg p-2 outline-none focus:border-[#5E53F4] focus:ring-1 focus:ring-[#5E53F4] bg-white">
                        <option>Beginner</option>
                        <option>Intermediate</option>
                        <option>Advanced</option>
                        <option>Expert</option>
                    </select>
                </div>
                <button @click="addSkill()" class="w-full md:w-auto text-xs font-bold bg-[#5E53F4] text-white px-4 py-2.5 rounded-lg hover:bg-[#4e44e0] transition-all shadow-sm h-[38px]">
                    Tambah
                </button>
            </div>
        </div>
    </div>
</div>
