@extends('capstone::layouts.app')
@section('title','Expo Events')
@section('content')
<div x-data="capstoneExpo" class="space-y-6">
    <div><h1 class="text-3xl font-bold tracking-tight">Expo Events</h1><p class="text-muted-foreground">Register your group for an available expo event.</p></div>
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak>
        <div x-show="!events.length" class="text-center py-12 border rounded-lg border-dashed"><x-capstone::icon name="CalendarDays" class="h-12 w-12 mx-auto mb-4 opacity-50 text-muted-foreground" /><h2 class="text-xl font-bold mb-2">No Expo Events Available</h2><p class="text-muted-foreground">No expo events are currently open for registration.</p></div>
        <div class="grid gap-4 md:grid-cols-2"><template x-for="event in events" :key="event.id"><x-capstone::card ::class="event.is_registered ? 'border-primary' : full(event) ? 'opacity-60' : ''">
            <div class="px-6 pb-3 flex items-center justify-between gap-2"><h2 class="text-lg font-semibold" x-text="event.name"></h2><x-capstone::badge x-show="event.is_registered" class="bg-green-600"><x-capstone::icon name="CheckCircle2" class="h-3 w-3 mr-1" />Registered</x-capstone::badge><x-capstone::badge x-show="!event.is_registered && full(event)" variant="destructive"><x-capstone::icon name="AlertCircle" class="h-3 w-3 mr-1" />Full</x-capstone::badge><x-capstone::badge variant="outline" x-show="!event.is_registered && !full(event)" x-text="(event.capacity-event.registrations_count)+' slots left'" /></div>
            <div class="px-6 space-y-3">
                <div class="flex items-center gap-2 text-sm"><x-capstone::icon name="CalendarDays" class="h-4 w-4 text-muted-foreground" /><span x-text="date(event.date)"></span></div><div class="flex items-center gap-2 text-sm"><x-capstone::icon name="Clock" class="h-4 w-4 text-muted-foreground" /><span x-text="event.start_time.slice(0,5)+' – '+event.end_time.slice(0,5)"></span></div><div class="flex items-center gap-2 text-sm"><x-capstone::icon name="MapPin" class="h-4 w-4 text-muted-foreground" /><span x-text="event.room"></span></div><div class="flex items-center gap-2 text-sm"><x-capstone::icon name="Users" class="h-4 w-4 text-muted-foreground" /><span x-text="event.registrations_count+'/'+event.capacity+' registered'"></span></div>
                <div class="w-full bg-muted rounded-full h-2"><div class="h-2 rounded-full transition-all" :class="full(event)?'bg-destructive':'bg-primary'" :style="{width:Math.min(100,event.capacity ? event.registrations_count/event.capacity*100 : 0)+'%'}"></div></div>
                <x-capstone::button class="w-full mt-2" x-show="!event.is_registered && !full(event)" ::disabled="saving || !event.can_register" ::title="event.registration_reason" @click="confirm(event,'register')">Daftar Expo</x-capstone::button>
                <div x-show="event.is_registered" class="space-y-2"><p class="text-sm text-green-600 font-medium text-center">✓ Your group is registered for this event</p><a :href="url('/mahasiswa/expo/'+event.id)" class="w-full h-9 rounded-md bg-primary text-primary-foreground inline-flex justify-center items-center text-sm font-medium"><x-capstone::icon name="Eye" class="mr-2 h-4 w-4" />Lihat Detail</a><x-capstone::button variant="outline" class="w-full" ::disabled="saving" @click="confirm(event,'withdraw')">Undurkan dari Expo</x-capstone::button></div>
            </div>
        </x-capstone::card></template></div>
    </div>
    <x-capstone::dialog id="expo-confirm"><h2 class="text-lg font-semibold" x-text="action==='register' ? 'Register your group for this expo event?' : 'Withdraw your group from this expo event?'"></h2><p class="text-sm text-muted-foreground mt-2" x-text="selected?.name"></p><div class="flex justify-end gap-2 mt-6"><x-capstone::button variant="outline" @click="$el.closest('dialog').close()">Cancel</x-capstone::button><x-capstone::button @click="submit" ::disabled="saving"><span x-text="saving ? 'Processing...' : 'Confirm'"></span></x-capstone::button></div></x-capstone::dialog>
</div>
@endsection
