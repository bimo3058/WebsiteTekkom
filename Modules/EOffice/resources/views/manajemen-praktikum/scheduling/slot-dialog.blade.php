<template x-teleport="body">
    <div x-show="slotOpen" x-cloak x-transition.opacity class="mp-schedule-modal-mask" @click.self="slotOpen=false;$dispatch('calendar-selection-cancelled')" @keydown.escape.window="slotOpen=false;$dispatch('calendar-selection-cancelled')">
        <section class="mp-schedule-modal" role="dialog" aria-modal="true" aria-labelledby="slot-dialog-title">
            <div class="mp-schedule-modal-header"><div><h2 id="slot-dialog-title">Rancangan Booking Praktikum</h2><p x-text="calendarSlot ? calendarSlot.roomName+' · '+calendarSlot.date.split('-').reverse().join('/')+' · '+slotStart+'–'+slotEnd+' WIB' : ''"></p></div></div>
            <form @submit.prevent="applyCalendarSlot()">
                <div class="mp-settings-body">
                    <p class="mp-settings-description" x-show="editingMeetingId" x-text="editingMeeting?.module_name"></p>
                    <fieldset :disabled="!!editingMeetingId" style="border:0;padding:0;margin:0;display:grid;gap:16px">
                    <div><label class="mp-settings-label">Nama Praktikum <span class="mp-schedule-field-error">*</span></label><x-eoffice::manajemen-praktikum.ui.ta-select x-model="slotPracticum" :options="[]" x-effect="options=Object.fromEntries(visiblePlans.map(p=>[p.id,p.name]))" placeholder="Pilih praktikum aktif..." :searchable="true" search-placeholder="Cari nama praktikum..." /></div>
                    <div><label class="mp-settings-label" for="slot-shift">Shift <span class="mp-schedule-field-error">*</span></label><input id="slot-shift" class="mp-input" type="number" inputmode="numeric" min="1" step="1" x-model="slotShift" placeholder="Contoh: 1" required><p class="mp-settings-description">Gunakan angka shift sesuai pembagian praktikan.</p></div>
                    </fieldset>
                    <div class="mp-schedule-controls">
                        <div><label class="mp-settings-label">Jam Mulai <span class="mp-schedule-field-error">*</span></label><x-eoffice::manajemen-praktikum.ui.time-select x-model="slotStart" :min="$calendar['settings']['open']" :max="$calendar['settings']['close']" label="Jam mulai" /></div>
                        <div><label class="mp-settings-label">Jam Selesai <span class="mp-schedule-field-error">*</span></label><x-eoffice::manajemen-praktikum.ui.time-select x-model="slotEnd" :min="$calendar['settings']['open']" :max="$calendar['settings']['close']" label="Jam selesai" /></div>
                    </div>
                    <p class="mp-settings-description mp-slot-help" x-text="editingMeetingId ? 'Perubahan hanya berlaku untuk pertemuan ini. Pertemuan dan shift lainnya tetap.' : 'Rentang jam terpilih digunakan setiap minggu selama modul berjalan. Periksa kembali jadwal shift lain sebelum menyimpan rancangan.'"></p>
                    <p class="mp-schedule-field-error" x-show="slotError" x-text="slotError" role="alert"></p>
                </div>
                <div class="mp-settings-footer"><button type="button" class="mp-delete-button mp-btn destructive md" x-show="editingMeetingId" :disabled="submitting" style="margin-right:auto" @click="askDeleteDraftMeeting()" aria-label="Hapus" title="Hapus"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button><button type="button" class="mp-btn secondary md" @click="slotOpen=false;$dispatch('calendar-selection-cancelled')">Batal</button><button type="submit" class="mp-btn primary md" :disabled="submitting||!slotPracticum||!slotShift||(editingMeetingId&&meetingFormUnchanged)" x-text="editingMeetingId ? 'Simpan Perubahan' : 'Simpan'"></button></div>
            </form>
        </section>
    </div>
</template>
<template x-teleport="body">
    <div x-show="deleteConfirmOpen" x-cloak x-transition.opacity class="mp-schedule-modal-mask" @click.self="if(!submitting){deleteConfirmOpen=false;slotOpen=true}" @keydown.escape.window="if(deleteConfirmOpen&&!submitting){deleteConfirmOpen=false;slotOpen=true}">
        <section class="mp-schedule-modal" role="dialog" aria-modal="true" aria-labelledby="delete-draft-title">
            <div class="mp-schedule-modal-header"><h2 id="delete-draft-title">Hapus Bagian Rancangan</h2></div>
            <div class="mp-settings-body"><p class="mp-settings-description" style="margin:0">Yakin ingin menghapus pertemuan ini dari rancangan? Pertemuan dan shift lainnya tidak akan dihapus.</p><div><strong x-text="editingMeeting?.practicum_name"></strong><p class="mp-settings-description" x-text="editingMeeting ? editingMeeting.module_name+' · Shift '+editingMeeting.shift+' · '+editingMeeting.date.split('-').reverse().join('/') : ''"></p></div></div>
            <div class="mp-settings-footer"><button type="button" class="mp-btn secondary md" :disabled="submitting" @click="deleteConfirmOpen=false;slotOpen=true">Batal</button><button type="button" class="mp-btn destructive md" :disabled="submitting" @click="deleteDraftMeeting()">Ya, Hapus</button></div>
        </section>
    </div>
</template>
