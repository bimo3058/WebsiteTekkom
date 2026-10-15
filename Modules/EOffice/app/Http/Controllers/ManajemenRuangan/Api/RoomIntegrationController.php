<?php

namespace Modules\EOffice\Http\Controllers\ManajemenRuangan\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EOffice\Services\RoomBookingService;

class RoomIntegrationController extends Controller
{
    protected RoomBookingService $bookingService;

    public function __construct(RoomBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Endpoint API untuk mengecek ketersediaan seluruh ruangan.
     * Digunakan oleh Dropdown di Modul KP, Capstone, dan Kanvas Modul Praktikum.
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kapasitas' => 'nullable|integer',
            'kategori' => 'nullable|string',
            'bypass_h_min' => 'nullable|boolean',
        ]);

        try {
            $bypassHMin = $request->has('bypass_h_min')
                ? filter_var($request->input('bypass_h_min'), FILTER_VALIDATE_BOOLEAN)
                : true;

            $rooms = $this->bookingService->getAvailableRooms(
                $request->input('tanggal'),
                $request->input('jam_mulai'),
                $request->input('jam_selesai'),
                $request->input('kapasitas'),
                $request->input('kategori'),
                $bypassHMin
            );

            return response()->json([
                'success' => true,
                'data' => $rooms,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 422);
        }
    }

    /**
     * Endpoint API untuk pemesanan ruangan dari modul lain (KP, Capstone, Praktikum).
     */
    public function bookRoom(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:eo_mr_ruangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'tujuan' => 'required|string|max:255',
            'source' => 'required|string',
            'source_id' => 'nullable|integer',
            'nomor_telepon' => 'nullable|string|max:30',
        ]);

        try {
            $peminjaman = $this->bookingService->bookRoom([
                'ruangan_id' => $request->input('ruangan_id'),
                'tanggal_pinjam' => $request->input('tanggal'),
                'jam_mulai' => $request->input('jam_mulai'),
                'jam_selesai' => $request->input('jam_selesai'),
                'tujuan' => $request->input('tujuan'),
                'source' => $request->input('source'),
                'source_id' => $request->input('source_id'),
                'nomor_telepon' => $request->input('nomor_telepon'),
                'user_id' => $request->input('user_id') ?? auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ruangan berhasil dialokasikan.',
                'data' => $peminjaman,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Endpoint API untuk membatalkan alokasi ruangan dari modul lain.
     */
    public function cancelBooking(Request $request)
    {
        $request->validate([
            'source' => 'required|string',
            'source_id' => 'required',
            'alasan' => 'nullable|string',
        ]);

        $cancelled = $this->bookingService->cancelBooking(
            $request->input('source'),
            $request->input('source_id'),
            $request->input('alasan')
        );

        if ($cancelled) {
            return response()->json([
                'success' => true,
                'message' => 'Peminjaman ruangan berhasil dibatalkan.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Data peminjaman tidak ditemukan atau sudah dibatalkan.',
        ], 404);
    }
}
