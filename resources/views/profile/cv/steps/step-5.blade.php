<div>
    <h3 class="text-lg font-bold text-slate-800 mb-6">Keahlian (Skills)</h3>

    <div class="p-4 mb-6 rounded-xl border" style="background: var(--c-primary-subtle); border-color: rgba(11,38,110,0.12);">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-[20px] mt-0.5 flex-shrink-0" style="color: var(--c-primary);">info</span>
            <p class="text-sm" style="color: var(--c-fg-sec);">
                Tambahkan keahlian teknis (hard skills) dan non-teknis (soft skills) yang relevan. Keahlian ini sangat penting untuk membantu sistem <strong style="color: var(--c-primary);">Applicant Tracking System (ATS)</strong> memfilter profil Anda.
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
                    <label class="form-label" style="font-size:11px">Nama Keahlian</label>
                    <input type="text" x-model="newSkill.nama" placeholder="Contoh: Laravel, Python, Public Speaking" class="form-control">
                </div>
                <div class="w-full md:w-40">
                    <label class="form-label" style="font-size:11px">Level</label>
                    <select x-model="newSkill.level" class="form-control">
                        <option>Beginner</option>
                        <option>Intermediate</option>
                        <option>Advanced</option>
                        <option>Expert</option>
                    </select>
                </div>
                <button @click="addSkill()" class="btn-primary text-xs w-full md:w-auto" style="height: 38px;">
                    Tambah
                </button>
            </div>
        </div>
    </div>
</div>
