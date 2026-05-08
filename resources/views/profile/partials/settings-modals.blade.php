{{-- profile/partials/settings-modals.blade.php --}}
@php $user = auth()->user(); @endphp

{{-- Modal: Kelola Foto --}}
<div id="modalManagePhoto" class="hidden fixed inset-0 z-[100] bg-slate-900/60">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 style="font-size:13px;font-weight:600;color:#374151;">Foto Profil</h3>
                <button type="button" onclick="closeManagePhotoModal()" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-4 space-y-2">
                <label for="avatarInput" class="w-full flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 hover:border-blue-200 cursor-pointer transition-all group">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">upload</span>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#374151;">Unggah Foto Baru</p>
                        <p style="font-size:11px;color:#94a3b8;">JPG, PNG atau WebP (Max. 2MB)</p>
                    </div>
                </label>
                <input type="file" id="avatarInput" class="hidden" accept="image/jpeg,image/png,image/webp">
                @if($user->avatar_url)
                <button type="button" onclick="openConfirmDeleteModal()" class="w-full flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-red-50 hover:border-red-200 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors">
                        <span class="material-symbols-outlined">delete</span>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#b91c1c;">Hapus Foto</p>
                        <p style="font-size:11px;color:#94a3b8;">Kembali ke inisial nama</p>
                    </div>
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal: Crop --}}
<div id="modalCrop" class="hidden fixed inset-0 z-[110] bg-slate-900/60">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 style="font-size:13px;font-weight:600;color:#374151;">Sesuaikan Foto</h3>
                <button type="button" onclick="closeCropModal()" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6">
                <div style="max-height:400px;overflow:hidden;border-radius:12px;background:#f8fafc;border:1px solid #f1f5f9;">
                    <img id="imageToCrop" style="display:block;max-width:100%;margin:0 auto;">
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" onclick="closeCropModal()" style="padding:8px 16px;font-size:13px;font-weight:500;color:#64748b;background:transparent;border:none;border-radius:8px;cursor:pointer;">Batal</button>
                <button type="button" id="btnSaveCrop" style="padding:8px 20px;font-size:13px;font-weight:600;background:#5E53F4;color:#fff;border:none;border-radius:8px;cursor:pointer;">Simpan Foto</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Konfirmasi Hapus Foto --}}
<div id="modalDeleteAvatar" class="hidden fixed inset-0 z-[120] bg-slate-900/60">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl">
            <div class="p-6 text-center">
                <div style="width:48px;height:48px;border-radius:50%;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <span class="material-symbols-outlined" style="color:#dc2626;">delete_forever</span>
                </div>
                <p style="font-size:14px;font-weight:600;color:#0f172a;margin-bottom:6px;">Hapus Foto Profil?</p>
                <p style="font-size:12px;color:#64748b;">Foto akan dihapus dan diganti inisial nama. Tidak dapat dibatalkan.</p>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex gap-3 rounded-b-2xl">
                <button type="button" onclick="closeDeleteAvatarModal()" style="flex:1;padding:9px;font-size:13px;font-weight:500;color:#64748b;background:transparent;border:1px solid #e2e8f0;border-radius:8px;cursor:pointer;">Batal</button>
                <button type="button" id="btnConfirmDeleteAvatar" style="flex:1;padding:9px;font-size:13px;font-weight:600;background:#dc2626;color:#fff;border:none;border-radius:8px;cursor:pointer;">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
let cropper;
const avatarInput = document.getElementById('avatarInput');
const imageToCrop = document.getElementById('imageToCrop');
const modalManage = document.getElementById('modalManagePhoto');
const modalCrop   = document.getElementById('modalCrop');
const modalDelete = document.getElementById('modalDeleteAvatar');

function openManagePhotoModal()  { modalManage.classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeManagePhotoModal() { modalManage.classList.add('hidden');    document.body.style.overflow=''; }
function openConfirmDeleteModal(){ closeManagePhotoModal(); modalDelete.classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeDeleteAvatarModal(){ modalDelete.classList.add('hidden');    document.body.style.overflow=''; }

avatarInput.addEventListener('change', function(e) {
    const file = e.target.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = function(ev) {
        closeManagePhotoModal(); imageToCrop.src = ev.target.result;
        modalCrop.classList.remove('hidden'); document.body.style.overflow='hidden';
        if (cropper) cropper.destroy();
        cropper = new Cropper(imageToCrop, {aspectRatio:1,viewMode:1,dragMode:'move',guides:false,center:true,highlight:false,cropBoxMovable:false,cropBoxResizable:false});
    };
    reader.readAsDataURL(file);
});

function closeCropModal() { modalCrop.classList.add('hidden'); document.body.style.overflow=''; avatarInput.value=''; }

document.getElementById('btnSaveCrop').addEventListener('click', function() {
    const btn=this, origFile=avatarInput.files[0];
    const canvas=cropper.getCroppedCanvas({width:400,height:400});
    btn.disabled=true; btn.textContent='Memproses...';
    canvas.toBlob(function(blob) {
        const fd=new FormData(); fd.append('avatar',blob,'avatar.webp'); fd.append('avatar_original',origFile);
        fetch("{{ route('profile.avatar.update') }}", {method:'POST',body:fd,headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})
        .then(r=>r.json()).then(d=>{ if(d.status==='success') window.location.reload(); else {alert(d.message||'Gagal');btn.disabled=false;btn.textContent='Simpan Foto';} })
        .catch(()=>{btn.disabled=false;btn.textContent='Simpan Foto';});
    },'image/webp',0.8);
});

document.getElementById('btnConfirmDeleteAvatar')?.addEventListener('click', function() {
    const btn=this; btn.disabled=true; btn.textContent='Menghapus...';
    fetch("{{ route('profile.avatar.destroy') }}", {method:'DELETE',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})
    .then(r=>r.json()).then(d=>{ if(d.status==='success') window.location.reload(); else {alert('Gagal');btn.disabled=false;btn.textContent='Ya, Hapus';} })
    .catch(()=>{btn.disabled=false;btn.textContent='Ya, Hapus';});
});
</script>