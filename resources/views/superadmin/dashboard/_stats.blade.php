{{-- resources/views/superadmin/dashboard/_stats.blade.php --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-7">
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">Total Users</p>
                <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_users) }}</p>
            </div>
            <div class="size-10 rounded-lg bg-sky-50 text-sky-300 flex items-center justify-center shrink-0 group-hover:bg-sky-300 group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">group</span>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">Superadmins</p>
                <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_superadmins) }}</p>
            </div>
            <div class="size-10 rounded-lg bg-primary-50 text-primary-400 flex items-center justify-center shrink-0 group-hover:bg-primary-500 group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">verified_user</span>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">Admin Modul</p>
                <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_admin_modul) }}</p>
            </div>
            <div class="size-10 rounded-lg bg-indigo-50 text-indigo-400 flex items-center justify-center shrink-0 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">Dosen</p>
                <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_lecturers) }}</p>
            </div>
            <div class="size-10 rounded-lg bg-success-50 text-success-300 flex items-center justify-center shrink-0 group-hover:bg-success-300 group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">school</span>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">GPM</p>
                <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_gpm) }}</p>
            </div>
            <div class="size-10 rounded-lg bg-rose-50 text-rose-400 flex items-center justify-center shrink-0 group-hover:bg-rose-500 group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">verified</span>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="flex items-start justify-between !py-5 !px-5">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-2">Mahasiswa</p>
                <p class="text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_students) }}</p>
            </div>
            <div class="size-10 rounded-lg bg-warning-50 text-warning-300 flex items-center justify-center shrink-0 group-hover:bg-warning-300 group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">person</span>
            </div>
        </x-ui.card-content>
    </x-ui.card>
</div>