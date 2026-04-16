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
    .post-tab-item:hover {
        color: #4f46e5;
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
        border-radius: 2px;
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
        padding: 6px 12px;
        border-radius: 6px;
        transition: background 0.2s;
    }
    .btn-drafts:hover {
        background: #f3f4f6;
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
        <button type="button" class="btn-drafts" onclick="loadDraft()">📥 Load Draft</button>
    </div>
    
    <div class="post-tabs">
        <div class="post-tab-item active" data-tab="text" onclick="switchTab('text')">📝 Teks</div>
        <div class="post-tab-item" data-tab="media" onclick="switchTab('media')">🖼️ Image & Video</div>
        <div class="post-tab-item" data-tab="link" onclick="switchTab('link')">🔗 Link</div>
    </div>
    
    <form action="{{ route('manajemenmahasiswa.forum.store') }}" method="POST" id="createPostForm">
        @csrf
        <input type="hidden" name="format" id="postFormat" value="text">
        <input type="hidden" name="konten" id="finalKonten" value="">

        <div class="mb-4">
            <label class="form-label">Judul Postingan</label>
            <div class="input-wrapper">
                <input type="text" name="judul" id="inputJudul" class="custom-input align-content-start @error('judul') border-danger @enderror" placeholder="Tuliskan judul yang menarik dan deskriptif..." maxlength="200" required value="{{ old('judul') }}">
                <span class="char-count"><span id="judulCount">0</span>/200</span>
            </div>
            @error('judul')
                <div class="text-danger mt-1 fw-medium" style="font-size: 13px;">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="form-label">Kategori Postingan</label>
            <select name="kategori" id="inputKategori" class="custom-select form-select @error('kategori') border-danger @enderror" required>
                <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih Kategori Obrolan</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ old('kategori') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('kategori')
                <div class="text-danger mt-1 fw-medium" style="font-size: 13px;">{{ $message }}</div>
            @enderror
        </div>
        
        <!-- Tab Content: Text -->
        <div class="mb-5 tab-content" id="content-text">
            <label class="form-label">Isi Postingan</label>
            <textarea id="inputTextBody" class="custom-textarea @error('konten') border-danger @enderror" rows="8" placeholder="Bagikan apa yang ada di pikiranmu..."></textarea>
            @error('konten')
                <div class="text-danger mt-1 fw-medium" style="font-size: 13px;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Tab Content: Media -->
        <div class="mb-5 tab-content" id="content-media" style="display: none;">
            <div class="alert alert-info py-2 mb-3" style="font-size: 13px; border-radius: 8px; border: none; background: #e0e7ff; color: #4338ca;">
                💡 <b>Tips:</b> Masukkan URL/Tautan gambar (JPG/PNG) atau video (YouTube/MP4) yang valid. Sistem akan otomatis menampilkan medianya.
            </div>
            <label class="form-label">Tautan Media (Image / Video URL)</label>
            <input type="url" id="inputMediaUrl" class="custom-input mb-3" placeholder="Contoh: https://example.com/image.jpg atau https://youtube.com/watch?v=...">
            <label class="form-label">Deskripsi Tambahan (Opsional)</label>
            <textarea id="inputMediaCaption" class="custom-textarea" rows="4" placeholder="Ceritakan lebih banyak tentang media ini..."></textarea>
        </div>

        <!-- Tab Content: Link -->
        <div class="mb-5 tab-content" id="content-link" style="display: none;">
            <label class="form-label">Tautan (URL)</label>
            <input type="url" id="inputLinkUrl" class="custom-input mb-3" placeholder="Contoh: https://medium.com/@username/title">
            <label class="form-label">Deskripsi Tautan (Opsional)</label>
            <textarea id="inputLinkCaption" class="custom-textarea" rows="4" placeholder="Mengapa tautan ini menarik untuk dibagikan?"></textarea>
        </div>
        
        <div class="d-flex justify-content-end gap-3 align-items-center pb-2">
            <span id="draftStatus" class="text-muted me-auto bg-light rounded px-3 py-2 fw-medium" style="font-size: 13px; display: none;"></span>
            
            <button type="button" class="btn btn-light fw-bold text-secondary px-4 py-2 shadow-sm" onclick="saveDraft()" style="border-radius: 8px;">
                💾 Simpan Draft
            </button>
            <a href="{{ route('manajemenmahasiswa.forum.index') }}" class="btn-action btn-cancel text-decoration-none shadow-sm">
                <span>✕</span> Batal
            </a>
            <button type="submit" class="btn-action btn-post shadow-sm px-4">
                <span>🚀</span> Terbitkan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Tab switching logic
    function switchTab(tab) {
        // Toggle Active Class on Tabs
        document.querySelectorAll('.post-tab-item').forEach(el => el.classList.remove('active'));
        document.querySelector(`.post-tab-item[data-tab="${tab}"]`).classList.add('active');
        
        // Toggle Display Content
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.getElementById(`content-${tab}`).style.display = 'block';
        
        // Update hidden format input
        document.getElementById('postFormat').value = tab;
    }

    // Live Character counter for Judul
    const judulInput = document.getElementById('inputJudul');
    const judulCount = document.getElementById('judulCount');
    
    // Check initial length (if validation fail return back with old)
    if(judulInput.value) {
        judulCount.textContent = judulInput.value.length;
    }

    judulInput.addEventListener('input', function() {
        judulCount.textContent = this.value.length;
    });

    // Handle Form Submission Interceptor
    const form = document.getElementById('createPostForm');
    form.addEventListener('submit', function(e) {
        const format = document.getElementById('postFormat').value;
        let combinedKonten = '';

        if (format === 'text') {
            combinedKonten = document.getElementById('inputTextBody').value;
        } 
        else if (format === 'media') {
            const mediaUrl = document.getElementById('inputMediaUrl').value;
            const caption = document.getElementById('inputMediaCaption').value;
            if (mediaUrl) {
                // Simple regex to detect image extension
                if (mediaUrl.match(/\.(jpeg|jpg|gif|png|webp)(\?.*)?$/i) != null) {
                    combinedKonten = `<img src="${mediaUrl}" alt="Media Post" style="max-width: 100%; border-radius: 8px; margin-bottom: 15px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);"><br>${caption}`;
                } 
                // YouTube embed parser
                else if (mediaUrl.includes('youtube.com') || mediaUrl.includes('youtu.be')) {
                    let videoId = mediaUrl.split('v=')[1];
                    if(!videoId && mediaUrl.includes('youtu.be/')) videoId = mediaUrl.split('youtu.be/')[1];
                    const ampersandPosition = videoId ? videoId.indexOf('&') : -1;
                    if(ampersandPosition != -1) videoId = videoId.substring(0, ampersandPosition);
                    
                    if(videoId) {
                         combinedKonten = `<iframe width="100%" height="400" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allowfullscreen style="border-radius: 8px; margin-bottom: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);"></iframe><br>${caption}`;
                    } else {
                         combinedKonten = `<a href="${mediaUrl}" target="_blank" class="text-primary fw-bold text-decoration-underline">${mediaUrl}</a><br>${caption}`;
                    }
                } 
                // Standard Video fallback
                else if (mediaUrl.match(/\.(mp4|webm|mkv)(\?.*)?$/i) != null) {
                    combinedKonten = `<video width="100%" controls style="border-radius: 8px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);"><source src="${mediaUrl}"></video><br>${caption}`;
                }
                // Unknown media link
                else {
                    combinedKonten = `<a href="${mediaUrl}" target="_blank" class="text-primary fw-bold text-decoration-underline">${mediaUrl}</a><br>${caption}`;
                }
            } else {
                combinedKonten = caption; // Fallback jika tidak ada media
            }
        } 
        else if (format === 'link') {
            const linkUrl = document.getElementById('inputLinkUrl').value;
            const caption = document.getElementById('inputLinkCaption').value;
            if (linkUrl) {
                combinedKonten = `<a href="${linkUrl}" target="_blank" class="d-inline-flex p-3 rounded bg-light border border-primary-subtle text-primary fw-bold text-decoration-none mb-3">🔗 ${linkUrl}</a><br>${caption}`;
            } else {
                combinedKonten = caption;
            }
        }

        // Fill the final hidden input to be sent to backend
        document.getElementById('finalKonten').value = combinedKonten;
        
        // Clean draft completely upon successful submission
        localStorage.removeItem('forum_draft_judul');
        localStorage.removeItem('forum_draft_kategori');
        localStorage.removeItem('forum_draft_text');
        localStorage.removeItem('forum_draft_mediaUrl');
        localStorage.removeItem('forum_draft_mediaCap');
        localStorage.removeItem('forum_draft_linkUrl');
        localStorage.removeItem('forum_draft_linkCap');
        localStorage.removeItem('forum_draft_format');
    });

    // -----------------------------------------
    // Drafts Logic using Client Side localStorage
    // -----------------------------------------
    function saveDraft() {
        // Save current format tab
        const format = document.getElementById('postFormat').value;
        localStorage.setItem('forum_draft_format', format);
        
        // Save Global Fields
        localStorage.setItem('forum_draft_judul', document.getElementById('inputJudul').value);
        localStorage.setItem('forum_draft_kategori', document.getElementById('inputKategori').value);
        
        // Save tab-specific fields
        localStorage.setItem('forum_draft_text', document.getElementById('inputTextBody').value);
        localStorage.setItem('forum_draft_mediaUrl', document.getElementById('inputMediaUrl').value);
        localStorage.setItem('forum_draft_mediaCap', document.getElementById('inputMediaCaption').value);
        localStorage.setItem('forum_draft_linkUrl', document.getElementById('inputLinkUrl').value);
        localStorage.setItem('forum_draft_linkCap', document.getElementById('inputLinkCaption').value);
        
        showDraftStatus('✅ Draft berhasil disimpan: ' + new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
    }

    function loadDraft() {
        const title = localStorage.getItem('forum_draft_judul');
        const category = localStorage.getItem('forum_draft_kategori');
        const format = localStorage.getItem('forum_draft_format') || 'text';

        let draftFound = false;

        // Restore global fields
        if (title) { document.getElementById('inputJudul').value = title; draftFound = true; }
        if (category) { document.getElementById('inputKategori').value = category; draftFound = true; }
        
        // Restore tab-specific fields
        const text = localStorage.getItem('forum_draft_text');
        if (text) document.getElementById('inputTextBody').value = text;
        
        const mediaUrl = localStorage.getItem('forum_draft_mediaUrl');
        if (mediaUrl) document.getElementById('inputMediaUrl').value = mediaUrl;
        
        const mediaCap = localStorage.getItem('forum_draft_mediaCap');
        if (mediaCap) document.getElementById('inputMediaCaption').value = mediaCap;
        
        const linkUrl = localStorage.getItem('forum_draft_linkUrl');
        if (linkUrl) document.getElementById('inputLinkUrl').value = linkUrl;
        
        const linkCap = localStorage.getItem('forum_draft_linkCap');
        if (linkCap) document.getElementById('inputLinkCaption').value = linkCap;
        
        if(draftFound) {
            // Trigger length calculation
            judulInput.dispatchEvent(new Event('input'));
            
            // Switch to the correct tab where the draft was saved
            switchTab(format);
            
            showDraftStatus('📥 Berhasil memuat draft terakhir!');
        } else {
            showDraftStatus('⚠️ Tidak ada draft yang tersimpan.');
        }
    }

    function showDraftStatus(message) {
        const status = document.getElementById('draftStatus');
        status.style.display = 'block';
        status.textContent = message;
        setTimeout(() => status.style.display = 'none', 4000);
    }

    // Auto-save draft quietly every 1 min if user changes things
    setInterval(() => {
        if(document.getElementById('inputJudul').value.length > 5 || document.getElementById('inputTextBody').value.length > 5) {
            saveDraft();
        }
    }, 60000);

</script>
@endpush

</x-manajemenmahasiswa::layouts.mahasiswa>
