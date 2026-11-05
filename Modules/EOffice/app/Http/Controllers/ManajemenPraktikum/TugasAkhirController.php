<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{Praktikum, DaftarPraktikan, TugasAkhir, TaPengumpulan, TaNilai, TaAbsensi};
use Modules\EOffice\Services\NotifikasiService;

class TugasAkhirController extends Controller
{
    private array $newPaths = [];

    private function transaction(callable $callback): void
    {
        $this->newPaths = [];
        try {
            DB::transaction($callback);
        } catch (\Throwable $exception) {
            // Only clean files created by this request, never older submission versions.
            foreach ($this->newPaths as $path) Storage::disk('local')->delete($path);
            throw $exception;
        } finally {
            $this->newPaths = [];
        }
    }
    private function context(Request $request): array
    {
        $role = $request->route('taRole');
        $praktikum = Praktikum::findOrFail($request->route('praktikum'));
        $id = auth()->id();
        $allowed = match ($role) {
            'koor' => (string) $praktikum->koor_id === (string) $id,
            'dosen' => $praktikum->dosens()->where('users.id', $id)->exists(),
            'asprak' => $praktikum->asistenPraktikum()->where('user_id', $id)->exists(),
            'mahasiswa' => $praktikum->daftarPraktikan()->where('user_id', $id)->exists(),
            default => false,
        };
        abort_unless($allowed, 403);
        if (!$request->isMethod('GET')) $this->check((bool) $praktikum->is_active, 'Praktikum telah diarsipkan dan tidak dapat diubah.');
        return [$praktikum, $role];
    }

    private function task(Praktikum $praktikum, bool $lock = false): TugasAkhir
    {
        if($lock) Praktikum::whereKey($praktikum->id)->lockForUpdate()->firstOrFail();
        $query = TugasAkhir::where('praktikum_id', $praktikum->id);
        return ($lock ? $query->lockForUpdate() : $query)->firstOrFail();
    }

    private function check(bool $condition, string $message): void
    {
        if (!$condition) throw ValidationException::withMessages(['tugas_akhir' => $message]);
    }

    private function storeFile($file, Praktikum $praktikum): array
    {
        $path = $file->storeAs('tugas-akhir/'.$praktikum->id, Str::uuid().'.bin', 'local');
        $this->check(is_string($path) && $path !== '', 'File gagal disimpan. Silakan coba kembali.');
        $this->newPaths[] = $path;
        return ['path' => $path, 'nama' => $file->getClientOriginalName()];
    }

    private function notify(Praktikum $praktikum, string $message): void
    {
        $ids = $praktikum->daftarPraktikan()->pluck('user_id')
            ->merge($praktikum->asistenPraktikum()->pluck('user_id'))
            ->merge($praktikum->dosens()->pluck('users.id'))->push($praktikum->koor_id)->filter()->unique();
        app(NotifikasiService::class)->kirimBulk($ids, 'Tugas Akhir — '.$praktikum->nama, $message);
    }

    private function invalidateGrade(TugasAkhir $ta, string $studentId, string $event, ?string $stageId = null): void
    {
        if ($stageId && !count($ta->componentsFor($stageId))) return;
        $this->invalidatePublication($ta->praktikum_id);
        $grade = TaNilai::where('tugas_akhir_id', $ta->id)->where('praktikan_id', $studentId)->lockForUpdate()->first();
        if (!$grade) return;
        $history = $grade->riwayat;
        $pending = data_get(collect($history)->last(), 'belum_ditinjau', []);
        if ($stageId) $pending[] = $stageId;
        $history[] = ['waktu' => now()->toIso8601String(), 'oleh' => auth()->user()->name, 'jenis' => $event, 'tahap_id'=>$stageId, 'belum_ditinjau'=>array_values(array_unique($pending)), 'sebelum' => $grade->skor, 'sesudah' => $grade->skor];
        $grade->update(['riwayat' => $history, 'disetujui_koor' => false, 'disetujui_dosen' => false, 'version' => $grade->version + 1]);
    }

    private function invalidatePublication(string $praktikumId): void
    {
        $ids=DaftarPraktikan::where('praktikum_id',$praktikumId)->pluck('id');
        \Modules\EOffice\Models\Nilai::whereIn('daftar_praktikan_id',$ids)->update(['disetujui_koor'=>false,'disetujui_dosen'=>false,'dipublikasikan'=>false]);
        TaNilai::whereIn('tugas_akhir_id',TugasAkhir::where('praktikum_id',$praktikumId)->select('id'))->update(['disetujui_koor'=>false,'disetujui_dosen'=>false]);
    }

    public function index(Request $request)
    {
        [$praktikum, $role] = $this->context($request);
        $sessionKey = match ($role) { 'koor' => 'koor_praktikum_id', 'asprak' => 'manprak_asprak_praktikum_id', 'mahasiswa' => 'mhs_praktikum_id', default => null };
        if ($sessionKey) session([$sessionKey => $praktikum->id]);
        $ta = TugasAkhir::where('praktikum_id', $praktikum->id)->first();
        $visible = $ta && ($ta->published || $role === 'koor');
        $praktikan = $praktikum->daftarPraktikan()->with('user.student')
            ->when($role === 'mahasiswa', fn ($q) => $q->where('user_id', auth()->id()))->get();
        $submissions = $visible ? TaPengumpulan::where('tugas_akhir_id', $ta->id)->whereIn('praktikan_id', $praktikan->pluck('id'))->get() : collect();
        $grades = $visible ? TaNilai::where('tugas_akhir_id', $ta->id)->whereIn('praktikan_id', $praktikan->pluck('id'))
            ->when($role === 'mahasiswa', fn ($q) => $q->where('disetujui_koor', true)->where('disetujui_dosen', true))->get()->keyBy('praktikan_id') : collect();
        $attendance = $visible ? TaAbsensi::where('tugas_akhir_id', $ta->id)->whereIn('praktikan_id', $praktikan->pluck('id'))->get()->keyBy('praktikan_id') : collect();
        $section = $request->route('taSection') ?? 'modul';
        $selectedStage = $request->query('tahap_id');
        if ($selectedStage) abort_unless($visible && collect($ta->tahap)->contains('id', $selectedStage), 404);
        if (!$visible) $ta = null;
        $view = $section === 'tugas' && $selectedStage && in_array($role, ['asprak', 'dosen']) ? 'pengumpulan' : 'index';
        if ($view === 'pengumpulan') $praktikan->loadMissing('user.student');
        return view('eoffice::manajemen-praktikum.tugas-akhir.'.$view, compact('praktikum', 'role', 'ta', 'praktikan', 'submissions', 'grades', 'attendance', 'section', 'selectedStage'));
    }

