<x-eoffice::manajemen-praktikum.layout pageTitle="Skema Predikat">
    <div class="mp-page-header"><div><h1 class="mp-page-title">Skema Predikat</h1><p class="mp-page-sub">Aturan predikat per angkatan untuk seluruh praktikum Teknik Komputer.</p></div></div>
    <div class="mp-card" style="overflow:hidden">
        <div class="mp-card-header"><div><h2 class="mp-card-title">Versi Baru Skema Predikat</h2><p style="font-size:12px;color:#666D80;margin:6px 0 0">Versi aktif: {{ $policy->version }}. Versi lama dan nilai yang sudah dipublikasikan tidak diubah.</p></div></div>
        <form method="POST" action="{{ route('eoffice.manprak.admin.grade-policies.store') }}" x-data="{{ json_encode(['rules'=>old('rules',$policy->rules)]) }}">
            @csrf<input type="hidden" name="expected_policy" value="{{ old('expected_policy',$policy->id) }}">
            <div style="padding:16px 20px;display:flex;flex-direction:column;gap:16px">
                <div class="mp-alert warning" style="margin:0">Buat versi baru hanya berdasarkan perubahan aturan akademik. Nilai yang menunggu persetujuan perlu diajukan ulang jika skema berubah.</div>
                <div><label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">Nama Versi <span style="color:#DF1C41">*</span></label><input class="mp-input" name="nama" value="{{ old('nama') }}" maxlength="150" required placeholder="Contoh: Pedoman Predikat Mulai Angkatan 2027" style="width:100%"></div>
                <template x-for="(rule,index) in rules" :key="index">
                    <section style="border:1px solid #DFE1E7;border-radius:10px;overflow:hidden">
                        <div style="padding:12px 16px;background:#F7F8FA;display:flex;align-items:end;gap:12px;flex-wrap:wrap">
                            <div><label style="display:block;font-size:12px;margin-bottom:6px">Mulai Angkatan</label><input class="mp-input" type="number" :name="'rules['+index+'][from]'" x-model="rule.from" min="2000" max="2099" step="1" required style="width:110px"></div>
                            <div><label style="display:block;font-size:12px;margin-bottom:6px">Sampai Angkatan</label><input class="mp-input" type="number" :name="'rules['+index+'][to]'" x-model="rule.to" min="2000" max="2099" step="1" placeholder="Tanpa batas" style="width:130px"></div>
                            <button type="button" class="mp-btn error sm" :disabled="rules.length===1" @click="rules.splice(index,1)">Hapus Rentang</button>
                        </div>
                        <div style="overflow-x:auto"><table class="mp-table" style="width:100%;min-width:500px"><thead><tr><th class="mp-th">NILAI MINIMUM</th><th class="mp-th">PREDIKAT</th><th class="mp-th">BOBOT HURUF</th><th class="mp-th">AKSI</th></tr></thead>
                            <tbody><template x-for="(band,bandIndex) in rule.bands" :key="bandIndex"><tr class="mp-tr">
                                <td style="padding:10px 16px"><input class="mp-input" type="number" :name="'rules['+index+'][bands]['+bandIndex+'][minimum]'" x-model="band.minimum" min="0" max="100" step="0.01" required style="width:90px" aria-label="Nilai minimum"></td>
                                <td style="padding:10px 16px"><input class="mp-input" :name="'rules['+index+'][bands]['+bandIndex+'][letter]'" x-model="band.letter" maxlength="4" required style="width:70px" aria-label="Predikat"></td>
                                <td style="padding:10px 16px"><input class="mp-input" type="number" :name="'rules['+index+'][bands]['+bandIndex+'][points]'" x-model="band.points" min="0" max="4" step="0.01" required style="width:80px" aria-label="Bobot huruf"></td>
                                <td style="padding:10px 16px"><button type="button" class="mp-btn error sm" :disabled="rule.bands.length<=2" @click="rule.bands.splice(bandIndex,1)">Hapus</button></td>
                            </tr></template></tbody>
                        </table></div>
                        <div style="padding:12px 16px"><button type="button" class="mp-btn secondary sm" :disabled="rule.bands.length>=12" @click="rule.bands.push({minimum:'',letter:'',points:''})">Tambah Batas Nilai</button></div>
                    </section>
                </template>
                <p style="font-size:12px;color:#666D80;margin:0;line-height:1.6">Batas nilai mencakup nilai minimum hingga batas berikutnya. Batas terakhir wajib 0. Rentang angkatan harus berurutan dari 2000 dan rentang terakhir tanpa batas atas.</p>
                <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap"><button type="button" class="mp-btn secondary md" :disabled="rules.length>=20" @click="rules.push({from:'',to:null,bands:[{minimum:'',letter:'A',points:'4.00'},{minimum:'0',letter:'E',points:'0.00'}]})">Tambah Rentang Angkatan</button><button class="mp-btn primary md" type="submit">Simpan Versi Baru</button></div>
            </div>
        </form>
    </div>
    <div class="mp-card" style="overflow:hidden">
        <div class="mp-card-header"><h2 class="mp-card-title">Riwayat Skema Predikat</h2></div>
        <div style="overflow-x:auto"><table class="mp-table" style="width:100%"><thead><tr><th class="mp-th">VERSI</th><th class="mp-th">NAMA</th><th class="mp-th">DIBUAT</th><th class="mp-th">STATUS</th></tr></thead><tbody>
            @foreach($history as $version)<tr class="mp-tr"><td style="padding:12px 16px">{{ $version->version }}</td><td style="padding:12px 16px"><details><summary style="cursor:pointer">{{ $version->nama }}</summary><div style="font-size:12px;color:#666D80;padding-top:8px">@foreach($version->rules as $rule)<div>{{ $rule['from'] }}–{{ $rule['to'] ?? 'seterusnya' }}: @foreach($rule['bands'] as $band) ≥ {{ $band['minimum'] }} = {{ $band['letter'] }}@if(!$loop->last); @endif @endforeach</div>@endforeach</div></details></td><td style="padding:12px 16px">{{ $version->created_at->format('d M Y, H:i') }}</td><td style="padding:12px 16px">{{ $version->id===$policy->id ? 'Aktif' : 'Arsip' }}</td></tr>@endforeach
        </tbody></table></div>
    </div>
    <div class="mp-card" style="overflow:hidden">
        <div class="mp-card-header"><div><h2 class="mp-card-title">Verifikasi Angkatan Mahasiswa</h2><p style="font-size:12px;color:#666D80;margin:6px 0 0">Hanya mahasiswa peserta praktikum. Angkatan dibaca dari digit ke-7 dan ke-8 NIM, bukan dua digit pertama.</p></div></div>
        <div style="overflow-x:auto"><table class="mp-table" style="width:100%;min-width:700px"><thead><tr><th class="mp-th">NAMA</th><th class="mp-th">NIM</th><th class="mp-th">ANGKATAN TERSIMPAN</th><th class="mp-th">HASIL BACA NIM</th><th class="mp-th">AKSI</th></tr></thead><tbody>
            @foreach($students as $student)
                @php
                    $cohort=null;$cohortError=null;
                    try{$cohort=app(\Modules\EOffice\Services\ManajemenPraktikum\StudentCohort::class)->fromNim($student->student_number);}catch(\Illuminate\Validation\ValidationException $e){$cohortError=collect($e->errors())->flatten()->first();}
                @endphp
                <tr class="mp-tr"><td style="padding:12px 16px">{{ $student->user?->name }}</td><td style="padding:12px 16px">{{ $student->student_number }}</td><td style="padding:12px 16px">{{ $student->cohort_year }}</td><td style="padding:12px 16px">{{ $cohort ?? 'NIM tidak valid' }}</td><td style="padding:12px 16px">
                    @if($cohort && (string)$cohort!==(string)$student->cohort_year)<form method="POST" action="{{ route('eoffice.manprak.admin.grade-policies.verify',$student->id) }}">@csrf<input type="hidden" name="nim" value="{{ $student->student_number }}"><input type="hidden" name="stored_year" value="{{ $student->cohort_year }}"><button type="submit" class="mp-btn primary sm">Verifikasi Angkatan</button></form>
                    @elseif($cohort)<span style="color:#047857;font-size:12px">Sesuai</span>@else<span style="color:#BE123C;font-size:12px">{{ $cohortError }}</span>@endif
                </td></tr>
            @endforeach
        </tbody></table></div>
        <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$students" :options="[20]" />
    </div>
</x-eoffice::manajemen-praktikum.layout>
