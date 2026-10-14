<div x-data="lecturerExpoReview" class="space-y-6">
    <a :href="backUrl" class="inline-flex items-center gap-2 text-sm hover:underline"><x-capstone::icon name="ArrowLeft" class="h-4 w-4" />Back to Evaluations</a>
    <div><h1 class="text-3xl font-bold tracking-tight">Review EXPO</h1><p class="mt-2 text-muted-foreground">Persetujuan pengajuan self-evaluation &amp; dokumen EXPO kelompok (1 keputusan per kelompok)</p></div>
    @include('capstone::partials.loading')
    <template x-if="data && !loading && !error"><div class="grid gap-6 lg:grid-cols-3">
        <aside class="space-y-4">
            <section class="rounded-xl border bg-card p-6">
                <h2 class="font-semibold" x-text="data.group?.code||data.group?.name||'Group '+data.group?.id"></h2>
                <p class="mt-1 text-sm text-muted-foreground" x-text="data.period?.name||''"></p>
                <div class="mt-4 space-y-2 text-sm">
                    <p x-show="data.submitted_by">Perwakilan: <span class="font-medium" x-text="data.submitted_by?.name+' ('+data.submitted_by?.nim+')'"></span></p>
                    <p x-show="data.registration?.registered_at">Terdaftar: <span x-text="date(data.registration.registered_at)"></span></p>
                    <p x-show="data.registration?.reviewed_at">Direview: <span x-text="date(data.registration.reviewed_at,true)"></span></p>
                </div>
            </section>
            <section class="rounded-xl border bg-card p-6">
                <h2 class="mb-3 font-semibold">Anggota Kelompok</h2>
                <div class="space-y-2"><template x-for="member in data.members||[]" :key="member.id"><div class="flex items-center justify-between gap-2 border-t py-2 text-sm"><div class="min-w-0"><p class="truncate font-medium" x-text="member.name"></p><p class="text-xs text-muted-foreground" x-text="member.nim"></p></div><x-capstone::badge x-show="member.is_representative" variant="secondary" class="shrink-0 bg-primary/10 text-primary text-xs">Perwakilan</x-capstone::badge></div></template></div>
            </section>
            <section class="rounded-xl border bg-card p-6">
                <h2 class="mb-3 font-semibold">Ringkasan Nilai</h2>
                <p class="text-3xl font-black text-primary" x-text="weighted"></p>
                <p class="mt-1 text-xs text-muted-foreground">Weighted score dari self-evaluation perwakilan. Terhitung di PDC2 setelah disetujui.</p>
            </section>
        </aside>
        <div class="space-y-4 lg:col-span-2">
            <div x-show="reviewStatus==='SUBMITTED'" class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800"><p class="font-semibold">Menunggu keputusan Anda</p><p>Pengajuan self-evaluation dan dokumen kelompok sedang menunggu persetujuan.</p></div>
            <div x-show="reviewStatus==='APPROVED'" class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800"><p class="font-semibold">Disetujui</p><p>Pengajuan disetujui — nilai EXPO berlaku untuk seluruh anggota dan terhitung di PDC2.</p><p x-show="data.registration?.review_feedback" class="mt-1" x-text="'Catatan: '+data.registration.review_feedback"></p></div>
            <div x-show="reviewStatus==='REJECTED'" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><p class="font-semibold">Ditolak</p><p>Kelompok diperbolehkan memperbaiki self-evaluation dan mengunggah ulang dokumen.</p><p x-show="data.registration?.review_feedback" class="mt-1" x-text="'Catatan: '+data.registration.review_feedback"></p></div>

            <section class="rounded-xl border bg-card p-6">
                <h2 class="mb-4 font-semibold">Self-Evaluation Perwakilan</h2>
                <p x-show="!(data.components||[]).length" class="text-sm text-muted-foreground">Komponen penilaian EXPO belum dikonfigurasi.</p>
                <div x-show="(data.components||[]).length" class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-muted/40"><tr><th class="p-3 font-medium">Komponen</th><th class="p-3 font-medium">Bobot</th><th class="p-3 font-medium">Nilai</th><th class="p-3 font-medium">Catatan</th></tr></thead><tbody><template x-for="component in data.components||[]" :key="component.period_component_id"><tr class="border-t"><td class="p-3"><span class="mr-2 text-xs font-bold text-primary" x-text="component.code"></span><span x-text="component.name"></span></td><td class="p-3" x-text="component.weight+'%'"></td><td class="p-3 font-semibold" x-text="component.score ?? '—'"></td><td class="p-3 text-muted-foreground" x-text="component.notes||'—'"></td></tr></template></tbody></table></div>
            </section>

            <section class="rounded-xl border bg-card p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div><h2 class="font-semibold">Dokumen EXPO</h2><p class="mt-1 text-sm text-muted-foreground" x-show="!document">Belum ada dokumen yang diunggah.</p></div>
                    <x-capstone::button x-show="document" variant="outline" size="sm" @click="download()" ::disabled="!!downloading"><x-capstone::icon name="Download" /><span x-text="downloading?'Mengunduh...':'Unduh Dokumen'"></span></x-capstone::button>
                </div>
                <div x-show="document" class="mt-4 space-y-1 rounded-lg border bg-muted/20 p-4 text-sm"><p class="font-medium" x-text="document?.original_name"></p><p class="text-muted-foreground" x-text="'Status: '+(document?.status||'—')+(document?.uploaded_by?.name?' • oleh '+document.uploaded_by.name:'')"></p><p class="text-muted-foreground" x-show="document?.feedback" x-text="'Catatan: '+document.feedback"></p><p class="text-muted-foreground" x-show="document?.created_at" x-text="'Diunggah '+date(document.created_at)"></p></div>
            </section>

            <section class="rounded-xl border bg-card p-6" x-show="editable">
                <h2 class="mb-2 font-semibold">Keputusan</h2>
                <label class="grid gap-2 text-sm"><span>Catatan untuk kelompok <span class="text-muted-foreground">(wajib saat menolak)</span></span><textarea x-model="feedback" rows="3" maxlength="2000" class="rounded-md border bg-background p-3 text-sm" placeholder="Feedback / alasan penolakan..."></textarea></label>
                <div class="mt-4 flex flex-wrap gap-3">
                    <x-capstone::button @click="review('APPROVED')" ::disabled="saving"><x-capstone::icon name="CircleCheck" /><span x-text="saving?'Menyimpan...':'Setujui Pengajuan'"></span></x-capstone::button>
                    <x-capstone::button variant="destructive" @click="review('REJECTED')" ::disabled="saving"><x-capstone::icon name="CircleX" />Tolak Pengajuan</x-capstone::button>
                </div>
            </section>
            <p x-show="!editable && reviewStatus" class="rounded-lg border border-border bg-accent p-4 text-sm" x-text="reviewStatus==='APPROVED'?'Pengajuan sudah disetujui dan terkunci.':(reviewStatus==='REJECTED'?'Pengajuan ditolak — menunggu perbaikan dari kelompok.':'Pengajuan tidak dapat direview pada status kelompok saat ini.')"></p>

            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700" x-show="errors.root" role="alert"><p x-text="errors.root"></p></div>
        </div>
    </div></template>
</div>