    public function save(Request $request)
    {
        [$praktikum, $role] = $this->context($request);
        abort_unless($role === 'koor', 403);
        $data = $request->validate([
            'version' => 'required|integer|min:0', 'ketentuan' => 'required|string|max:5000',
            'tipe'=>'sometimes|required|in:individu,kelompok,kelompok_besar',
            'tahap' => 'required|array|min:1|max:50', 'tahap.*.id' => 'nullable|uuid',
            'tahap.*.nama' => 'required|string|max:150', 'tahap.*.ketentuan' => 'required|string|max:5000',
            'tahap.*.deadline' => 'nullable|date', 'komponen' => 'sometimes|array|max:100',
            'tahap.*.dibuka_pada'=>'nullable|date',
            'konfirmasi_hapus_tahap'=>'nullable|array|max:50', 'konfirmasi_hapus_tahap.*'=>'uuid',
            'hapus_lampiran'=>'nullable|array|max:200', 'hapus_lampiran.*'=>'required|string|max:500',
            'tahap.*.pengumpulan' => 'sometimes|boolean',
            'tahap.*.parameter' => 'sometimes|array|max:50',
            'tahap.*.parameter.*.id' => 'nullable|uuid',
            'tahap.*.parameter.*.nama' => 'required|string|max:150',
            'tahap.*.parameter.*.bobot' => 'nullable|integer|min:1|max:100',
            'tahap.*.revisi_aktif' => 'sometimes|boolean',
            'tahap.*.revisi_deadline' => 'nullable|date',
            'tahap.*.revisi_metode' => 'nullable|in:rata_rata,berbobot,tertinggi,ganti',
            'tahap.*.revisi_bobot_awal' => 'nullable|numeric|min:0|max:100',
            'tahap.*.lampiran' => 'nullable|array|max:3', 'tahap.*.lampiran.*' => 'file|max:5120',
            'komponen.*.id' => 'nullable|uuid', 'komponen.*.nama' => 'required|string|max:150',
            'komponen.*.bobot' => 'required|integer|gt:0|max:100', 'publish' => 'nullable|boolean',
            'lampiran' => 'nullable|array|max:3', 'lampiran.*' => 'file|max:5120',
        ]);
        if (isset($data['komponen'])) $this->check(abs(collect($data['komponen'])->sum('bobot') - 100) < 0.001, 'Total bobot komponen harus 100%.');
        $parametersChanged = false;
        $this->transaction(function () use ($praktikum, $data, $request, &$parametersChanged) {
            Praktikum::whereKey($praktikum->id)->lockForUpdate()->firstOrFail();
            $ta = TugasAkhir::where('praktikum_id', $praktikum->id)->lockForUpdate()->first();
            $this->check((int) ($ta?->version ?? 0) === (int) $data['version'], 'Ketentuan telah berubah. Muat ulang halaman sebelum menyimpan.');
            $tahap = $this->withIds($data['tahap'], $ta?->tahap ?? []);
            foreach ($tahap as $index => &$stage) {
                $oldStage = collect($ta?->tahap ?? [])->firstWhere('id', $stage['id']);
                $stage['pengumpulan'] = (bool)($stage['pengumpulan'] ?? true);
                $this->check(!$stage['pengumpulan'] || !empty($stage['deadline']), 'Tenggat wajib diisi untuk tahap dengan pengumpulan.');
                if ($stage['pengumpulan'] && !empty($stage['dibuka_pada'])) $this->check(\Illuminate\Support\Carbon::parse($stage['dibuka_pada'])->lt($stage['deadline']), 'Ditutup Pada harus setelah Dibuka Pada.');
                if (!isset($data['komponen'])) $this->check(count($stage['parameter'] ?? []) > 0, 'Isi minimal satu Kolom Penilaian pada setiap tahap.');
                $stage['revisi_aktif'] = (bool)($stage['revisi_aktif'] ?? false);
                $stage['revisi_metode'] = $stage['revisi_metode'] ?? 'rata_rata';
                $stage['revisi_bobot_awal'] = (float)($stage['revisi_bobot_awal'] ?? 50);
                if ($oldStage && TaNilai::where('tugas_akhir_id', $ta->id)->get()->contains(fn($g) => count(array_intersect(array_keys($g->revisionScores()), collect($ta->componentsFor($stage['id']))->pluck('id')->all())) > 0)) {
                    $this->check(($stage['revisi_metode'] === ($oldStage['revisi_metode'] ?? 'rata_rata')) && $stage['revisi_bobot_awal'] === (float)($oldStage['revisi_bobot_awal'] ?? 50), 'Metode revisi dikunci setelah nilai revisi tersimpan.');
                }
                $this->check(!$stage['revisi_aktif'] || ($stage['pengumpulan'] && !empty($stage['revisi_deadline'])), 'Revisi memerlukan pengumpulan dan tenggat revisi.');
                $stageGraded=$oldStage && TaNilai::where('tugas_akhir_id',$ta->id)->get()->contains(fn($g)=>collect($ta->componentsFor($stage['id']))->contains(fn($c)=>isset($g->skor[$c['id']]) || isset($g->initialScores()[$c['id']]) || isset($g->revisionScores()[$c['id']])));
                if ($oldStage && ($stageGraded || TaPengumpulan::where('tugas_akhir_id', $ta->id)->where('tahap_id', $stage['id'])->exists())) {
                    $this->check($stage['pengumpulan'] === (bool)($oldStage['pengumpulan'] ?? true), 'Pengumpulan tidak dapat dinonaktifkan setelah digunakan.');
                    $oldIds=collect($ta->componentsFor($stage['id']))->pluck('id')->all();
                    if (!isset($data['komponen'])) $this->check(collect($stage['parameter'] ?? [])->pluck('id')->all()===$oldIds && collect($stage['parameter'])->every(fn($p)=>$p['nama']===collect($ta->componentsFor($stage['id']))->firstWhere('id',$p['id'])['nama']), 'Susunan kolom penilaian dikunci karena tahap sudah memiliki pengumpulan.');
                    if (!empty($oldStage['deadline'])) $this->check(\Illuminate\Support\Carbon::parse($stage['deadline'])->gte($oldStage['deadline']), 'Tenggat pengumpulan tidak dapat dipersingkat setelah ada pengumpulan.');
                }
                $attachments = collect($oldStage['lampiran'] ?? [])->whereNotIn('path', $data['hapus_lampiran'] ?? [])->values()->all();
                $newFiles = $request->file('tahap.'.$index.'.lampiran', []);
                $this->check(count($attachments) + count($newFiles) <= 3, 'Maksimal 3 lampiran per tahap.');
                foreach ($newFiles as $file) $attachments[] = $this->storeFile($file, $praktikum);
                $stage['lampiran'] = $attachments;
            }
            unset($stage);
            if (isset($data['komponen'])) {
                $komponen = $this->withIds($data['komponen'], $ta?->komponen ?? []);
            } else {
                $komponen = [];
                foreach ($tahap as &$stage) {
                    $oldComponents = $ta ? $ta->componentsFor($stage['id']) : [];
                    foreach ($this->withIds($stage['parameter'] ?? [], $oldComponents) as $component) {
                        $oldComponent = collect($oldComponents)->firstWhere('id', $component['id']);
                        $weight=array_key_exists('bobot',$component) ? (blank($component['bobot']) ? null : (int)$component['bobot']) : ($oldComponent['bobot'] ?? null);
                        $komponen[] = ['id'=>$component['id'], 'nama'=>$component['nama'], 'tahap_id'=>$stage['id'], 'bobot'=>$weight];
                    }
                    unset($stage['parameter']);
                }
                unset($stage);
            }
            $weightsSubmitted=collect($data['tahap'])->flatMap(fn($stage)=>$stage['parameter']??[])->contains(fn($parameter)=>array_key_exists('bobot',$parameter));
            if ($weightsSubmitted && collect($komponen)->every(fn($c)=>$c['bobot']!==null)) $this->check(abs(collect($komponen)->sum('bobot')-100)<.001, 'Total bobot seluruh kolom tugas akhir harus 100%.');
            if ($ta) {
                $oldWeights=collect($ta->komponen)->pluck('bobot','id');
                $weightsChanged=collect($komponen)->contains(fn($c)=>$oldWeights->has($c['id']) && $oldWeights[$c['id']] != $c['bobot']);
                if ($weightsChanged) $this->check(!TaNilai::where('tugas_akhir_id',$ta->id)->where(fn($q)=>$q->where('disetujui_koor',true)->orWhere('disetujui_dosen',true))->exists(), 'Batalkan persetujuan atau publikasi nilai sebelum mengubah bobot tugas akhir.');
            }
            if ($ta) {
                $used = TaPengumpulan::where('tugas_akhir_id', $ta->id)->pluck('tahap_id')->unique();
                $this->check($used->diff(collect($tahap)->pluck('id'))->isEmpty(), 'Tahap yang sudah memiliki pengumpulan tidak dapat dihapus.');
                $removed = collect($ta->komponen)->whereNotIn('id', collect($komponen)->pluck('id'));
                if (isset($data['komponen'])) foreach($ta->tahap as $oldStage) {
                    $oldComponents=$ta->componentsFor($oldStage['id']);
                    $usedStage=TaPengumpulan::where('tugas_akhir_id',$ta->id)->where('tahap_id',$oldStage['id'])->exists()
                        || TaNilai::where('tugas_akhir_id',$ta->id)->get()->contains(fn($g)=>collect($oldComponents)->contains(fn($c)=>isset($g->skor[$c['id']]) || isset($g->initialScores()[$c['id']]) || isset($g->revisionScores()[$c['id']])));
                    if($usedStage) $this->check(collect($oldComponents)->map(fn($c)=>[$c['id'],$c['nama']])->all()===collect($komponen)->filter(fn($c)=>($c['tahap_id']??$tahap[0]['id'])===$oldStage['id'])->map(fn($c)=>[$c['id'],$c['nama']])->values()->all(),'Susunan kolom penilaian dikunci karena tahap sudah memiliki pengumpulan atau nilai.');
                }
                $grades = TaNilai::where('tugas_akhir_id', $ta->id)->lockForUpdate()->get();
                foreach (collect($ta->tahap)->whereNotIn('id', collect($tahap)->pluck('id')) as $removedStage) {
                    foreach ($ta->componentsFor($removedStage['id']) as $component) {
                        $id = $component['id'];
                        $this->check(!$grades->contains(fn($g) => isset($g->skor[$id]) || isset($g->initialScores()[$id]) || isset($g->revisionScores()[$id])), 'Tahap masih memiliki nilai. Kosongkan nilainya di Absensi & Nilai sebelum menghapus.');
                        $komponen = collect($komponen)->reject(fn($c) => $c['id'] === $id)->values()->all();
                    }
                }
                foreach ($removed as $component) {
                    $id = $component['id'];
                    $this->check(!$grades->contains(fn($g) => isset($g->skor[$id]) || isset($g->initialScores()[$id]) || isset($g->revisionScores()[$id])), 'Parameter "'.$component['nama'].'" masih memiliki nilai. Kosongkan nilainya di Absensi & Nilai sebelum menghapus.');
                }
                if ($ta->komponen != $komponen) {
                    $parametersChanged = true;
                    $this->invalidatePublication($praktikum->id);
                    foreach ($grades as $grade) $grade->update(['disetujui_koor'=>false, 'disetujui_dosen'=>false, 'version'=>$grade->version+1]);
                }
            }
            $history = $ta?->riwayat ?? [];
            if ($ta) $history[] = ['waktu' => now()->toIso8601String(), 'oleh' => auth()->user()->name, 'ketentuan' => $ta->ketentuan, 'lampiran'=>$ta->lampiran, 'tahap' => $ta->tahap, 'komponen' => $ta->komponen];
            $attachments = collect($ta?->lampiran ?? [])->whereNotIn('path', $data['hapus_lampiran'] ?? [])->values()->all();
            $this->check(count($attachments) + count($request->file('lampiran', [])) <= 3, 'Maksimal 3 lampiran ketentuan.');
            foreach ($request->file('lampiran', []) as $file) {
                $attachments[] = $this->storeFile($file, $praktikum);
            }
            $published = ($ta?->published ?? false) || $request->boolean('publish');
            TugasAkhir::updateOrCreate(['praktikum_id' => $praktikum->id], [
                'ketentuan' => $data['ketentuan'], 'tipe'=>$data['tipe'] ?? $ta?->tipe, 'tahap' => $tahap, 'komponen' => $komponen,
                'published' => $published, 'lampiran' => $attachments, 'riwayat' => $history,
                'version' => ($ta?->version ?? 0) + 1,
            ]);
            if ($published) $this->notify($praktikum, 'Ketentuan tugas akhir telah diperbarui di halaman Modul. Penugasan dan tenggat tersedia di halaman Tugas.');
        });
        return back()->with('success', $parametersChanged
            ? 'Parameter diperbarui. Periksa bobot dan kelengkapan nilai; persetujuan nilai perlu diulang.'
            : 'Ketentuan tugas akhir berhasil disimpan.');
    }

