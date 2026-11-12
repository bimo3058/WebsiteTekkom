<template x-teleport="body">
    <div x-show="listOpen" x-cloak x-transition.opacity class="mp-schedule-modal-mask" @click.self="closeList()">
        <section class="mp-schedule-modal" role="dialog" aria-modal="true" aria-labelledby="practicum-list-title" aria-describedby="practicum-list-description">
            <div class="mp-schedule-modal-header"><div><h2 id="practicum-list-title">Daftar Praktikum</h2><p id="practicum-list-description" x-text="practicumItems.length+' praktikum dalam rancangan atau pengajuan ini.'"></p></div></div>
            <ul class="mp-schedule-practicum-list">
                <template x-for="item in practicumItems" :key="item.id">
                    <li><div class="mp-settings-name" x-text="item.name"></div><div class="mp-settings-email" x-text="item.period"></div><div class="mp-schedule-counts"><span class="mp-badge neutral sm" x-text="item.shifts+' shift'"></span><span class="mp-badge neutral sm" x-text="item.sessions+' sesi'"></span></div></li>
                </template>
            </ul>
            <div class="mp-settings-footer"><button type="button" class="mp-btn secondary md" x-ref="practicumListClose" @click="closeList()">Tutup</button></div>
        </section>
    </div>
</template>
