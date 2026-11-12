<x-eoffice::manajemen-praktikum.ui.assessment-styles />
                            <template x-teleport="body">
                                <div data-final-bulk-dialog x-show="bulkOpen" x-cloak
                                    @keydown.escape.window="closeBulk()"
                                    @click.self="closeBulk()"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    style="position:fixed; inset:0; z-index:1000; display:block; background:rgba(15,23,42,0.55);">
                                    <div
                                        role="dialog" aria-modal="true" aria-label="Isi Nilai Terpilih" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:calc(100% - 40px); max-width:480px; max-height:calc(100dvh - 32px); display:flex; flex-direction:column; background:#fff; border:1px solid #DFE1E7; border-radius:14px; box-shadow:0 20px 50px rgba(15,23,42,0.22); overflow:hidden;">
                                        <div style="padding:20px 22px; border-bottom:1px solid #DFE1E7; display:flex; justify-content:space-between; align-items:flex-start; gap:16px;flex-shrink:0">
                                            <div>
                                                <div style="font-size:18px; font-weight:700; color:#0D0D12;">Isi Nilai Terpilih</div>
                                                <div style="font-size:13px; color:#666D80; margin-top:4px;">
                                                    Atur kolom penilaian, nomor mahasiswa, dan nilai yang akan diterapkan.
                                                </div>
                                            </div>
                                        </div>
                                        <form @submit.prevent="applyBulk()" style="display:flex;flex-direction:column;min-height:0;overflow:hidden">
                                            <div style="padding:22px; display:flex; flex-direction:column; gap:18px;overflow-y:auto;min-height:0">
                                                <div>
                                                    <label style="display:block; font-size:13px; font-weight:600; color:#353849; margin-bottom:8px;">Kolom Penilaian <span style="color:#E11D48;">*</span></label>
                                                    <div data-final-bulk-fields style="display:flex; flex-direction:column; gap:8px;max-height:104px;overflow-y:auto;padding:1px 2px;flex-shrink:0" @scroll.capture="if($event.target.hasAttribute('data-final-bulk-fields'))$dispatch('close-assessment-menu')">
                                                        <template x-for="(field, fieldIndex) in bulkFields" :key="fieldIndex">
                                                            <div x-data="{ assessmentOpen: false }" @close-assessment-menu.window="assessmentOpen=false" style="position:relative; display:flex; align-items:flex-start; gap:10px;">
                                                                <div class="mp-assessment-field">
                                                                    <div role="button" tabindex="0" class="mp-assessment-trigger"
                                                                        :class="{ 'is-open': assessmentOpen }"
                                                                        @click="assessmentOpen = !assessmentOpen"
                                                                        @keydown.enter.prevent="assessmentOpen = !assessmentOpen"
                                                                        @keydown.space.prevent="assessmentOpen = !assessmentOpen"
                                                                        >
                                                                        <span x-text="assessmentLabel(field)"></span>
                                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                            >
                                                                            <polyline points="6 9 12 15 18 9"></polyline>
                                                                        </svg>
                                                                    </div>
                                                                    <div x-show="assessmentOpen" x-cloak class="mp-assessment-menu"
                                                                        :style="assessmentOpen ? {position:'fixed',top:$el.previousElementSibling.getBoundingClientRect().bottom-$el.closest('[role=dialog]').getBoundingClientRect().top+4+'px',left:$el.previousElementSibling.getBoundingClientRect().left-$el.closest('[role=dialog]').getBoundingClientRect().left+'px',width:$el.previousElementSibling.getBoundingClientRect().width+'px',maxHeight:'min(200px, 30dvh)',overflowY:'auto',zIndex:1001} : {}"
                                                                        @click.outside="assessmentOpen = false"
                                                                        x-transition:enter="transition ease-out duration-150"
                                                                        x-transition:enter-start="opacity-0 -translate-y-1"
                                                                        x-transition:enter-end="opacity-100 translate-y-0">
                                                                        <template x-for="option in availableAssessmentOptions(fieldIndex)" :key="option.value">
                                                                            <div role="button" tabindex="0" class="mp-assessment-option"
                                                                                :class="{ 'is-selected': field === option.value }"
                                                                                @click="bulkFields[fieldIndex] = option.value; assessmentOpen = false"
                                                                                @keydown.enter.prevent="bulkFields[fieldIndex] = option.value; assessmentOpen = false"
                                                                                >
                                                                                <span x-text="option.label"></span>
                                                                                <template x-if="field === option.value">
                                                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                                                    </svg>
                                                                                </template>
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <button type="button" @click="removeAssessmentField(fieldIndex)" aria-label="Hapus kolom penilaian"
                                                                    style="appearance:none; width:46px; height:46px; flex:0 0 46px; display:flex; align-items:center; justify-content:center; border:1px solid #FECDD3; border-radius:9px; background:#FFF1F2; color:#E11D48; cursor:pointer;" class="mp-delete-button" title="Hapus"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    <template x-if="bulkFields.length < assessmentOptions().length">
                                                        <div role="button" class="mp-assessment-add" tabindex="0"
                                                            @click="addAssessmentField()"
                                                            @keydown.enter.prevent="addAssessmentField()"
                                                            @keydown.space.prevent="addAssessmentField()">
                                                            <span>+</span>
                                                            <span>Tambah Kolom Penilaian</span>
                                                        </div>
                                                    </template>
                                                    <div style="margin-top:6px; text-align:right; font-size:12px; color:#666D80;">Maksimal 3 kolom penilaian</div>
                                                    <div x-show="bulkFields.length === 0" style="margin-top:7px; font-size:12px; color:#BE123C;">Tambahkan minimal satu kolom penilaian.</div>
                                                    </div>

                                                <div>
                                                    <label style="display:block; font-size:13px; font-weight:600; color:#353849; margin-bottom:8px;">No Dipilih <span style="color:#E11D48;">*</span></label>
                                                    <input type="text" x-model="bulkNumbers" @input="bulkError = ''"
                                                        required placeholder="Contoh: 1-10 atau 1-5, 8, 10-12"
                                                        class="mp-input" style="width:100%; height:44px; font-size:13px;">
                                                    <div style="font-size:12px; color:#666D80; margin-top:7px;">Gunakan tanda hubung untuk rentang dan koma untuk nomor terpisah.</div>
                                                </div>

                                                <div>
                                                <label style="display:block; font-size:13px; font-weight:600; color:#353849; margin-bottom:8px;">Nilai <span style="color:#E11D48;">*</span></label>
                                                <x-eoffice::manajemen-praktikum.ui.grade-score-input model="bulkValue" label="Nilai yang diterapkan" :required="true" :wide="true" x-ref="bulkValueInput" />
                                                <div style="font-size:12px; color:#666D80; margin-top:7px;">Gunakan angka antara 0 sampai 100.</div>
                                                </div>

                                                <div x-show="bulkError" x-text="bulkError"
                                                    style="padding:10px 12px; border:1px solid #FECDD3; border-radius:8px; background:#FFF1F2; color:#BE123C; font-size:12px;"></div>
                                            </div>
                                            <div class="mp-modal-footer" style="border-radius:0 0 14px 14px;flex-shrink:0">
                                                <button type="button" class="mp-btn secondary" @click="closeBulk()"
                                                    style="height:38px; padding:0 16px; border:1px solid #DFE1E7; border-radius:8px; background:#fff; color:#353849; font-size:13px; font-weight:600;">Batal</button>
                                                <button type="submit" class="mp-btn"
                                                    style="height:38px; padding:0 16px; border:1px solid #0B266E; border-radius:8px; background:#0B266E; color:#fff; font-size:13px; font-weight:600;">Terapkan Nilai</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>