    private function withIds(array $rows, array $old): array
    {
        $ids = collect($old)->pluck('id');
        $result = [];
        foreach ($rows as $row) {
            $this->check(empty($row['id']) || $ids->contains($row['id']), 'Identitas tahap atau komponen tidak sesuai.');
            $row['id'] = !empty($row['id']) ? $row['id'] : (string) Str::uuid();
            $result[] = $row;
        }
        $this->check(collect($result)->pluck('id')->unique()->count() === count($result), 'Tahap atau komponen tidak boleh duplikat.');
        return $result;
    }

    public function submit(Request $request)
    {
        [$praktikum, $role] = $this->context($request);
        abort_unless($role === 'mahasiswa', 403);
        $data = $request->validate(['tahap_id' => 'required|uuid', 'files' => 'nullable|array|max:3', 'files.*' => 'file|max:5120', 'link' => 'nullable|url:http,https|max:2000', 'reuse_files'=>'nullable|boolean']);
        $this->transaction(function () use ($praktikum, $request, $data) {
            $ta = $this->task($praktikum, true);
            $this->check($ta->published, 'Tugas akhir belum dipublikasikan.');
            $stage = collect($ta->tahap)->firstWhere('id', $data['tahap_id']);
            abort_unless($stage, 404);
            $this->check((bool)($stage['pengumpulan'] ?? true), 'Tahap ini tidak memerlukan pengumpulan.');
            $student = $praktikum->daftarPraktikan()->where('user_id', auth()->id())->firstOrFail();
            $submission = TaPengumpulan::where('tugas_akhir_id', $ta->id)->where('praktikan_id', $student->id)->where('tahap_id', $stage['id'])->lockForUpdate()->first();
            $resubmit=$submission && $submission->status==='dibatalkan';
            $this->check(empty($stage['dibuka_pada']) || now()->gte($stage['dibuka_pada']), 'Pengumpulan belum dibuka.');
            $deadline = \Illuminate\Support\Carbon::parse($stage['deadline']);
            $late = (!$submission || $resubmit) && now()->gt($deadline);
            if ($late && ($stage['revisi_aktif'] ?? false) && !empty($stage['revisi_deadline'])) {
                $deadline = \Illuminate\Support\Carbon::parse($stage['revisi_deadline']);
            }
            if ($submission && !$resubmit) {
                $this->check(($stage['revisi_aktif'] ?? false) && !empty($stage['revisi_deadline']) && now()->lte($stage['revisi_deadline']), 'Periode revisi sudah ditutup.');
                $deadline = \Illuminate\Support\Carbon::parse($stage['revisi_deadline']);
            }
            $this->check(now()->lte($deadline), 'Tenggat pengumpulan sudah berakhir.');
            $files = [];
            if ($request->boolean('reuse_files')) {
                $this->check($resubmit, 'Berkas lama hanya dapat digunakan saat mengumpulkan kembali pengumpulan yang dibatalkan.');
                $previous=collect($submission->riwayat)->last(fn($entry)=>($entry['jenis']??null)==='pengumpulan');
                $files=$previous['files'] ?? [];
            }
            $this->check(count($files)+count($request->file('files',[]))<=3, 'Maksimal 3 file per pengumpulan.');
            $this->check($request->hasFile('files') || count($files)>0 || !empty($data['link']), 'Sertakan minimal satu file atau link.');
            foreach ($request->file('files', []) as $file) {
                $files[] = $this->storeFile($file, $praktikum);
            }
            $history = $submission?->riwayat ?? [];
            $history[] = ['jenis' => 'pengumpulan', 'kategori'=>($submission && !$resubmit) ? 'revisi' : ($late ? 'terlambat' : 'awal'), 'waktu' => now()->toIso8601String(), 'oleh' => auth()->user()->name, 'files' => $files, 'link' => $data['link'] ?? null];
            TaPengumpulan::updateOrCreate(['tugas_akhir_id' => $ta->id, 'praktikan_id' => $student->id, 'tahap_id' => $stage['id']], ['status' => 'dikumpulkan', 'riwayat' => $history, 'catatan' => null, 'deadline_revisi' => null]);
            $this->invalidateGrade($ta, $student->id, 'pengumpulan_baru', $stage['id']);
            $recipients = $praktikum->asistenPraktikum()->pluck('user_id')->push($praktikum->koor_id)->filter()->unique();
            app(NotifikasiService::class)->kirimBulk($recipients, 'Pengumpulan Tugas Akhir', auth()->user()->name.' mengumpulkan tahap '.$stage['nama'].' pada '.$praktikum->nama.'.');
        });
        return back()->with('success', 'Tugas berhasil dikumpulkan.');
    }

