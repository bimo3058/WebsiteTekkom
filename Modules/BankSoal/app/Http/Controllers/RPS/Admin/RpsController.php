<?php

namespace Modules\BankSoal\Http\Controllers\RPS\Admin;

use Illuminate\Routing\Controller;

/**
 * [Admin - RPS] Controller untuk manajemen RPS tingkat Admin
 * 
 * Role: Admin
 * Fitur: RPS (Rencana Pembelajaran Semester)
 * 
 * Admin dapat melihat, mengelola, dan menghapus semua RPS di sistem.
 */
class RpsController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        return view('banksoal::pages.rps.admin.index');
    }
}
