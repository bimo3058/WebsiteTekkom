<?php

namespace Modules\BankSoal\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\BankSoal\Models\PeriodeUjian;
use Modules\BankSoal\Models\PendaftarUjian;

class PeriodeTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;

    // Reset halaman saat mencari
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    // Reset halaman saat mengubah perPage
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Pastikan status otomatis terupdate sebelum load tabel (sama seperti di index sebelumnya)
        $this->updateStatusOtomatis();

        $query = PeriodeUjian::query();

        if (!empty($this->search)) {
            $query->where('nama_periode', 'ilike', '%' . $this->search . '%')
                  ->orWhere('status', 'ilike', '%' . $this->search . '%');
        }

        $periodes = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('banksoal::livewire.periode-table', [
            'periodes' => $periodes
        ]);
    }

    private function updateStatusOtomatis()
    {
        $periodes = PeriodeUjian::all();
        $now = now();
        foreach ($periodes as $p) {
            $tglMulai = \Carbon\Carbon::parse($p->tanggal_mulai)->startOfDay();
            $tglSelesai = \Carbon\Carbon::parse($p->tanggal_selesai)->endOfDay();
            
            if ($p->status !== 'selesai') {
                if ($now->between($tglMulai, $tglSelesai)) {
                    if ($p->status !== 'aktif') {
                        $p->update(['status' => 'aktif']);
                    }
                } elseif ($now->gt($tglSelesai)) {
                    if ($p->status !== 'selesai' && $p->status !== 'draft') {
                        // Atau bisa dibiarkan tidak ganti status selesai karena SELESAI itu force close, 
                        // tapi logikanya kemarin statusnya otomatis aktif atau draft
                    }
                } elseif ($now->lt($tglMulai)) {
                    if ($p->status !== 'draft') {
                        $p->update(['status' => 'draft']);
                    }
                }
            }
        }
    }

    public function closePendaftaran($id)
    {
        $periode = PeriodeUjian::findOrFail($id);
        
        if ($periode->pendaftaran_terbuka) {
            $periode->update([
                'pendaftaran_ditutup_paksa' => true,
            ]);
            
            session()->flash('success', 'Pendaftaran periode ' . $periode->nama_periode . ' berhasil ditutup darurat.');
        }
    }

    public function deletePeriode($id)
    {
        $hasPendaftar = PendaftarUjian::where('periode_ujian_id', $id)->exists();
        
        if ($hasPendaftar) {
            session()->flash('error', 'Gagal menghapus periode. Sudah ada mahasiswa yang mendaftar.');
            return;
        }

        $periode = PeriodeUjian::findOrFail($id);
        $periode->delete();
        
        session()->flash('success', 'Periode ujian berhasil dihapus.');
    }
}
