<?php

namespace Modules\EOffice\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Praktikum;

class PraktikanImportService
{
    private function columnPairs(array $row): array
    {
        $nims=[];$names=[];
        foreach($row as $column=>$value){
            $header=$this->normalizedName(ltrim((string)$value,"\xEF\xBB\xBF"));
            $header=trim(preg_replace('/[\s_\-(]*\d+[\s)]*$/u','',$header));
            if(in_array($header,['nim','nim mahasiswa','nomor induk mahasiswa'],true))$nims[]=$column;
            if(in_array($header,['nama','nama mahasiswa','nama lengkap'],true))$names[]=$column;
        }
        if(!$nims || !$names)return [];
        if(count($nims)!==count($names))throw \Illuminate\Validation\ValidationException::withMessages(['file'=>'Setiap kolom NIM harus memiliki pasangan kolom Nama. Periksa header file.']);
        return array_map(fn($nim,$name)=>['nim'=>$nim,'name'=>$name],$nims,$names);
    }

    public function normalizedName(string $name): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $name)));
    }

    public function import(string $practicumId, array $rows): array
    {
        $headerIndex = null;$pairs=[];
        foreach ($rows as $index => $row) {
            $pairs=$this->columnPairs($row);
            if ($pairs) {
                $headerIndex=$index;
                break;
            }
        }
        if ($headerIndex === null) {
            throw \Illuminate\Validation\ValidationException::withMessages(['file' => 'Kolom NIM dan Nama harus tersedia pada baris header yang sama.']);
        }

        $records=[];
        foreach($rows as $index=>$row){
            if($index<=$headerIndex)continue;
            $headers=$this->columnPairs($row);
            if($headers){$pairs=$headers;continue;}
            foreach($pairs as $pair){
                $nim=trim((string)($row[$pair['nim']]??''));$name=trim((string)($row[$pair['name']]??''));
                if($nim==='' && $name==='')continue;
                $records[]=['row'=>$index+1,'nim'=>$nim,'name'=>$name,'column'=>\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($pair['nim']+1)];
            }
        }
        return DB::transaction(function () use ($practicumId, $records) {
            $practicum = Praktikum::whereKey($practicumId)->lockForUpdate()->firstOrFail();
            abort_if($practicum->status !== 'aktif' || $practicum->is_active === false, 403, 'Praktikum tidak aktif.');
            abort_if(app(PracticumGroupingLock::class)->isLocked($practicumId), 403, 'Aktivitas praktikum sudah tercatat. Impor tidak diperbolehkan.');
            $result = ['added' => 0, 'duplicate' => 0, 'issues' => []];
            $seen = [];
            foreach ($records as $record) {
                $index=$record['row']-1;$nim=$record['nim'];$name=$record['name'];
                $issue = null;
                if ($nim === '' || $name === '') $issue = 'NIM dan nama wajib diisi.';
                elseif (! preg_match('/^\d+$/D', $nim)) $issue = 'NIM tidak valid. Gunakan format teks agar angka NIM tetap utuh.';
                if ($issue) {
                    $result['issues'][] = ['row' => $index + 1, 'column'=>$record['column'], 'nim' => $nim, 'reason' => $issue];
                    continue;
                }
                $matches = User::whereHas('student', fn ($q) => $q->where('student_number', $nim))->get();
                if ($matches->count() !== 1) $issue = $matches->isEmpty() ? 'NIM belum terdaftar di SITKOM.' : 'NIM terhubung ke lebih dari satu akun. Hubungi pengelola.';
                elseif ($this->normalizedName($matches->first()->name) !== $this->normalizedName($name)) $issue = 'Nama tidak sesuai dengan akun pemilik NIM. Periksa kembali sebelum mengimpor ulang.';
                if ($issue) {
                    $result['issues'][] = ['row' => $index + 1, 'column'=>$record['column'], 'nim' => $nim, 'reason' => $issue];
                    continue;
                }
                $user = $matches->first();
                if (! \Modules\EOffice\Services\ManajemenPraktikum\ActiveStudent::eligible($user)) {
                    $result['issues'][] = ['row'=>$index+1, 'column'=>$record['column'], 'nim'=>$nim, 'reason'=>'Mahasiswa tidak berstatus aktif atau akunnya ditangguhkan.'];
                    continue;
                }
                if (isset($seen[$nim]) || DaftarPraktikan::where('praktikum_id', $practicumId)->where('user_id', $user->id)->exists()) {
                    $result['duplicate']++;
                    continue;
                }
                $seen[$nim] = true;
                DaftarPraktikan::create(['praktikum_id' => $practicumId, 'user_id' => $user->id, 'status' => 'terdaftar']);
                $result['added']++;
            }
            return $result;
        });
    }
}
