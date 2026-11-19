@props(['praktikum'])
<div x-data="{editData:{status:@js($praktikum->is_active?'nonaktif':'aktif')},confirmOpen:false,confirmed:false,submitting:false,
    get statusMessage(){return this.editData.status==='nonaktif'?'Nonaktifkan praktikum ini? Mahasiswa hanya dapat membaca arsip. Penugasan dosen, koordinator, dan asisten pada praktikum ini akan dicabut. Data kegiatan dan berkas tetap disimpan. Role akun pada praktikum atau modul lain tidak ikut dicabut.':'Aktifkan kembali praktikum ini? Penugasan lama tidak dipulihkan otomatis. Admin perlu menetapkan dosen dan CERC perlu menetapkan koordinator serta asisten.';}
}">
    <form method="POST" action="{{ route('eoffice.manprak.admin.praktikum.toggle-active',$praktikum->id) }}" x-ref="statusForm" @submit="if(!confirmed){$event.preventDefault();confirmOpen=true;}else{submitting=true;}">
        @csrf @method('PATCH')
        <input type="hidden" name="previous_status" value="{{ $praktikum->status }}">
        <input type="hidden" name="status_confirmed" :value="confirmed?'1':'0'">
        <button type="submit" @class(['destructive'=>$praktikum->is_active]) :disabled="submitting"><x-eoffice::manajemen-praktikum.ui.icon name="{{ $praktikum->is_active?'lock':'check' }}" />{{ $praktikum->is_active?'Nonaktifkan Praktikum':'Aktifkan Praktikum' }}</button>
    </form>
    @include('eoffice::manajemen-praktikum.admin.partials.status-confirmation')
</div>
