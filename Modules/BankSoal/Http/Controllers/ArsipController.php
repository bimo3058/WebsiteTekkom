<?php

namespace Modules\BankSoal\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Models\ArsipSoal;
use Modules\BankSoal\Models\PenarikanSoal;
use Modules\BankSoal\Models\PenarikanSoalArchived;
use Modules\BankSoal\Models\Shared\MataKuliah;

class ArsipController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'mk_id' => 'required|integer',
            'agenda' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'semester' => 'required|string',
            'soal_json' => 'required|string',
            'direct_archive' => 'nullable|boolean',
            'deskripsi' => 'nullable|string',
            'catatan_internal' => 'nullable|string',
        ]);

        $user = $request->user();
        $soalArray = json_decode($data['soal_json'], true) ?: [];
        $jumlah = count($soalArray);
        $total = array_reduce($soalArray, fn($sum, $s) => $sum + (float) ($s['bobot'] ?? 0), 0);

        return DB::transaction(function () use ($data, $user, $soalArray, $jumlah, $total, $request) {
            $penarikan = PenarikanSoal::create([
                'dosen_id' => $user->id,
                'mk_id' => $data['mk_id'],
                'nama_ekstraksi' => ($data['agenda'] ?? 'Ekstraksi'),
                'tipe_ujian' => ($data['agenda'] ?? null),
                'tahun_akademik' => $data['tahun_ajaran'],
                'semester' => $data['semester'],
                'soal_data' => json_encode(array_map(function ($s) {
                    return array_merge(['id' => $s['id'] ?? null, 'cpl' => $s['cpl'] ?? null, 'cpmk' => $s['cpmk'] ?? null, 'tipe_soal' => $s['tipe_soal'] ?? null], $s);
                }, $soalArray)),
                'jumlah_soal' => $jumlah,
                'total_bobot' => $total,
                'status' => $request->boolean('direct_archive') ? 'archived' : 'pending',
                'deskripsi' => $data['deskripsi'] ?? null,
                'catatan_internal' => $data['catatan_internal'] ?? null,
            ]);

            $archived = null;
            if ($request->boolean('direct_archive')) {
                $mk = MataKuliah::find($data['mk_id']);
                $arsip = ArsipSoal::create([
                    'mk_id' => $data['mk_id'],
                    'dosen_id' => $user->id,
                    'tahun_akademik' => $data['tahun_ajaran'],
                    'semester' => $data['semester'],
                    'nama_arsip' => trim(($mk->nama ?? 'Mata Kuliah') . ' - ' . ($data['agenda'] ?? '')),
                    'tipe_ujian' => $data['agenda'] ?? null,
                    'jumlah_soal' => $jumlah,
                    'total_bobot' => $total,
                    'soal_data' => $penarikan->soal_data,
                    'status' => 'final',
                    'deskripsi' => $data['deskripsi'] ?? null,
                    'catatan_internal' => $data['catatan_internal'] ?? null,
                ]);

                $archived = PenarikanSoalArchived::create([
                    'penarikan_id' => $penarikan->id,
                    'arsip_id' => $arsip->id,
                    'archived_by' => $user->id,
                ]);
            }

            return response()->json([
                'success' => true,
                'type' => $archived ? 'archived' : 'pending',
                'message' => $archived ? 'Soal berhasil diarsipkan.' : 'Soal berhasil dibuat penarikan.',
            ]);
        });
    }
}