    public function unsubmit(Request $request)
    {
        [$praktikum,$role]=$this->context($request);
        abort_unless($role==='mahasiswa',403);
        $data=$request->validate(['tahap_id'=>'required|uuid','submission_token'=>'required|string|size:64']);
        DB::transaction(function() use($praktikum,$data){
            $ta=$this->task($praktikum,true);
            $this->check($ta->published,'Tugas akhir belum dipublikasikan.');
            $stage=collect($ta->tahap)->firstWhere('id',$data['tahap_id']);
            $this->check($stage && ($stage['pengumpulan']??true),'Tahap pengumpulan tidak tersedia. Muat ulang halaman.');
            $this->check(!empty($stage['deadline']) && now()->lte($stage['deadline']) && (empty($stage['dibuka_pada']) || now()->gte($stage['dibuka_pada'])),'Pengumpulan hanya dapat dibatalkan selama periode awal masih terbuka.');
            $student=$praktikum->daftarPraktikan()->where('user_id',auth()->id())->firstOrFail();
            $submission=TaPengumpulan::where('tugas_akhir_id',$ta->id)->where('praktikan_id',$student->id)->where('tahap_id',$stage['id'])->lockForUpdate()->firstOrFail();
            $this->check(hash_equals($submission->token(),$data['submission_token']),'Pengumpulan telah berubah. Muat ulang halaman.');
            $this->check($submission->status==='dikumpulkan' && $submission->entries()->count()===1,'Pengumpulan revisi atau pengumpulan yang sudah ditinjau tidak dapat dibatalkan.');
            $grade=TaNilai::where('tugas_akhir_id',$ta->id)->where('praktikan_id',$student->id)->lockForUpdate()->first();
            $this->check(!collect($ta->componentsFor($stage['id']))->contains(fn($c)=>isset($grade?->skor[$c['id']]) || isset($grade?->initialScores()[$c['id']]) || isset($grade?->revisionScores()[$c['id']])),'Pengumpulan yang sudah dinilai tidak dapat dibatalkan.');
            $history=$submission->riwayat;
            foreach($history as &$entry) if(($entry['jenis']??null)==='pengumpulan' && empty($entry['dibatalkan_pada'])) $entry['dibatalkan_pada']=now()->toIso8601String();
            unset($entry);
            $history[]=['jenis'=>'pembatalan','waktu'=>now()->toIso8601String(),'oleh'=>auth()->user()->name];
            $submission->update(['status'=>'dibatalkan','riwayat'=>$history]);
            $this->invalidateGrade($ta,$student->id,'pembatalan_pengumpulan',$stage['id']);
        });
        return back()->with('success','Pengumpulan dibatalkan. Perbaiki berkas atau tautan, lalu kumpulkan kembali sebelum tenggat.');
    }

