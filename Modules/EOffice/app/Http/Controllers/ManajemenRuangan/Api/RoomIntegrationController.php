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
}
