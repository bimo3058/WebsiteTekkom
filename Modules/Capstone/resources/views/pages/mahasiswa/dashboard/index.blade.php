@extends('capstone::layouts.app')
@section('title','Dashboard')
@section('content')
<div x-data="capstoneDashboard" @open-schedule-detail.window="detail($event.detail.event)" @refresh-dashboard.window="refreshMahasiswa()">
@include('capstone::partials.loading')
<div x-show="!loading && !error" x-cloak class="flex flex-col space-y-6">
    <div><h1 class="text-2xl font-bold tracking-tight text-gray-900">Halo, {{ explode(' ', $actor['name'])[0] }}!</h1></div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-6 pt-5 pb-4 border-b border-gray-100"><h2 class="text-base font-semibold text-gray-900">Status Group</h2><p class="text-sm text-gray-400 mt-0.5">Periode <span class="font-semibold text-gray-500" x-text="data.group_period?.name || data.active_periods?.[0]?.name || 'N/A'"></span></p></div>
        <div class="flex items-stretch justify-between gap-2 px-6 py-6">
            @foreach(['PDC1'=>'PDC 1','SEMPRO'=>'Seminar Proposal','PDC2'=>'PDC 2','TA_DRAFT'=>'TA Draft','EXPO'=>'Expo','TA'=>'Sidang TA'] as $phase=>$label)
            <div class="flex flex-col items-center gap-2.5 flex-1" :title="(workflow.phases || []).find(p=>p.phase==='{{ $phase }}')?.status || 'locked'">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center border-2 transition-all" :class="(workflow.phases || []).find(p=>p.phase==='{{ $phase }}')?.status==='completed' ? 'bg-emerald-400 border-emerald-400 text-white' : ((workflow.phases || []).find(p=>p.phase==='{{ $phase }}')?.status || 'locked')==='locked' ? 'bg-gray-100 border-transparent text-gray-300' : 'bg-primary border-primary text-white ring-2 ring-primary/20'"><x-capstone::icon name="Check" size="20" x-show="(workflow.phases || []).find(p=>p.phase==='{{ $phase }}')?.status==='completed'" /><x-capstone::icon name="Square" size="18" x-show="(workflow.phases || []).find(p=>p.phase==='{{ $phase }}')?.status!=='completed' && ((workflow.phases || []).find(p=>p.phase==='{{ $phase }}')?.status || 'locked')!=='locked'" /></div>
                <span class="text-xs font-medium text-center leading-tight text-gray-700">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm h-full flex flex-col" x-show="group?.id && group?.status">
            <div class="px-6 pt-5 pb-4 flex flex-row items-center justify-between border-b border-gray-100"><h3 class="text-base font-semibold text-gray-900">Group Project</h3><x-capstone::button variant="ghost" size="icon" class="h-8 w-8 text-gray-400 hover:text-gray-600" @click="refreshMahasiswa()" aria-label="Refresh group"><x-capstone::icon name="RefreshCw" class="h-4 w-4" /></x-capstone::button></div>
            <div class="p-6 flex flex-col gap-4 grow">
                <div><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span x-text="phaseLabel(workflow.current_phase)"></span></span></div>
                <div><h3 class="text-xl font-bold text-gray-900 leading-snug line-clamp-2" x-text="data.title || group?.title?.title || 'Belum ada judul'"></h3><p class="text-sm text-gray-400 mt-1 font-mono" x-text="group?.code || ((group?.period?.name || '')+'K'+(group?.id || ''))"></p></div>
                <div class="flex items-center gap-4"><span class="text-sm text-gray-500 w-16">Progress</span><span class="text-sm font-semibold text-gray-800" x-text="progress+'%'"></span><div class="flex-1 bg-gray-200 h-2 rounded-full overflow-hidden"><div class="bg-primary h-full rounded-full transition-all" :style="{width:progress+'%'}"></div></div></div>
                <div class="flex items-center gap-4"><span class="text-sm text-gray-500 w-16">Member</span><div class="flex -space-x-2"><template x-for="member in (group?.members || []).slice(0,5)" :key="member.id"><span class="h-8 w-8 rounded-full border-2 border-white bg-primary/10 text-primary text-[10px] font-semibold flex items-center justify-center" :title="memberDisplayName(member)" x-text="initials(memberDisplayName(member))"></span></template></div></div>
                <div class="mt-auto flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    <template x-for="name in supervisorNames(group).slice(0,2)" :key="name"><span class="inline-flex items-center gap-2 text-sm text-gray-700"><span class="h-7 w-7 rounded-full bg-gray-200 text-gray-600 text-[10px] font-semibold flex items-center justify-center" x-text="initials(name)"></span><span x-text="name"></span></span></template>
                    <span class="ml-auto rounded-md border border-gray-200 bg-white px-2.5 py-1 text-xs font-medium text-gray-600">Dosen Pembimbing</span>
                </div>
            </div>
        </div>
        <div x-show="!group?.id || !group?.status" class="rounded-xl border border-gray-100 bg-white shadow-sm flex flex-col items-center justify-center p-8 text-center"><p class="font-semibold text-gray-800 mb-4">Anda belum memiliki grup yang disetujui</p><x-capstone::button href="/mahasiswa/group" variant="outline" size="sm">Buat / Cari Kelompok</x-capstone::button></div>
        @include('capstone::partials.mini-calendar')
    </div>

    <div>
        <h2 class="text-base font-bold text-gray-900 border-l-4 border-primary pl-3 mb-4">Upload Document</h2>
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">
            @include('capstone::pages.mahasiswa.dashboard._documents')
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-5 pt-4 pb-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-900">Akses Cepat</h3></div>
                <div class="p-4 grid grid-cols-2 gap-3">
                    <a href="/mahasiswa/titles" class="rounded-xl border border-gray-100 p-4 hover:shadow-sm hover:border-primary/30 transition-all"><span class="text-primary"><x-capstone::icon name="BookOpen" size="20" /></span><span class="block mt-2.5 text-[13px] font-semibold text-gray-900 leading-tight">Jelajahi Judul</span><span class="block mt-1 text-xs text-gray-400">Judul &amp; Bids <span class="text-gray-300">•</span></span></a>
                    <a href="/mahasiswa/group" class="rounded-xl border border-gray-100 p-4 hover:shadow-sm hover:border-amber-200 transition-all"><span class="text-amber-500"><x-capstone::icon name="Users" size="20" /></span><span class="block mt-2.5 text-[13px] font-semibold text-gray-900 leading-tight">Buat/Cari Kelompok</span><span class="block mt-1 text-xs text-gray-400">My Group <span class="text-gray-300">•</span></span></a>
                    <a href="/mahasiswa/grades" class="rounded-xl border border-gray-100 p-4 hover:shadow-sm hover:border-emerald-200 transition-all"><span class="text-emerald-500"><x-capstone::icon name="CircleCheck" size="20" /></span><span class="block mt-2.5 text-[13px] font-semibold text-gray-900 leading-tight">Nilai Saya</span><span class="block mt-1 text-xs text-gray-400">Lihat Nilai saya <span class="text-gray-300">•</span></span></a>
                    <a href="/mahasiswa/schedule" class="rounded-xl border border-gray-100 p-4 hover:shadow-sm hover:border-primary/30 transition-all"><span class="text-primary"><x-capstone::icon name="Calendar" size="20" /></span><span class="block mt-2.5 text-[13px] font-semibold text-gray-900 leading-tight">Jadwal</span><span class="block mt-1 text-xs text-gray-400">Lihat Jadwal <span class="text-gray-300">•</span></span></a>
                </div>
            </div>
        </div>
    </div>
</div>
@include('capstone::partials.schedule-detail')
</div>
@endsection
