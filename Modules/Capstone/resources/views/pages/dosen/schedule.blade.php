@extends('capstone::layouts.app')
@section('title','Jadwal Saya')
@section('content')
<div x-data="capstoneSchedules" class="space-y-5">
    @include('capstone::partials.schedule-header')
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @include('capstone::partials.schedule-toolbar')
        @include('capstone::partials.schedule-calendar')
        @include('capstone::partials.schedule-table')
    </div>
    <x-capstone::dialog id="schedule-form" class="sm:max-w-[480px]">
        <h2 class="text-lg font-semibold" x-text="editing ? 'Ubah Jadwal BIMBINGAN' : 'Jadwal BIMBINGAN Baru'"></h2>
        <p class="text-sm text-muted-foreground mt-2" x-text="editing ? 'Perbarui detail jadwal.' : 'Buat sesi bimbingan baru untuk kelompok Anda.'"></p>
        <form @submit.prevent="save">
            <div class="grid gap-4 py-4">
                <x-capstone::field name="group_id" label="Kelompok" type="select" :options="[''=>'Pilih kelompok']" required><template x-for="group in groups" :key="group.id"><option :value="group.id" x-text="group.title?.title || group.code || 'Kelompok '+group.id"></option></template></x-capstone::field>
                <x-capstone::field name="date" label="Tanggal" type="date" placeholder="dd/mm/yyyy" required />
                <div class="grid grid-cols-2 gap-4"><x-capstone::field name="start_time" label="Waktu Mulai" type="time" placeholder="cth., 09:00" required /><x-capstone::field name="end_time" label="Waktu Selesai" type="time" placeholder="cth., 12:00" required /></div>
                <x-capstone::field name="mode" label="Mode" type="select" :options="['offline'=>'Luring','online'=>'Daring']" />
                <template x-if="form.mode!=='online'"><div><x-capstone::field name="room" label="Lokasi" type="select" :options="[''=>'Pilih lokasi']" required><template x-for="location in locations.filter(l=>l.type==='offline')" :key="location.id"><option :value="location.name" x-text="location.name"></option></template></x-capstone::field></div></template>
                <template x-if="form.mode==='online'"><div><x-capstone::field name="room" label="Tautan Rapat / Platform" placeholder="cth., tautan Zoom atau URL Google Meet" required /></div></template>
                <x-capstone::field name="notes" label="Catatan (Opsional)" type="textarea" placeholder="cth., Bawa laptop masing-masing..." maxlength="1000" />
            </div>
            <div class="flex justify-end gap-2"><x-btn style="outline" @click="$el.closest('dialog').close()">Batal</x-btn><x-btn type="submit" x-bind:disabled="saving"><span x-text="saving ? 'Menyimpan...' : editing ? 'Simpan Perubahan' : 'Buat Jadwal'"></span></x-btn></div>
        </form>
    </x-capstone::dialog>
</div>
@endsection
