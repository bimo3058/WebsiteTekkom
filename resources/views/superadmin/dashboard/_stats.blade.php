{{-- resources/views/superadmin/dashboard/_stats.blade.php --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mb-5 sm:mb-7">
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="!py-3 sm:!py-5 !px-3 sm:!px-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-1 sm:mb-2">Total Users</p>
                    <p class="text-xl sm:text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_users) }}</p>
                </div>
                <div class="size-8 sm:size-10 rounded-lg bg-sky-50 text-sky-300 flex items-center justify-center shrink-0 group-hover:bg-sky-300 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[16px] sm:text-[20px]">group</span>
                </div>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="!py-3 sm:!py-5 !px-3 sm:!px-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-1 sm:mb-2">Superadmins</p>
                    <p class="text-xl sm:text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_superadmins) }}</p>
                </div>
                <div class="size-8 sm:size-10 rounded-lg bg-primary-50 text-primary-400 flex items-center justify-center shrink-0 group-hover:bg-primary-500 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[16px] sm:text-[20px]">verified_user</span>
                </div>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="!py-3 sm:!py-5 !px-3 sm:!px-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-1 sm:mb-2">Admin Modul</p>
                    <p class="text-xl sm:text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_admin_modul) }}</p>
                </div>
                <div class="size-8 sm:size-10 rounded-lg bg-indigo-50 text-indigo-400 flex items-center justify-center shrink-0 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[16px] sm:text-[20px]">manage_accounts</span>
                </div>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="!py-3 sm:!py-5 !px-3 sm:!px-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-1 sm:mb-2">Dosen</p>
                    <p class="text-xl sm:text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_lecturers) }}</p>
                </div>
                <div class="size-8 sm:size-10 rounded-lg bg-success-50 text-success-300 flex items-center justify-center shrink-0 group-hover:bg-success-300 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[16px] sm:text-[20px]">school</span>
                </div>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="!py-3 sm:!py-5 !px-3 sm:!px-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-1 sm:mb-2">GPM</p>
                    <p class="text-xl sm:text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_gpm) }}</p>
                </div>
                <div class="size-8 sm:size-10 rounded-lg bg-rose-50 text-rose-400 flex items-center justify-center shrink-0 group-hover:bg-rose-500 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[16px] sm:text-[20px]">verified</span>
                </div>
            </div>
        </x-ui.card-content>
    </x-ui.card>
    <x-ui.card class="group hover:border-primary-100 hover:shadow-md hover:-translate-y-px transition-all">
        <x-ui.card-content class="!py-3 sm:!py-5 !px-3 sm:!px-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] sm:text-[11px] font-bold uppercase tracking-wider text-muted-foreground mb-1 sm:mb-2">Mahasiswa</p>
                    <p class="text-xl sm:text-[28px] font-bold text-foreground leading-none tracking-tight">{{ number_format($total_students) }}</p>
                </div>
                <div class="size-8 sm:size-10 rounded-lg bg-warning-50 text-warning-300 flex items-center justify-center shrink-0 group-hover:bg-warning-300 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[16px] sm:text-[20px]">person</span>
                </div>
            </div>
        </x-ui.card-content>
    </x-ui.card>
</div>