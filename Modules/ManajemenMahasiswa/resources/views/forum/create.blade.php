<x-manajemenmahasiswa::layouts.mahasiswa>

@push('styles')
<style>
    .create-post-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
    }
    
    /* Post Tabs */
    .post-tabs {
        border-bottom: 2px solid #f3f4f6;
        margin-bottom: 24px;
        display: flex;
        gap: 24px;
    }
    .post-tab-item {
        padding-bottom: 12px;
        color: #1f2937;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        position: relative;
    }
    .post-tab-item.active {
        color: #4f46e5;
    }
    .post-tab-item.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #4f46e5;
    }
    
    /* Form Elements */
    .form-label {
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
        margin-bottom: 8px;
    }
    .custom-input, .custom-select, .custom-textarea {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        width: 100%;
        transition: all 0.2s;
    }
    .custom-input:focus, .custom-select:focus, .custom-textarea:focus {
        background-color: #ffffff;
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }
    .char-count {
        font-size: 12px;
        color: #9ca3af;
        position: absolute;
        bottom: 12px;
        right: 16px;
    }
    .input-wrapper {
        position: relative;
    }
    
    /* Action Buttons */
    .btn-action {
        border-radius: 8px;
        padding: 10px 24px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-post {
        background-color: #818cf8;
        color: white;
    }
    .btn-post:hover {
        background-color: #6366f1;
    }
    .btn-cancel {
        background-color: #ef4444;
        color: white;
    }
    .btn-cancel:hover {
        background-color: #dc2626;
    }
    .btn-drafts {
        background: transparent;
        border: none;
        color: #111827;
        font-weight: 700;
        font-size: 15px;
    }
</style>
@endpush

<div class="mb-4">
    <h3 class="fw-bold mb-1 text-dark">Forum Diskusi</h3>
    <p class="text-dark fw-bold" style="font-size: 14px;">Wadah komunikasi mahasiswa & alumni</p>
</div>

<div class="create-post-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">Buat Post</h4>
        <button class="btn-drafts">Drafts</button>
    </div>
    
    <div class="post-tabs">
        <div class="post-tab-item active">Teks</div>
        <div class="post-tab-item">Image&Video</div>
        <div class="post-tab-item">Link</div>
    </div>
    
    <form action="#" method="POST">
        <div class="mb-4">
            <label class="form-label">Judul</label>
            <div class="input-wrapper">
                <input type="text" class="custom-input" placeholder="Placeholder">
                <span class="char-count">0/200</span>
            </div>
        </div>
        
        <div class="mb-4">
            <label class="form-label">Tambah Tag</label>
            <select class="custom-select form-select">
                <option value="" disabled selected>Placeholder</option>
                <option value="akademik">Akademik</option>
                <option value="umum">Umum</option>
                <option value="tanya-jawab">Tanya Jawab</option>
            </select>
        </div>
        
        <div class="mb-5">
            <label class="form-label">Isi Body Text</label>
            <textarea class="custom-textarea" rows="6" placeholder="Placeholder"></textarea>
        </div>
        
        <div class="d-flex justify-content-end gap-3">
            <a href="{{ route('manajemenmahasiswa.forum.index') }}" class="btn-action btn-cancel text-decoration-none">
                <span>✕</span> Kembali
            </a>
            <button type="button" class="btn-action btn-post">
                <span>↑</span> Post
            </button>
        </div>
    </form>
</div>

</x-manajemenmahasiswa::layouts.mahasiswa>