    public function review(Request $request)
    {
        [$praktikum, $role] = $this->context($request);
        abort_unless($role === 'asprak', 403);
        $data = $request->validate(['submission_id' => 'required|uuid', 'catatan' => 'required|string|max:500', 'deadline_revisi' => 'required|date|after:now']);
        DB::transaction(function () use ($praktikum, $data) {
            $ta = $this->task($praktikum, true);
            $submission = TaPengumpulan::where('tugas_akhir_id', $ta->id)->lockForUpdate()->findOrFail($data['submission_id']);
            $stage = collect($ta->tahap)->firstWhere('id', $submission->tahap_id);
            $this->check((bool)$stage,'Tahap pengumpulan sudah tidak tersedia. Muat ulang halaman.');
            $this->check(($stage['revisi_aktif']??false) && !empty($stage['revisi_deadline']) && now()->lt($stage['revisi_deadline']), 'Koordinator belum membuka revisi atau tenggat revisi sudah berakhir.');
            $this->check(\Illuminate\Support\Carbon::parse($data['deadline_revisi'])->lte($stage['revisi_deadline']), 'Tenggat tidak boleh melebihi tenggat revisi dari koordinator.');
            $this->check($submission->status === 'dikumpulkan', 'Pengumpulan sudah ditinjau. Muat ulang halaman.');
            $history = $submission->riwayat;
            $history[] = ['jenis' => 'perbaikan', 'waktu' => now()->toIso8601String(), 'oleh' => auth()->user()->name, 'catatan' => $data['catatan'], 'deadline' => $data['deadline_revisi']];
            $submission->update(['status' => 'perlu_perbaikan', 'catatan' => $data['catatan'], 'deadline_revisi' => $data['deadline_revisi'], 'riwayat' => $history]);
            // Reopening a submission invalidates publication until the score is reviewed again.
            $this->invalidateGrade($ta, $submission->praktikan_id, 'permintaan_perbaikan', $submission->tahap_id);
            $student = DaftarPraktikan::findOrFail($submission->praktikan_id);
            app(NotifikasiService::class)->kirim($student->user_id, 'Perbaikan Tugas Akhir', $data['catatan'].' Tenggat: '.$data['deadline_revisi']);
        });
        return back()->with('success', 'Permintaan perbaikan telah dikirim.');
    }

    private function gradeRequest(Request $request, array $payload): Request
    {
        $child=$request->duplicate(null,$payload);
        // JSON requests read from a separate input bag, not the form request bag.
        if ($request->isJson()) $child->setJson(new \Symfony\Component\HttpFoundation\InputBag($payload));
        return $child;
    }

