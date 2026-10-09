@extends('capstone::layouts.app')
@section('title','My Schedule')
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
        <h2 class="text-lg font-semibold" x-text="editing ? 'Edit BIMBINGAN Schedule' : 'New BIMBINGAN Schedule'"></h2>
        <p class="text-sm text-muted-foreground mt-2" x-text="editing ? 'Update the schedule details.' : 'Set up a new bimbingan session for your group.'"></p>
        <form @submit.prevent="save">
            <div class="grid gap-4 py-4">
                <x-capstone::field name="group_id" label="Group" type="select" :options="[''=>'Select a group']" required><template x-for="group in groups" :key="group.id"><option :value="group.id" x-text="group.title?.title || group.code || 'Group '+group.id"></option></template></x-capstone::field>
                <x-capstone::field name="date" label="Date" type="date" required />
                <div class="grid grid-cols-2 gap-4"><x-capstone::field name="start_time" label="Start Time" type="time" required /><x-capstone::field name="end_time" label="End Time" type="time" required /></div>
                <x-capstone::field name="mode" label="Mode" type="select" :options="['offline'=>'Offline','online'=>'Online']" />
                <template x-if="form.mode!=='online'"><div><x-capstone::field name="room" label="Location" type="select" :options="[''=>'Select location']" required><template x-for="location in locations.filter(l=>l.type==='offline')" :key="location.id"><option :value="location.name" x-text="location.name"></option></template></x-capstone::field></div></template>
                <template x-if="form.mode==='online'"><div><x-capstone::field name="room" label="Meeting Link / Platform" placeholder="e.g., Zoom link or Google Meet URL" required /></div></template>
                <x-capstone::field name="notes" label="Notes (Optional)" type="textarea" maxlength="1000" />
            </div>
            <div class="flex justify-end gap-2"><x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Cancel</x-capstone::button><x-capstone::button type="submit" ::disabled="saving"><span x-text="saving ? 'Saving...' : editing ? 'Save Changes' : 'Create Schedule'"></span></x-capstone::button></div>
        </form>
    </x-capstone::dialog>
</div>
@endsection
