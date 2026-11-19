@if($stage['revisi_aktif'] ?? false)
    <div style="border-top:1px dashed #DFE1E7;padding-top:16px;margin-bottom:{{ ($compact ?? false) ? '0' : '16px' }};font-size:12px;line-height:1.6;color:#666D80">
        <h4 style="margin:0 0 10px;font-size:12px;font-weight:600;color:#353849">Ketentuan Revisi</h4>
        <dl style="display:grid;grid-template-columns:minmax(100px,auto) minmax(0,1fr);gap:6px 16px;margin:0">
            <dt>Tenggat revisi</dt><dd style="margin:0;color:#353849;font-weight:600">{{ \Illuminate\Support\Carbon::parse($stage['revisi_deadline'])->format('d/m/Y, H:i') }}</dd>
            <dt>Metode penilaian</dt><dd style="margin:0;color:#353849;font-weight:600">{{ ['rata_rata'=>'Rata-rata nilai awal dan revisi','berbobot'=>'Berbobot (nilai awal '.($stage['revisi_bobot_awal'] ?? 50).'%)','tertinggi'=>'Nilai tertinggi','ganti'=>'Gunakan nilai revisi'][$stage['revisi_metode'] ?? 'rata_rata'] }}</dd>
        </dl>
        <p style="margin:6px 0 0">Tanpa pengumpulan revisi, nilai awal tetap digunakan.</p>
    </div>
@endif