    public function saveRecap(Request $request)
    {
        [, $role]=$this->context($request);
        abort_unless($role==='asprak',403);
        $data=$request->validate(['students'=>'required|array|min:1','students.*'=>'required|array','students.*.grade'=>'sometimes|array','students.*.attendance'=>'sometimes|array']);
        DB::transaction(function () use ($request,$data) {
            foreach ($data['students'] as $studentId=>$row) {
                $this->check(isset($row['attendance']) || isset($row['grade']), 'Tidak ada perubahan absensi atau nilai yang dikirim.');
                if (isset($row['attendance'])) $this->attendance($this->gradeRequest($request,['praktikan_id'=>$studentId]+$row['attendance']));
                if (isset($row['grade'])) $this->grade($this->gradeRequest($request,['praktikan_id'=>$studentId]+$row['grade']));
            }
        });
        return response()->json(['message'=>'Absensi dan nilai tugas akhir berhasil disimpan.']);
    }

    public function gradeBatch(Request $request)
    {
        [, $role]=$this->context($request);
        abort_unless($role==='asprak',403);
        $data=$request->validate([
            'tahap_id'=>'required|uuid','students'=>'required|array|min:1',
            'students.*.version'=>'required|integer|min:0','students.*.skor'=>'required|array',
            'students.*.skor.*'=>'nullable|numeric|min:0|max:100',
            'students.*.revisi'=>'nullable|array','students.*.revisi.*'=>'nullable|numeric|min:0|max:100',
        ]);
        DB::transaction(function () use ($request,$data) {
            foreach ($data['students'] as $studentId=>$row) {
                $row['revisi']=array_filter($row['revisi'] ?? [],fn($score)=>$score!==null && $score!=='');
                $child=$this->gradeRequest($request,['praktikan_id'=>$studentId,'tahap_id'=>$data['tahap_id']]+$row);
                $this->grade($child);
            }
        });
        return back()->with('success','Nilai tahap berhasil disimpan.');
    }

    public function grade(Request $request)
    {
        [$praktikum, $role] = $this->context($request);
        abort_unless($role === 'asprak', 403);
        $data = $request->validate(['praktikan_id' => 'required|uuid', 'version' => 'required|integer|min:0', 'tahap_id'=>'nullable|uuid', 'skor' => 'required|array', 'skor.*' => 'nullable|numeric|min:0|max:100', 'revisi'=>'nullable|array', 'revisi.*'=>'nullable|numeric|min:0|max:100', 'kosongkan'=>'nullable|array', 'kosongkan.*'=>'accepted']);
        DB::transaction(function () use ($praktikum, $data) {
            $ta = $this->task($praktikum, true);
            $this->check($ta->published, 'Tugas akhir belum dipublikasikan.');
            $praktikum->daftarPraktikan()->findOrFail($data['praktikan_id']);
            $components = !empty($data['tahap_id']) ? $ta->componentsFor($data['tahap_id']) : $ta->komponen;
            if(!empty($data['tahap_id'])) $this->check(collect($ta->tahap)->contains('id',$data['tahap_id']),'Tahap penilaian telah berubah. Muat ulang halaman sebelum menyimpan.');
            $this->check(!empty($components), 'Tahap ini tidak memiliki parameter penilaian.');
            $keys = collect($components)->pluck('id')->sort()->values()->all();
            $submittedKeys = collect(array_keys($data['skor']))->sort()->values()->all();
            $this->check($keys === $submittedKeys, 'Tahap atau kolom penilaian telah berubah. Muat ulang halaman sebelum menyimpan.');
            $clear = array_keys($data['kosongkan'] ?? []);
            $this->check(!array_diff($clear, $keys), 'Parameter yang dikosongkan tidak sesuai.');
            if (!empty($data['tahap_id'])) $this->check(!in_array(null, $data['skor'], true), 'Isi seluruh parameter tahap ini.');
            $data['skor'] = array_filter($data['skor'], fn($score) => $score !== null);
            $hasRevision=collect($data['revisi'] ?? [])->contains(fn($score)=>$score!==null && $score!=='');
            $this->check(count($data['skor']) > 0 || count($clear) > 0 || $hasRevision, 'Isi minimal satu nilai atau pilih Kosongkan nilai.');
            $grade = TaNilai::where('tugas_akhir_id', $ta->id)->where('praktikan_id', $data['praktikan_id'])->lockForUpdate()->first();
            $this->check((int) ($grade?->version ?? 0) === (int) $data['version'], 'Data penilaian telah diperbarui. Muat ulang halaman sebelum menyimpan.');
            $initial = array_replace($grade?->initialScores() ?? [], $data['skor']);
            $revisions = $grade?->revisionScores() ?? [];
            foreach ($data['revisi'] ?? [] as $id => $score) {
                $this->check(in_array($id, $keys, true), 'Parameter revisi tidak sesuai tahap.');
                if ($score === null) continue;
                $component = collect($components)->firstWhere('id', $id);
                $stageId = $component['tahap_id'] ?? $ta->tahap[0]['id'];
                $submission = TaPengumpulan::where('tugas_akhir_id', $ta->id)->where('praktikan_id', $data['praktikan_id'])->where('tahap_id', $stageId)->first();
                $this->check($submission && $submission->entries()->count() > 1, 'Nilai revisi hanya dapat diisi setelah mahasiswa mengumpulkan revisi.');
                $revisions[$id] = $score;
            }
            foreach ($clear as $id) { unset($initial[$id], $revisions[$id]); }
            $effective = $initial;
            foreach ($ta->komponen as $component) {
                $id = $component['id'];
                if (!array_key_exists($id, $revisions)) continue;
                $stage = collect($ta->tahap)->firstWhere('id', $component['tahap_id'] ?? $ta->tahap[0]['id']);
                $a = (float)($initial[$id] ?? 0); $r = (float)$revisions[$id];
                $effective[$id] = match ($stage['revisi_metode'] ?? 'rata_rata') {
                    'ganti' => $r, 'tertinggi' => max($a, $r),
                    'berbobot' => $a * (float)($stage['revisi_bobot_awal'] ?? 50)/100 + $r * (100-(float)($stage['revisi_bobot_awal'] ?? 50))/100,
                    default => ($a+$r)/2,
                };
            }
            $history = $grade?->riwayat ?? [];
            $pending = data_get(collect($history)->last(), 'belum_ditinjau', []);
            foreach ($ta->tahap as $stage) {
                if (!array_key_exists('revisi_aktif', $stage)) continue;
                $submission = TaPengumpulan::where('tugas_akhir_id',$ta->id)->where('praktikan_id',$data['praktikan_id'])->where('tahap_id',$stage['id'])->first();
                if ($submission && $submission->entries()->count() > 1 && !collect($ta->componentsFor($stage['id']))->every(fn($c) => array_key_exists($c['id'], $revisions))) $pending[] = $stage['id'];
            }
            $pending = array_values(array_intersect(array_unique($pending),collect($ta->tahap)->pluck('id')->all()));
            $pending = array_values(array_filter($pending, function ($id) use ($ta, $data, $revisions) {
                $stageKeys = collect($ta->componentsFor($id))->pluck('id');
                if (!$stageKeys->every(fn($key) => array_key_exists($key, $data['skor']))) return true;
                $stage = collect($ta->tahap)->firstWhere('id', $id);
                $submission = TaPengumpulan::where('tugas_akhir_id',$ta->id)->where('praktikan_id',$data['praktikan_id'])->where('tahap_id',$id)->first();
                if ($stage && array_key_exists('revisi_aktif', $stage) && $submission && $submission->entries()->count() > 1) {
                    return !$stageKeys->every(fn($key) => array_key_exists($key, $revisions));
                }
                return false;
            }));
            $history[] = ['waktu' => now()->toIso8601String(), 'oleh' => auth()->user()->name, 'jenis' => 'penilaian', 'tahap_id'=>$data['tahap_id'] ?? null, 'belum_ditinjau'=>$pending, 'awal'=>$initial, 'revisi'=>$revisions, 'sebelum' => $grade?->skor, 'sesudah' => $effective];
            TaNilai::updateOrCreate(['tugas_akhir_id' => $ta->id, 'praktikan_id' => $data['praktikan_id']], ['skor' => $effective, 'riwayat' => $history, 'version' => ($grade?->version ?? 0) + 1, 'disetujui_koor' => false, 'disetujui_dosen' => false]);
            $this->invalidatePublication($praktikum->id);
        });
        return back()->with('success', 'Nilai tugas akhir berhasil disimpan.');
    }

