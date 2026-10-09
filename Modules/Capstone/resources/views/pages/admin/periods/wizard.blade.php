<div class="min-h-screen" x-data="periodWizard(@js($periodId))">
    <div class="mx-auto max-w-7xl space-y-5 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
        <p class="text-sm"><span class="text-gray-400">SICATA</span><span class="mx-2 text-gray-300">/</span><span class="font-medium text-gray-900">Periode</span></p>
        <div class="h-px bg-gray-100"></div>
        <div class="flex items-center justify-between gap-3">
            <button type="button" @click="backToList()" class="inline-flex h-10 items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-600 hover:bg-gray-50"><x-capstone::icon name="ChevronLeft" class="h-4 w-4" />Kembali</button>
            <button type="button" x-show="step<5" @click="next()" :disabled="saving || copying || (step===1 && !hasTemplates)" class="inline-flex h-10 items-center rounded-lg bg-[#1E2A5A] px-6 text-sm font-medium text-white hover:bg-[#162040] disabled:opacity-50">Lanjut</button>
            <button type="button" x-show="step===5" @click="save()" :disabled="!editable" class="inline-flex h-10 items-center rounded-lg bg-[#1E2A5A] px-6 text-sm font-medium text-white hover:bg-[#162040] disabled:opacity-50" x-text="saving ? 'Menyimpan...' : 'Simpan'"></button>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $periodId ? 'Edit Periode' : 'Tambah Periode Baru' }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $periodId ? 'Perbarui konfigurasi periode yang sudah ada.' : 'Buat periode baru dengan konfigurasi langkah demi langkah.' }}</p>
        </div>
        @include('capstone::partials.loading')
        <div x-show="!loading && !error" x-cloak>
            <nav class="flex items-center gap-1 overflow-x-auto whitespace-nowrap py-1 text-sm" aria-label="Langkah periode">
                <button type="button" @click="go(0)" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 font-medium" :class="step===0 ? 'border-[#1E2A5A] bg-indigo-50 text-[#1E2A5A]' : 'border-transparent text-[#1E2A5A]'" :disabled="saving || copying"><x-capstone::icon name="Info" class="h-4 w-4" />Informasi Dasar</button>
                <x-capstone::icon name="ChevronRight" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
                <button type="button" @click="go(1)" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 font-medium" :class="step===1 ? 'border-[#1E2A5A] bg-indigo-50 text-[#1E2A5A]' : (step>1 ? 'border-transparent text-[#1E2A5A]' : 'border-transparent text-gray-500')" :disabled="saving || copying"><x-capstone::icon name="Settings" class="h-4 w-4" />Setup Tipe Penilaian</button>
                <x-capstone::icon name="ChevronRight" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
                <button type="button" @click="go(2)" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 font-medium" :class="step===2 ? 'border-[#1E2A5A] bg-indigo-50 text-[#1E2A5A]' : (step>2 ? 'border-transparent text-[#1E2A5A]' : 'border-transparent text-gray-500')" :disabled="saving || copying"><x-capstone::icon name="Settings" class="h-4 w-4" />Peer Review</button>
                <x-capstone::icon name="ChevronRight" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
                <button type="button" @click="go(3)" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 font-medium" :class="step===3 ? 'border-[#1E2A5A] bg-indigo-50 text-[#1E2A5A]' : (step>3 ? 'border-transparent text-[#1E2A5A]' : 'border-transparent text-gray-500')" :disabled="saving || copying"><x-capstone::icon name="Calendar" class="h-4 w-4" />Tanggal Fase</button>
                <x-capstone::icon name="ChevronRight" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
                <button type="button" @click="go(4)" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 font-medium" :class="step===4 ? 'border-[#1E2A5A] bg-indigo-50 text-[#1E2A5A]' : (step>4 ? 'border-transparent text-[#1E2A5A]' : 'border-transparent text-gray-500')" :disabled="saving || copying"><x-capstone::icon name="Users" class="h-4 w-4" />Konfigurasi Group</button>
                <x-capstone::icon name="ChevronRight" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
                <button type="button" @click="go(5)" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 font-medium" :class="step===5 ? 'border-[#1E2A5A] bg-indigo-50 text-[#1E2A5A]' : 'border-transparent text-gray-500'" :disabled="saving || copying"><x-capstone::icon name="Eye" class="h-4 w-4" />Review</button>
            </nav>
            <div class="mt-4 rounded-2xl border border-gray-200 bg-white">
                <form id="period-wizard-form" @submit.prevent="save()" novalidate>
                    <fieldset :disabled="!editable" class="min-w-0">
                        <div x-show="step===0">@include('capstone::pages.admin.periods.steps.basic')</div>
                        <div x-show="step===1">@include('capstone::pages.admin.periods.steps.evaluation-setup')</div>
                        <div x-show="step===2">@include('capstone::pages.admin.periods.steps.peer')</div>
                        <div x-show="step===3">@include('capstone::pages.admin.periods.steps.phases')</div>
                        <div x-show="step===4">@include('capstone::pages.admin.periods.steps.group')</div>
                        <div x-show="step===5">@include('capstone::pages.admin.periods.steps.review')</div>
                    </fieldset>
                    <div x-show="Object.keys(errors).length" role="alert" class="mx-6 mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"><template x-for="(messages,key) in errors" :key="key"><p x-text="messages[0]"></p></template></div>
                </form>
                <div x-show="step===1" class="flex items-center justify-between gap-3 rounded-b-2xl border-t border-gray-100 px-6 py-4">
                    <button type="button" @click="step===1 ? prevType() : back()" :disabled="saving || copying" class="inline-flex h-10 items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-600 hover:bg-gray-50 disabled:opacity-50"><x-capstone::icon name="ChevronLeft" class="h-4 w-4" />Kembali</button>
                    <button type="button" x-show="step<5" @click="step===1 ? nextType() : next()" :disabled="saving || copying || (step===1 && !hasTemplates)" class="inline-flex h-10 items-center rounded-lg bg-[#1E2A5A] px-6 text-sm font-medium text-white hover:bg-[#162040] disabled:opacity-50">Lanjut</button>
                    <button type="button" x-show="step===5" @click="save()" :disabled="!editable" class="inline-flex h-10 items-center rounded-lg bg-[#1E2A5A] px-6 text-sm font-medium text-white hover:bg-[#162040] disabled:opacity-50" x-text="saving ? 'Menyimpan...' : 'Simpan'"></button>
                </div>
            </div>
        </div>
    </div>
</div>
