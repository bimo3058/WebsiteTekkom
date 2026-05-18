<?php

namespace Modules\BankSoal\Http\Controllers\BS\GPM;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Models\Parameter;

class ParameterController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $parameters = Parameter::orderBy('id', 'desc')->get();

        // Return JSON for AJAX requests (legacy support)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $parameters
            ]);
        }

        // Return view for page load
        $skorMinimum = DB::table('bs_pengaturan')->where('kunci', 'standar_skor_minimum')->value('nilai') ?? 60;
        return view('banksoal::pages.gpm.parameter.index', compact('skorMinimum', 'parameters'));
    }

    /**
     * Show form for creating a new parameter.
     */
    public function create()
    {
        return view('banksoal::pages.gpm.parameter.create');
    }

    /**
     * Show form for editing an existing parameter.
     */
    public function edit(string $id)
    {
        $id = (int) $id;
        $parameter = Parameter::findOrFail($id);
        return view('banksoal::pages.gpm.parameter.edit', compact('parameter'));
    }

    public function updateSkor(Request $request)
    {
        $request->validate([
            'skor' => 'required|integer|min:0|max:100',
        ]);

        try {
            DB::table('bs_pengaturan')->updateOrInsert(
                ['kunci' => 'standar_skor_minimum'],
                ['nilai' => $request->skor, 'updated_at' => now()]
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Standar skor terendah berhasil diperbarui'
                ]);
            }

            return back()->with('success', 'Standar skor terendah berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui standar skor: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal memperbarui standar skor: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|in:rps,soal',
            'aspek' => 'required|string|max:255',
            'bobot' => 'required|integer|min:1|max:100',
        ]);

        try {
            $parameter = Parameter::create($validated);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Parameter berhasil ditambahkan',
                    'data' => $parameter
                ], 201);
            }

            return redirect()->route('banksoal.soal.gpm.parameter.index')
                ->with('success', 'Parameter berhasil ditambahkan');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan Parameter: ' . $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('error', 'Gagal menambahkan Parameter: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $id = (int) $id;

        try {
            $parameter = Parameter::findOrFail($id);
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $parameter
                ]);
            }
            return view('banksoal::pages.gpm.parameter.show', compact('parameter'));
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter tidak ditemukan'
                ], 404);
            }
            return redirect()->route('banksoal.soal.gpm.parameter.index')->with('error', 'Parameter tidak ditemukan');
        }
    }

    public function update(Request $request, string $id)
    {
        $id = (int) $id;

        $validated = $request->validate([
            'jenis' => 'required|string|in:rps,soal',
            'aspek' => 'required|string|max:255',
            'bobot' => 'required|integer|min:1|max:100',
        ]);

        try {
            $parameter = Parameter::findOrFail($id);
            $parameter->update($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Parameter berhasil diperbarui',
                    'data' => $parameter
                ]);
            }

            return redirect()->route('banksoal.soal.gpm.parameter.index')
                ->with('success', 'Parameter berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui Parameter: ' . $e->getMessage()
                ], 500);
            }
            return back()->withInput()->with('error', 'Gagal memperbarui Parameter: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $id = (int) $id;

        try {
            $parameter = Parameter::findOrFail($id);
            
            if ($parameter->hasilReview()->exists()) {
                $message = 'Parameter tidak dapat dihapus karena sudah digunakan dalam review RPS.';
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
                return back()->with('error', $message);
            }

            $parameter->delete();
            
            $message = 'Parameter berhasil dihapus';
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            return redirect()->route('banksoal.soal.gpm.parameter.index')->with('success', $message);
        } catch (\Exception $e) {
            $message = 'Gagal menghapus Parameter: ' . $e->getMessage();
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            return back()->with('error', $message);
        }
    }
}
