<x-eoffice::manajemen-praktikum.layout pageTitle="Skema Predikat">
    <x-eoffice::manajemen-praktikum.ui.admin-settings-styles />
    <div class="mp-page-header"><div><h1 class="mp-page-title">Skema Predikat</h1><p class="mp-page-sub">Aturan predikat per angkatan untuk seluruh praktikum Teknik Komputer.</p></div></div>
    @if(session('success'))<div class="mp-alert success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="mp-alert warning" role="alert"><div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div></div>@endif
    <section class="mp-card mp-settings-card">
        <div class="mp-card-header">
            <div><h2 class="mp-card-title">Versi Baru Skema Predikat</h2><p class="mp-settings-description">Versi lama dan nilai yang sudah dipublikasikan tidak diubah.</p></div>
            <span class="mp-badge success sm">Versi aktif: {{ $policy->version }}</span>
        </div>
        <form method="POST" action="{{ route('eoffice.manprak.admin.grade-policies.store') }}" x-data="{{ json_encode(['rules'=>old('rules',$policy->rules)]) }}">
            @csrf
            <input type="hidden" name="expected_policy" value="{{ old('expected_policy',$policy->id) }}">
            <div class="mp-settings-body">
                <p class="mp-settings-description" style="margin:0">Buat versi baru hanya berdasarkan perubahan aturan akademik. Nilai yang menunggu persetujuan perlu diajukan ulang jika skema berubah.</p>
                <div><label class="mp-settings-label" for="policy-name">Nama Versi <span style="color:var(--c-error,#DF1C41)">*</span></label><input id="policy-name" class="mp-input" name="nama" value="{{ old('nama') }}" maxlength="150" required placeholder="Contoh: Pedoman Predikat Mulai Angkatan 2027"></div>
                <template x-for="(rule,index) in rules" :key="index">
                    <section class="mp-settings-range">
                        <div class="mp-settings-range-head">
                            <div class="mp-settings-field"><label class="mp-settings-label" :for="'policy-from-'+index">Mulai Angkatan</label><input :id="'policy-from-'+index" class="mp-input" type="number" :name="'rules['+index+'][from]'" x-model="rule.from" min="2000" max="2099" step="1" required></div>
                            <div class="mp-settings-field"><label class="mp-settings-label" :for="'policy-to-'+index">Sampai Angkatan</label><input :id="'policy-to-'+index" class="mp-input" type="number" :name="'rules['+index+'][to]'" x-model="rule.to" min="2000" max="2099" step="1" placeholder="Tanpa batas"></div>
                            <button type="button" class="mp-delete-button mp-settings-trash" :disabled="rules.length===1" @click="rules.splice(index,1)" aria-label="Hapus Rentang" title="Hapus Rentang"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button>
                        </div>
                        <div class="mp-data-scroll"><table class="mp-table mp-settings-table" style="min-width:560px">
                            <thead><tr><th class="mp-th mp-settings-no">NO</th><th class="mp-th">NILAI MINIMUM</th><th class="mp-th">PREDIKAT</th><th class="mp-th">BOBOT HURUF</th><th class="mp-th" style="text-align:center;width:64px">AKSI</th></tr></thead>
                            <tbody><template x-for="(band,bandIndex) in rule.bands" :key="bandIndex"><tr class="mp-tr">
                                <td class="mp-settings-no" x-text="bandIndex+1"></td>
                                <td><input class="mp-input" type="number" :name="'rules['+index+'][bands]['+bandIndex+'][minimum]'" x-model="band.minimum" min="0" max="100" step="0.01" required aria-label="Nilai minimum"></td>
                                <td><input class="mp-input" :name="'rules['+index+'][bands]['+bandIndex+'][letter]'" x-model="band.letter" maxlength="4" required aria-label="Predikat"></td>
                                <td><input class="mp-input" type="number" :name="'rules['+index+'][bands]['+bandIndex+'][points]'" x-model="band.points" min="0" max="4" step="0.01" required aria-label="Bobot huruf"></td>
                                <td style="text-align:center"><button type="button" class="mp-delete-button mp-settings-trash" :disabled="rule.bands.length<=2" @click="rule.bands.splice(bandIndex,1)" aria-label="Hapus Batas Nilai" title="Hapus Batas Nilai"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button></td>
                            </tr></template></tbody>
                        </table></div>
                        <div style="padding:12px 20px"><button type="button" class="mp-btn secondary sm" :disabled="rule.bands.length>=12" @click="rule.bands.push({minimum:'',letter:'',points:''})">Tambah Batas Nilai</button></div>
                    </section>
                </template>
                <p class="mp-settings-description" style="margin:0">Batas nilai mencakup nilai minimum hingga batas berikutnya. Batas terakhir wajib 0. Rentang angkatan harus berurutan dari 2000 dan rentang terakhir tanpa batas atas.</p>
                <div><button type="button" class="mp-btn secondary md" :disabled="rules.length>=20" @click="rules.push({from:'',to:null,bands:[{minimum:'',letter:'A',points:'4.00'},{minimum:'0',letter:'E',points:'0.00'}]})">Tambah Rentang Angkatan</button></div>
            </div>
            <div class="mp-settings-footer"><p class="mp-settings-description">Versi baru langsung digunakan untuk perhitungan nilai yang belum dipublikasikan.</p><button class="mp-btn primary md" type="submit">Simpan Versi Baru</button></div>
        </form>
    </section>
    <section class="mp-card mp-settings-card">
        <div class="mp-card-header"><div><h2 class="mp-card-title">Riwayat Skema Predikat</h2><p class="mp-settings-description">Versi aktif dan arsip aturan predikat yang pernah digunakan.</p></div></div>
        <div class="mp-data-scroll"><table class="mp-table mp-settings-table" style="min-width:680px">
            <thead><tr><th class="mp-th mp-settings-no">VERSI</th><th class="mp-th">NAMA</th><th class="mp-th">DIBUAT</th><th class="mp-th" style="text-align:center">STATUS</th></tr></thead>
            <tbody>@forelse($history as $version)<tr class="mp-tr">
                <td class="mp-settings-no">{{ $version->version }}</td>
                <td><details><summary>{{ $version->nama }}</summary><div class="mp-settings-description">@foreach($version->rules as $rule)<div>{{ $rule['from'] }}–{{ $rule['to'] ?? 'seterusnya' }}: @foreach($rule['bands'] as $band) ≥ {{ $band['minimum'] }} = {{ $band['letter'] }}@if(!$loop->last); @endif @endforeach</div>@endforeach</div></details></td>
                <td style="white-space:nowrap;color:var(--c-fg-muted)">{{ $version->created_at->format('d M Y, H:i') }}</td>
                <td style="text-align:center"><span class="mp-badge {{ $version->id===$policy->id ? 'success' : 'neutral' }} sm">{{ $version->id===$policy->id ? 'Aktif' : 'Arsip' }}</span></td>
            </tr>@empty<tr><td colspan="4" class="mp-settings-empty">Belum ada riwayat skema predikat.</td></tr>@endforelse</tbody>
        </table></div>
    </section>
</x-eoffice::manajemen-praktikum.layout>