    public function attendance(Request $request)
    {
        [$praktikum, $role] = $this->context($request);
        abort_unless($role === 'asprak', 403);
        $data = $request->validate([
            'praktikan_id' => 'required|uuid', 'version' => 'required|integer|min:0',
            'status' => 'required|in:hadir,terlambat,tidak_hadir,alpa', 'keterangan' => 'nullable|string|max:500',
        ]);
        DB::transaction(function () use ($praktikum, $data) {
            $ta = $this->task($praktikum, true);
            $this->check($ta->published, 'Tugas akhir belum dipublikasikan.');
            $praktikum->daftarPraktikan()->findOrFail($data['praktikan_id']);
            $attendance = TaAbsensi::where('tugas_akhir_id', $ta->id)->where('praktikan_id', $data['praktikan_id'])->lockForUpdate()->first();
            $this->check((int) ($attendance?->version ?? 0) === (int) $data['version'], 'Absensi telah diperbarui. Muat ulang halaman sebelum menyimpan.');
            $history = $attendance?->riwayat ?? [];
            $history[] = ['waktu' => now()->toIso8601String(), 'oleh' => auth()->user()->name, 'sebelum' => $attendance ? ['status' => $attendance->status, 'keterangan' => $attendance->keterangan] : null, 'sesudah' => ['status' => $data['status'], 'keterangan' => $data['keterangan'] ?? null]];
            TaAbsensi::updateOrCreate(['tugas_akhir_id' => $ta->id, 'praktikan_id' => $data['praktikan_id']], ['status' => $data['status'], 'keterangan' => $data['keterangan'] ?? null, 'riwayat' => $history, 'version' => ($attendance?->version ?? 0) + 1]);
        });
        return back()->with('success', 'Absensi tugas akhir berhasil disimpan.');
    }

    public function weights(Request $request)
    {
        [$praktikum, $role] = $this->context($request);
        abort_unless($role === 'koor', 403);
        $data = $request->validate(['version'=>'required|integer|min:1', 'bobot'=>'required|array', 'bobot.*'=>'required|integer|gt:0|max:100'], [
            'bobot.*.integer'=>'Bobot setiap parameter harus berupa bilangan bulat.',
            'bobot.*.gt'=>'Bobot setiap parameter harus lebih dari 0%.',
            'bobot.*.max'=>'Bobot setiap parameter maksimal 100%.',
        ]);
        DB::transaction(function () use ($praktikum, $data) {
            $ta = $this->task($praktikum, true);
            $this->check((int)$ta->version === (int)$data['version'], 'Pengaturan telah berubah. Muat ulang halaman.');
            $this->check(collect($data['bobot'])->keys()->sort()->values()->all() === collect($ta->komponen)->pluck('id')->sort()->values()->all(), 'Isi bobot seluruh parameter.');
            $this->check(abs(array_sum($data['bobot'])-100) < .001, 'Total bobot harus 100%.');
            $components = $ta->komponen;
            foreach ($components as &$c) $c['bobot'] = (int)$data['bobot'][$c['id']];
            unset($c);
            if ($components == $ta->komponen) return;
            $this->check(!TaNilai::where('tugas_akhir_id',$ta->id)->where(fn($q)=>$q->where('disetujui_koor',true)->orWhere('disetujui_dosen',true))->exists(), 'Batalkan persetujuan atau publikasi nilai sebelum mengubah bobot tugas akhir.');
            $this->invalidatePublication($praktikum->id);
            $history = $ta->riwayat ?? [];
            $history[] = ['waktu'=>now()->toIso8601String(), 'oleh'=>auth()->user()->name, 'ketentuan'=>$ta->ketentuan, 'tahap'=>$ta->tahap, 'komponen'=>$ta->komponen];
            $ta->update(['komponen'=>$components, 'riwayat'=>$history, 'version'=>$ta->version+1]);
            foreach (TaNilai::where('tugas_akhir_id',$ta->id)->lockForUpdate()->get() as $grade) {
                $grade->update(['disetujui_koor'=>false, 'disetujui_dosen'=>false, 'version'=>$grade->version+1]);
            }
        });
        return back()->with('success','Bobot tugas akhir berhasil disimpan.');
    }

