<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        // Ambil langsung dari tabel lecturers JOIN users
        $query = Lecturer::with('user')->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            )->orWhere('employee_number', 'like', "%{$search}%");
        }

        $dosens = $query->paginate(15)->withQueryString();

        return view('eoffice::manajemen-praktikum.admin.dosen', compact('dosens'));
    }
}