<x-eoffice::manajemen-praktikum.layout pageTitle="Kelola Koordinator & Asisten">
    <x-eoffice::manajemen-praktikum.ui.staffing-styles />
    @php
        $activeMembers=$anggota->reject(fn($a)=>$a->trashed());
        $formConfig=['roleModalOpen'=>$errors->any() && (string)old('praktikum_id')===(string)$praktikum->id,'students'=>$mahasiswaOptions->values(),'assignments'=>$staffingAssignments,'coordinatorId'=>$praktikum->koor_id ? (string)$praktikum->koor_id : '', 'role'=>old('role','asprak'),'userId'=>(string)old('user_id','')];
        $formConfig['userIds']=old('user_ids',[(string)old('user_id','')]);
    @endphp
    <div class="mp-staffing-page" x-data="practicumStaffingForm(@js($formConfig))" style="display:contents">
        <div class="mp-page-header"><div><h1 class="mp-page-title">{{ $praktikum->nama }}</h1><p class="mp-page-sub">Kelola Koordinator & Asisten · {{ $praktikum->semester }} {{ $praktikum->tahun_ajaran }}</p></div><a class="mp-btn secondary md" href="{{ route('eoffice.manprak.cerc.kelola-role.index') }}">Kembali</a></div>
        @if(session('success'))<div class="mp-alert success" role="status">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mp-alert error" role="alert">{{ session('error') }}</div>@endif
        @if($errors->any() && !old('praktikum_id'))<div class="mp-alert error" role="alert">{{ $errors->first() }}</div>@endif
        <section class="mp-card mp-settings-card"><div class="mp-settings-body"><div><h2 class="mp-staffing-section-title">Dosen Pengampu</h2><div class="mp-staffing-teachers">@forelse($praktikum->dosens as $dosen)<x-eoffice::manajemen-praktikum.ui.staff-person :user="$dosen" />@empty<span class="mp-badge error sm" style="align-self:flex-start;width:fit-content;">Belum ditunjuk</span>@endforelse</div></div></div></section>
        <section class="mp-card mp-settings-card">
            <div class="mp-card-header"><div><h2 class="mp-staffing-section-title">Anggota Terdaftar</h2><p class="mp-staffing-member-count">{{ $activeMembers->where('role','koor')->count() }} koordinator · {{ $activeMembers->where('role','asprak')->count() }} asisten aktif</p></div>
                <div class="mp-staffing-actions"><button type="button" class="mp-btn primary md" @click="roleModalOpen=true"><x-eoffice::manajemen-praktikum.ui.icon name="plus" />Tambah Role</button>
                    @if($activeMembers->isNotEmpty())<form method="POST" action="{{ route('eoffice.manprak.cerc.kelola-role.revokeAll',$praktikum->id) }}" x-data="{confirmOpen:false,confirmed:false,submitting:false}" x-ref="actionForm" @submit="if(submitting){$event.preventDefault();return}if(!confirmed){$event.preventDefault();confirmOpen=true}else{submitting=true}">@csrf<button class="mp-delete-button mp-btn destructive md" :disabled="submitting" aria-label="Cabut Semua Role" title="Cabut Semua Role"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button><x-eoffice::manajemen-praktikum.ui.schedule-confirmation title="Cabut Semua Role" message="Cabut seluruh role koordinator dan asisten pada praktikum ini? Penugasan pada praktikum lain tetap dipertahankan." /></form>@endif
                </div>
            </div>
            <div class="mp-data-scroll"><table class="mp-table mp-settings-table mp-staffing-table" style="min-width:700px">
                <thead><tr><th class="mp-th mp-settings-no">NO</th><th class="mp-th">NAMA MAHASISWA</th><th class="mp-th">ROLE</th><th class="mp-th">BERGABUNG</th><th class="mp-th">AKSI</th></tr></thead>
                <tbody>@forelse($anggota as $member)<tr class="mp-tr">
                    <td class="mp-settings-no">{{ $loop->iteration }}</td><td>@if($member->user)<x-eoffice::manajemen-praktikum.ui.staff-person :user="$member->user" :show-email="true" :wrap="true" />@else<span class="mp-settings-email">Pengguna tidak tersedia</span>@endif</td>
                    <td><span class="mp-settings-name">{{ $member->role==='koor'?'Koordinator':'Asisten' }}</span>@if($member->trashed()) <span class="mp-badge error sm">Dicabut</span>@endif</td>
                    <td>{{ $member->created_at?->locale('id')->translatedFormat('d M Y') ?? '—' }}</td>
                    <td><form method="POST" action="{{ route('eoffice.manprak.cerc.kelola-role.'.($member->trashed()?'restore':'revoke'),$member->id) }}" x-data="{confirmOpen:false,confirmed:false,submitting:false}" x-ref="actionForm" @submit="if(submitting){$event.preventDefault();return}if(!confirmed){$event.preventDefault();confirmOpen=true}else{submitting=true}">
                        @csrf @unless($member->trashed()) @method('DELETE') @endunless
                        <button class="mp-btn {{ $member->trashed()?'secondary':'mp-delete-button' }} sm" :disabled="submitting" title="{{ $member->trashed()?'Kembalikan Role':'Cabut Role' }}" aria-label="{{ $member->trashed()?'Kembalikan Role':'Cabut Role' }}"><x-eoffice::manajemen-praktikum.ui.icon :name="$member->trashed()?'history':'trash'" />{{ $member->trashed() ? 'Kembalikan Role' : '' }}</button>
                        <x-eoffice::manajemen-praktikum.ui.schedule-confirmation :title="$member->trashed()?'Kembalikan Role':'Cabut Role'" :message="($member->trashed()?'Kembalikan':'Cabut').' role '.($member->role==='koor'?'koordinator':'asisten').' untuk '.($member->user?->name ?? 'pengguna ini').' pada praktikum ini?'" />
                    </form></td>
                </tr>@empty<tr><td colspan="5" class="mp-settings-empty">Belum ada koordinator atau asisten pada praktikum ini.</td></tr>@endforelse</tbody>
            </table></div>
        </section>
        <template x-teleport="body"><div x-show="roleModalOpen" x-cloak x-transition.opacity class="mp-staffing-mask" @click.self="closeModal()" @keydown.escape.window="if(studentOpen){studentOpen=false}else{closeModal()}">
            <section class="mp-staffing-modal" role="dialog" aria-modal="true" aria-labelledby="staffing-modal-title" :aria-busy="submitting">
                <header><h2 id="staffing-modal-title">Tambah Koordinator / Asisten</h2><p class="mp-staffing-help">Penugasan berlaku untuk {{ $praktikum->nama }}.</p></header>
                <form method="POST" action="{{ route('eoffice.manprak.cerc.kelola-role.assign') }}" @submit="submit($event)">
                    @csrf<input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}"><input type="hidden" name="user_id" :value="userId" :disabled="role==='asprak'"><input type="hidden" name="role" :value="role">
                    <template x-if="role==='asprak'"><div><template x-for="(id,index) in userIds" :key="index"><input type="hidden" name="user_ids[]" :value="id"></template></div></template>
                    <div class="mp-settings-body">
                        <div><label class="mp-settings-label">Role <span class="mp-staffing-danger">*</span></label><x-eoffice::manajemen-praktikum.ui.ta-select x-model="role" :options="['asprak'=>'Asisten','koor'=>'Koordinator']" x-effect="options=coordinatorTaken?{asprak:'Asisten'}:{asprak:'Asisten',koor:'Koordinator'}" /><p class="mp-staffing-help" x-show="coordinatorTaken">Maksimal satu koordinator aktif. Cabut penugasan koordinator saat ini sebelum menunjuk penggantinya.</p></div>
                        <div><label class="mp-settings-label">Mahasiswa <span class="mp-staffing-danger">*</span></label>
                        <template x-for="(id,index) in (role==='asprak'?userIds:[userId])" :key="index">
                        <div style="display:flex;gap:8px;align-items:flex-start;margin-bottom:10px;">
                        <div class="mp-staffing-lookup" style="flex:1;min-width:0;" @click.outside="if(activeRow===index)studentOpen=false">
                            <button type="button" class="ta-select" :disabled="submitting" @click="studentOpen=activeRow===index?!studentOpen:true;activeRow=index;search=''" :aria-expanded="studentOpen&&activeRow===index" aria-label="Pilih mahasiswa"><span x-text="role==='asprak'?labelFor(index):selectedLabel" style="min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></button>
                            <div class="ta-options mp-staffing-options" x-show="studentOpen&&activeRow===index" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1">
                                <div class="mp-staffing-search"><input class="mp-input" style="max-width:none" x-ref="studentSearch" type="search" x-model="search" placeholder="Cari nama / email..." aria-label="Cari mahasiswa" @keydown.enter.prevent></div>
                                <div class="mp-staffing-results"><template x-for="student in filteredStudents" :key="student.value"><button type="button" class="ta-option" :class="String(student.value)===String(id)?'selected':''" @click="selectStudent(student.value,index)"><span x-text="student.label"></span><span class="mp-badge neutral sm" style="flex-shrink:0;font-size:10px;" x-text="studentRoleLabel(student.value)"></span></button></template><div class="mp-staffing-empty" x-show="filteredStudents.length===0">Data tidak ditemukan.</div></div>
                            </div>
                            <p class="mp-staffing-help" x-show="id && hasSelectedRole(id)">Mahasiswa ini sudah memiliki role yang dipilih.</p>
                        </div>
                        <button type="button" x-show="role==='asprak'&&userIds.length>1" class="mp-delete-button mp-btn destructive md" style="flex-shrink:0;" @click="removeStudent(index)" aria-label="Hapus pilihan mahasiswa" title="Hapus"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button>
                        </div>
                        </template>
                        <button type="button" class="mp-btn secondary md" x-show="role==='asprak'&&userIds.length<5" @click="addStudent()" style="width:100%;justify-content:center;border-style:dashed;"><x-eoffice::manajemen-praktikum.ui.icon name="plus" />Tambah Asisten</button>
                        <p class="mp-staffing-help" x-show="role==='asprak'" style="text-align:right;">Maksimal 5 asisten per penunjukan.</p>
                        </div>
                    </div>
                    <div class="mp-settings-footer"><button type="button" class="mp-btn secondary md" @click="closeModal()" :disabled="submitting">Batal</button><button class="mp-btn primary md" :disabled="!canSubmit">Simpan</button></div>
                </form>
            </section>
        </div></template>
    </div>
</x-eoffice::manajemen-praktikum.layout>