    public function approve(Request $request)
    {
        [$praktikum, $role] = $this->context($request);
        abort_unless(in_array($role, ['koor', 'dosen']), 403);
        $data = $request->validate(['versions' => 'required|array', 'versions.*' => 'required|integer|min:1']);
        DB::transaction(function () use ($praktikum, $role, $data) {
            $ta = $this->task($praktikum, true);
            $grades = TaNilai::where('tugas_akhir_id', $ta->id)->whereIn('praktikan_id',$praktikum->daftarPraktikan()->select('id'))->lockForUpdate()->get();
            $this->check($ta->validWeights(), 'Koordinator perlu melengkapi bobot bilangan bulat dengan total 100%.');
            $this->check($grades->isNotEmpty(), 'Belum ada nilai untuk disetujui.');
            $this->check(!TaPengumpulan::where('tugas_akhir_id', $ta->id)->where('status', 'perlu_perbaikan')->where('deadline_revisi', '>', now())->exists(), 'Masih ada pengumpulan yang perlu diperbaiki sebelum tenggat.');
            foreach ($grades as $grade) {
                $lastEvent = collect($grade->riwayat)->last();
                $this->check(collect($ta->komponen)->every(fn($c) => array_key_exists($c['id'], $grade->skor)), 'Seluruh parameter harus dinilai sebelum persetujuan.');
                $pending = $lastEvent['belum_ditinjau'] ?? [];
                foreach ($pending as $stageId) {
                    $submission = TaPengumpulan::where('tugas_akhir_id',$ta->id)->where('praktikan_id',$grade->praktikan_id)->where('tahap_id',$stageId)->first();
                    $this->check($submission && $submission->status === 'perlu_perbaikan' && $submission->deadline_revisi && now()->gt($submission->deadline_revisi), 'Asisten perlu meninjau nilai tahap setelah pengumpulan atau perbaikan terbaru.');
                }
                $this->check(count($pending) > 0 || ($lastEvent['jenis'] ?? 'penilaian') === 'penilaian', 'Asisten perlu meninjau dan menyimpan ulang nilai setelah pengumpulan atau perbaikan terbaru.');
                $this->check((int) ($data['versions'][$grade->id] ?? 0) === (int) $grade->version, 'Nilai telah berubah. Muat ulang dan tinjau nilai sebelum menyetujui.');
                $wasPublished = $grade->disetujui_koor && $grade->disetujui_dosen;
                $grade->update([$role === 'koor' ? 'disetujui_koor' : 'disetujui_dosen' => true]);
                if (!$wasPublished && $grade->disetujui_koor && $grade->disetujui_dosen) {
                    $student = DaftarPraktikan::find($grade->praktikan_id);
                    if ($student) DB::afterCommit(fn()=>app(NotifikasiService::class)->kirim($student->user_id, 'Nilai Tugas Akhir', 'Nilai tugas akhir '.$praktikum->nama.' telah dipublikasikan.'));
                }
            }
        });
        return back()->with('success', 'Nilai disetujui. Nilai tampil setelah persetujuan koordinator dan dosen.');
    }

    public function fromPracticumPage(Request $request, Praktikum $praktikum, string $role, string $action): void
    {
        $ta = TugasAkhir::where('praktikum_id',$praktikum->id)->where('published',true)->first();
        if (!$ta) return;
        $data = $action === 'weights'
            ? ['version'=>$request->input('ta_version'),'bobot'=>$request->input('ta_bobot',[])]
            : ['versions'=>$request->input('ta_versions',[])];
        $bridge = Request::create($request->url(),'POST',$data);
        $route = new \Illuminate\Routing\Route('POST','/',fn()=>null);
        $route->defaults('taRole',$role)->defaults('praktikum',$praktikum->id);
        $route->bind($bridge);
        $bridge->setRouteResolver(fn()=>$route);
        if ($action === 'weights') $this->weights($bridge);
        else $this->approve($bridge);
    }

    public function destroy(Request $request)
    {
        [$praktikum, $role] = $this->context($request);
        abort_unless($role === 'koor',403);
        $data=$request->validate(['version'=>'required|integer|min:1']);
        DB::transaction(function () use ($praktikum,$data) {
            $ta=$this->task($praktikum,true);
            $this->check((int)$ta->version === (int)$data['version'], 'Data telah berubah. Muat ulang sebelum menghapus.');
            foreach ([TaPengumpulan::class,TaNilai::class,TaAbsensi::class] as $model) $this->check(!$model::where('tugas_akhir_id',$ta->id)->exists(), 'Tugas akhir tidak dapat dihapus karena sudah memiliki pengumpulan, nilai, atau absensi.');
            $ta->delete();
        });
        return back()->with('success','Tugas akhir berhasil dihapus.');
    }

    public function download(Request $request)
    {
        [$praktikum, $role] = $this->context($request);
        $ta = $this->task($praktikum);
        abort_unless($ta->published || $role === 'koor', 403);
        $path = $request->query('path');
        $files = collect($ta->lampiran ?? []);
        foreach ($ta->tahap as $stage) $files = $files->merge($stage['lampiran'] ?? []);
        $query = TaPengumpulan::where('tugas_akhir_id', $ta->id);
        if ($role === 'mahasiswa') $query->whereIn('praktikan_id', $praktikum->daftarPraktikan()->where('user_id', auth()->id())->pluck('id'));
        foreach ($query->get() as $submission) foreach ($submission->riwayat as $revision) $files = $files->merge($revision['files'] ?? []);
        $file = $files->firstWhere('path', $path);
        abort_unless($file && Storage::disk('local')->exists($file['path']), 404);
        return \Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::response(
            Storage::disk('local')->get($file['path']),$file['nama'],$request->boolean('download'),$request->fullUrlWithQuery(['download'=>1])
        );
    }
}
