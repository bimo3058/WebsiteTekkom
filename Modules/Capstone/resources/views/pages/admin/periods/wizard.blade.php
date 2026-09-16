<div class="min-h-screen bg-gray-50/50" x-data="periodWizard(@js($periodId))">
    <div class="sticky top-0 z-10"><div class="mx-auto max-w-7xl px-6 py-4"><h1 class="text-2xl font-semibold text-gray-900">{{ $periodId ? 'Edit Periode' : 'Tambah Periode Baru' }}</h1><p class="mt-1 text-sm text-gray-500">{{ $periodId ? 'Perbarui konfigurasi periode yang sudah ada.' : 'Buat periode baru dengan konfigurasi langkah demi langkah.' }}</p></div></div>
    @include('capstone::partials.loading')
    <div x-show="!loading && !error" x-cloak>
        <div class="mx-auto max-w-7xl px-6 py-4"><div class="rounded-xl border bg-white px-6 py-6 shadow-sm"><div class="flex items-start justify-between overflow-x-auto">
            @foreach([['Informasi Dasar','Informasi dasar periode'],['Setup Evaluasi','Konfigurasi penilaian'],['Tanggal Fase','Jadwal fase-fase'],['Konfigurasi Group','Pengaturan group'],['Review','Konfirmasi data']] as $index=>[$title,$description])
                <div class="flex flex-1 items-start"><div class="flex flex-1 flex-col items-center"><button type="button" @click="go({{ $index }})" :disabled="{{ $index }}>step+1 || saving || copying" :aria-current="step==={{ $index }} ? 'step' : null" class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-semibold transition-all" :class="step>{{ $index }} ? 'bg-green-600 text-white' : (step==={{ $index }} ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-gray-100 text-gray-400 border-2 border-gray-200')"><x-capstone::icon name="Check" x-show="step>{{ $index }}" class="h-5 w-5" /><span x-show="step<={{ $index }}">{{ $index+1 }}</span></button><div class="mt-3 min-w-20 text-center"><p class="text-sm font-semibold" :class="step>{{ $index }} ? 'text-green-700' : (step==={{ $index }} ? 'text-blue-700' : 'text-gray-500')">{{ $title }}</p><p class="mt-0.5 max-w-[120px] text-xs text-gray-400">{{ $description }}</p></div></div>@if(!$loop->last)<div class="flex-1 px-2 pt-5"><div class="h-0.5" :class="step>{{ $index }} ? 'bg-green-600' : 'bg-gray-200'"></div></div>@endif</div>
            @endforeach
        </div></div></div>
        <div class="mx-auto max-w-7xl px-6 py-8"><div class="rounded-xl border bg-white shadow-sm"><div class="p-6">
            <p x-show="finalized" class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">Periode final tidak dapat diubah.</p>
            <form id="period-wizard-form" @submit.prevent="save()" novalidate>
                <fieldset :disabled="!editable" class="min-w-0">
                    <div x-show="step===0">@include('capstone::pages.admin.periods.steps.basic')</div>
                    <div x-show="step===1">@include('capstone::pages.admin.periods.steps.evaluation')</div>
                    <div x-show="step===2">@include('capstone::pages.admin.periods.steps.phases')</div>
                    <div x-show="step===3">@include('capstone::pages.admin.periods.steps.group')</div>
                    <div x-show="step===4">@include('capstone::pages.admin.periods.steps.review')</div>
                </fieldset>
                <div x-show="Object.keys(errors).length" role="alert" class="mt-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"><template x-for="(messages,key) in errors" :key="key"><p x-text="messages[0]"></p></template></div>
            </form>
        </div><div class="flex items-center justify-between rounded-b-xl border-t bg-gray-50/50 px-6 py-4"><x-capstone::button variant="outline" @click="back()" ::disabled="saving || copying" class="gap-2"><x-capstone::icon name="ArrowLeft" /><span x-text="step===0 ? 'Kembali' : 'Sebelumnya'"></span></x-capstone::button><x-capstone::button x-show="step<4" @click="go(step+1)" ::disabled="saving || copying || (step===1 && !hasTemplates)" class="gap-2">Lanjut<x-capstone::icon name="ChevronRight" /></x-capstone::button><x-capstone::button x-show="step===4" type="submit" form="period-wizard-form" ::disabled="!editable || !hasTemplates" x-text="saving ? 'Menyimpan...' : 'Simpan'" /></div></div></div>
    </div>
</div>
