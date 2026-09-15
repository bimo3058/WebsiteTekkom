@extends('capstone::layouts.app')
@section('title','My Schedule')
@section('content')
<div x-data="capstoneSchedules" class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div><h1 class="text-3xl font-bold tracking-tight">My Schedule</h1><p class="text-muted-foreground">View all your schedules: bimbingan sessions, examinations, and events.</p></div>
        <div class="flex flex-wrap items-center gap-2"><x-capstone::button @click="edit()"><x-capstone::icon name="Plus" class="mr-2 h-4 w-4" />New BIMBINGAN</x-capstone::button>
            <select class="w-[200px] max-w-full h-9 rounded-md border px-3 text-sm bg-background" x-model="selectedPeriod" aria-label="Academic Period"><option value="all">All Periods</option><template x-for="period in periods" :key="period.id"><option :value="period.id" x-text="period.name+(period.is_active ? ' (Active)' : '')"></option></template></select>
        </div>
    </div>
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">@foreach(['BIMBINGAN'=>'Bimbingan','SEMPRO'=>'Sempro','EXPO'=>'Expo','TA_DEFENSE'=>'TA Defense'] as $type=>$label)<div class="p-4 rounded-lg border" :class="color('{{ $type }}')"><p class="text-2xl font-bold" x-text="count('{{ $type }}')"></p><p class="text-sm text-muted-foreground">{{ $label }}</p></div>@endforeach</div>
        @include('capstone::partials.schedule-calendar')
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
