@extends('capstone::layouts.app')
@section('title','Notifications')
@section('content')
<div x-data="capstoneNotifications" class="space-y-6">
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak class="space-y-6">
        <div class="flex items-start justify-between gap-3"><div><h1 class="text-3xl font-bold tracking-tight">Notifications</h1><p class="text-muted-foreground" x-text="total+' notifications · '+unreadCount+' unread'"></p></div><x-capstone::button variant="outline" size="sm" x-show="unreadCount>0" @click="readAll" ::disabled="busy"><x-capstone::icon name="CheckCheck" class="mr-2 h-4 w-4" />Mark All Read</x-capstone::button></div>
        <div x-show="!items.length" class="rounded-lg border border-dashed py-12 text-center"><x-capstone::icon name="Bell" class="text-muted-foreground mx-auto mb-4 h-12 w-12 opacity-50" /><h2 class="mb-2 text-xl font-bold">No Notifications</h2><p class="text-muted-foreground">You're all caught up!</p></div>
        <div class="space-y-2"><template x-for="n in items" :key="n.id"><article tabindex="0" :aria-label="n.title" @click="open(n)" @keydown.enter.self.prevent="open(n)" class="rounded-xl border bg-card text-card-foreground cursor-pointer transition-all duration-200" :class="!n.is_read?'border-primary/20 bg-primary/5 shadow-sm':'hover:border-primary/20 hover:bg-muted/30 shadow-none'">
            <div class="flex items-start gap-4 p-5"><div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full" :class="palette(n.type)"><x-capstone::icon name="MailOpen" class="h-4 w-4" x-show="n.is_read" /><x-capstone::icon name="Mail" class="h-4 w-4" x-show="!n.is_read" /></div><div class="min-w-0 flex-1">
                <div class="flex items-center gap-2"><span class="text-sm font-medium" :class="!n.is_read?'text-primary':''" x-text="n.title"></span><x-capstone::badge x-show="!n.is_read" class="px-1.5 py-0 text-xs">New</x-capstone::badge></div><p class="text-muted-foreground mt-0.5 line-clamp-2 text-sm" x-text="n.message"></p>
                <div x-show="n.type==='GROUP_INVITATION' && n.related_id" class="mt-4 flex gap-2"><template x-if="n.invitation_status==='PENDING' && !invitationActions[n.id]"><div class="flex gap-2"><x-capstone::button size="sm" @click.stop="invitation(n,'accept')" ::disabled="busy">Accept</x-capstone::button><x-capstone::button size="sm" variant="outline" @click.stop="invitation(n,'reject')" ::disabled="busy">Reject</x-capstone::button></div></template><span x-show="n.invitation_status==='ACCEPTED'" class="text-muted-foreground flex items-center gap-2 text-sm font-medium"><x-capstone::icon name="CheckCheck" class="h-4 w-4 text-green-500" />You accepted this invitation</span><span x-show="n.invitation_status==='REJECTED'" class="text-muted-foreground flex items-center gap-2 text-sm font-medium"><x-capstone::icon name="CircleX" class="h-4 w-4 text-red-500" />You rejected this invitation</span></div>
                <time class="block text-muted-foreground mt-2 text-xs whitespace-nowrap" :datetime="n.created_at" x-text="relative(n.created_at)"></time>
            </div></div>
        </article></template></div>
        <div x-show="lastPage>1" class="flex justify-center gap-2"><x-capstone::button variant="outline" size="sm" ::disabled="page<=1" @click="load(page-1)">Previous</x-capstone::button><span class="text-muted-foreground flex items-center px-3 text-sm" x-text="'Page '+page+' of '+lastPage"></span><x-capstone::button variant="outline" size="sm" ::disabled="page>=lastPage" @click="load(page+1)">Next</x-capstone::button></div>
    </div>
</div>
@endsection
