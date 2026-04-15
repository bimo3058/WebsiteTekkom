<x-manajemenmahasiswa::layouts.mahasiswa>

@push('styles')
<style>
    .forum-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
    }
    .avatar-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: #e0e7ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 20px;
    }
    .avatar-sm {
        width: 36px;
        height: 36px;
        font-size: 16px;
    }
    .btn-join {
        background-color: #818cf8;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 4px 16px;
        font-size: 13px;
        font-weight: 600;
        transition: background-color 0.2s;
    }
    .btn-join:hover {
        background-color: #6366f1;
    }
    .post-actions button {
        background: #f3f4f6;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        color: #4b5563;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-right: 12px;
    }
    .post-actions button:hover {
        background: #e5e7eb;
    }
    .tag-label {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .tag-green { background: #dcfce7; color: #16a34a; }
    .tag-red { background: #fee2e2; color: #dc2626; }
    .tag-gray { background: #f3f4f6; color: #6b7280; }
    
    .comment-list {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
    }
    
    .comment-item {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f3f4f6;
    }
    .comment-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .reply-form textarea {
        border-radius: 8px;
        resize: none;
        background-color: #f9fafb;
    }
    .reply-form textarea:focus {
        background-color: #ffffff;
        box-shadow: 0 0 0 2px #e0e7ff;
        border-color: #6366f1;
    }
    .btn-post {
        background-color: #818cf8;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 24px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-post:hover {
        background-color: #6366f1;
    }
</style>
@endpush

<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('manajemenmahasiswa.forum.index') }}" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
        <span aria-hidden="true">&larr;</span>
    </a>
    <div>
        <h3 class="fw-bold mb-0 text-dark">Detail Diskusi</h3>
        <p class="text-muted text-sm fw-medium mb-0">Baca postingan dan ikuti diskusinya</p>
    </div>
</div>

<div class="forum-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-placeholder">
                👤
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0">Username</h5>
                <span class="text-primary fw-medium" style="font-size: 13px;">• 2 hours ago</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button class="btn-join">Joined</button>
            <span class="text-muted" style="cursor: pointer; font-size: 24px; line-height: 1;">...</span>
        </div>
    </div>

    <h4 class="fw-bold text-dark mb-3">Lorem ipsum dolor sit amet consectetur adipiscing elit?</h4>
    
    <p class="text-dark" style="font-size: 15px; margin-bottom: 24px; line-height: 1.6;">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    </p>

    <!-- Labels -->
    <div class="d-flex gap-2 mb-4">
        <span class="tag-label tag-green">Akademik</span>
        <span class="tag-label tag-red">Penting</span>
        <span class="tag-label tag-gray">+4 tags</span>
    </div>

    <!-- Actions -->
    <div class="post-actions d-flex align-items-center mb-4">
        <button class="shadow-sm">
            <span class="me-1" style="font-size: 16px;">↑</span> 128 <span class="ms-1" style="font-size: 16px;">↓</span>
        </button>
        <button class="shadow-sm">
            <span class="me-1" style="font-size: 15px;">💬</span> 24 Comments
        </button>
        <button class="shadow-sm">
            <span style="font-size: 15px;">🔗</span> Share
        </button>
    </div>

    <!-- Reply Section -->
    <div class="reply-form mb-2">
        <div class="d-flex gap-3">
            <div class="avatar-placeholder avatar-sm flex-shrink-0">
                👤
            </div>
            <div class="flex-grow-1">
                <textarea class="form-control mb-3" rows="3" placeholder="Tulis komentar Anda di sini..."></textarea>
                <div class="d-flex justify-content-end">
                    <button class="btn-post shadow-sm">Kirim Komentar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments List -->
    <div class="comment-list">
        <h6 class="fw-bold text-dark mb-4">24 Comments</h6>
        
        @for ($i = 0; $i < 3; $i++)
        <div class="comment-item">
            <div class="d-flex gap-3">
                <div class="avatar-placeholder avatar-sm flex-shrink-0" style="background-color: #fce7f3; color: #db2777;">
                    👤
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold text-dark" style="font-size: 14px;">User Commenter {{ $i+1 }}</span>
                        <span class="text-muted" style="font-size: 12px;">• 1 hour ago</span>
                    </div>
                    <p class="text-dark mb-2" style="font-size: 14px; line-height: 1.5;">
                        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excellent post!
                    </p>
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-link p-0 text-muted fw-bold d-flex align-items-center gap-1 text-decoration-none" style="font-size: 13px;">
                            <span>↑</span> 12
                        </button>
                        <button class="btn btn-link p-0 text-muted fw-bold d-flex align-items-center gap-1 text-decoration-none" style="font-size: 13px;">
                            <span>↓</span>
                        </button>
                        <button class="btn btn-link p-0 text-muted fw-bold text-decoration-none" style="font-size: 13px;">
                            Reply
                        </button>
                        <button class="btn btn-link p-0 text-muted fw-bold text-decoration-none" style="font-size: 13px;">
                            Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endfor
        
        <div class="text-center mt-4">
            <button class="btn btn-light fw-bold text-primary px-4 py-2" style="border-radius: 8px;">Muat Lebih Banyak</button>
        </div>
    </div>
</div>

</x-manajemenmahasiswa::layouts.mahasiswa>
