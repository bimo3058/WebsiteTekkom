<style>
    /* ── Form Card ── */
    .form-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
    }
    .form-card-title {
        font-weight: 700;
        font-size: 16px;
        color: #0D0D12;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f3f4f6;
    }

    /* ── Custom Form Styles ── */
    .form-label-custom {
        font-weight: 600;
        font-size: 13px;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-label-custom .required {
        color: #dc2626;
    }
    .form-control-custom,
    .form-select-custom {
        border: 1.5px solid #DFE1E7;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        font-weight: 500;
        color: #0D0D12;
        transition: all 0.2s;
        background: #fff;
    }
    .form-control-custom:focus,
    .form-select-custom:focus {
        border-color: #0B266E;
        box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
        outline: none;
    }
    .form-control-custom::placeholder {
        color: #666D80;
        font-weight: 400;
    }
    textarea.form-control-custom {
        min-height: 140px;
        resize: vertical;
    }

    /* ── Searchable Select ── */
    .search-select-wrapper {
        position: relative;
    }
    .search-select-wrapper input[type="text"] {
        width: 100%;
    }
    .search-select-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1.5px solid #DFE1E7;
        border-top: none;
        border-radius: 0 0 10px 10px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 100;
        display: none;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .search-select-dropdown.show {
        display: block;
    }
    .search-select-option {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        transition: background 0.15s;
        border-bottom: 1px solid #f9fafb;
    }
    .search-select-option:hover {
        background: #eef2ff;
        color: #0B266E;
    }
    .search-select-option .sub-text {
        font-size: 11px;
        color: #666D80;
        font-weight: 400;
    }

    /* ── Checkbox Card Group ── */
    .checkbox-card-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .checkbox-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border: 1.5px solid #DFE1E7;
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        user-select: none;
    }
    .checkbox-card:hover {
        border-color: #5C78B8;
        background: #eef2ff;
    }
    .checkbox-card input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #0B266E;
        cursor: pointer;
        flex-shrink: 0;
    }
    .checkbox-card.checked {
        border-color: #0B266E;
        background: #eef2ff;
        color: #091958;
        font-weight: 600;
    }
    .checkbox-card.disabled {
        opacity: 0.45;
        cursor: not-allowed;
        pointer-events: none;
    }
    .checkbox-hint {
        font-size: 11px;
        color: #666D80;
        font-weight: 400;
        margin-top: 6px;
    }

    /* ── Banner Preview ── */
    .banner-upload-area {
        border: 2px dashed #C1C7CF;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafafa;
    }
    .banner-upload-area:hover {
        border-color: #0B266E;
        background: #eef2ff;
    }
    .banner-upload-area .upload-icon {
        font-size: 36px;
        margin-bottom: 8px;
        opacity: 0.5;
    }
    .banner-upload-area p {
        color: #666D80;
        font-size: 13px;
        font-weight: 500;
        margin: 0;
    }
    .banner-upload-area small {
        color: #666D80;
        font-size: 12px;
    }
    .banner-preview {
        width: 100%;
        max-height: 220px;
        object-fit: cover;
        border-radius: 10px;
        margin-top: 12px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .banner-preview:hover {
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    /* ── Lightbox Modal ── */
    .lightbox-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10000;
        background: rgba(0, 0, 0, 0.92);
        align-items: center;
        justify-content: center;
        animation: lightboxFadeIn 0.25s ease;
    }
    .lightbox-modal.active {
        display: flex;
    }
    @keyframes lightboxFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .lightbox-content {
        position: relative;
        max-width: 90vw;
        max-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .lightbox-content img {
        max-width: 90vw;
        max-height: 82vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
        animation: lightboxZoomIn 0.3s ease;
    }
    @keyframes lightboxZoomIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .lightbox-close {
        position: fixed;
        top: 20px;
        right: 24px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        z-index: 10001;
    }
    .lightbox-close:hover {
        background: rgba(255,255,255,0.2);
        transform: scale(1.05);
    }
    .banner-current {
        position: relative;
        margin-bottom: 12px;
    }
    .banner-current .badge-current {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(0,0,0,0.6);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
    }

    /* ── Multi File Upload ── */
    .file-upload-area {
        border: 2px dashed #C1C7CF;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafafa;
    }
    .file-upload-area:hover,
    .file-upload-area.dragover {
        border-color: #0B266E;
        background: #eef2ff;
    }
    .file-upload-area .upload-icon {
        font-size: 28px;
        margin-bottom: 6px;
        opacity: 0.5;
    }
    .file-upload-area p {
        color: #666D80;
        font-size: 13px;
        font-weight: 500;
        margin: 0;
    }
    .file-upload-area small {
        color: #666D80;
        font-size: 12px;
    }
    .file-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        margin-top: 14px;
    }
    .file-preview-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #DFE1E7;
        background: #f9fafb;
        transition: all 0.2s;
    }
    .file-preview-item img {
        width: 100%;
        height: 100px;
        object-fit: cover;
    }
    .file-preview-item .file-info {
        padding: 8px 10px;
        font-size: 11px;
        font-weight: 600;
        color: #374151;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .file-preview-item .file-size {
        font-size: 10px;
        color: #666D80;
        font-weight: 400;
    }
    .file-preview-item .btn-remove-file {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(220, 38, 38, 0.85);
        color: #fff;
        border: none;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        line-height: 1;
    }
    .file-preview-item .btn-remove-file:hover {
        background: #dc2626;
        transform: scale(1.1);
    }
    .doc-preview-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border: 1px solid #DFE1E7;
        border-radius: 10px;
        background: #f9fafb;
        position: relative;
        margin-bottom: 8px;
    }
    .doc-preview-item .doc-icon {
        font-size: 24px;
        flex-shrink: 0;
    }
    .doc-preview-item .doc-info {
        flex: 1;
        min-width: 0;
    }
    .doc-preview-item .doc-name {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .doc-preview-item .doc-size {
        font-size: 11px;
        color: #666D80;
    }
    .doc-preview-item .btn-remove-doc {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #fee2e2;
        color: #dc2626;
        border: none;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s;
    }
    .doc-preview-item .btn-remove-doc:hover {
        background: #dc2626;
        color: #fff;
    }
    .existing-file-label {
        font-size: 12px;
        font-weight: 700;
        color: #666D80;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 10px;
        margin-top: 4px;
    }

    /* ── Back Button ── */
    .detail-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 24px;
    }
    .btn-back {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #DFE1E7;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #374151;
        font-size: 18px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .btn-back:hover {
        background: #f3f4f6;
        border-color: #C1C7CF;
        color: #0D0D12;
    }

    /* ── Buttons ── */
    .btn-submit {
        background: #0B266E;
        color: #ffffff;
        font-weight: 600;
        font-size: 14px;
        padding: 12px 28px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-submit:hover {
        background: #091958;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(11, 38, 110, 0.3);
    }
    .btn-cancel {
        background: #f3f4f6;
        color: #374151;
        font-weight: 600;
        font-size: 14px;
        padding: 12px 28px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-cancel:hover {
        background: #DFE1E7;
        color: #0D0D12;
    }

    /* ── Multi-Select Panitia ── */
    .panitia-select-wrapper {
        position: relative;
    }
    .panitia-chips-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        min-height: 44px;
        padding: 8px 12px;
        border: 1.5px solid #DFE1E7;
        border-radius: 10px;
        background: #fff;
        cursor: text;
        transition: border-color 0.2s, box-shadow 0.2s;
        align-items: center;
    }
    .panitia-chips-container:focus-within {
        border-color: #0B266E;
        box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
    }
    .panitia-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        background: #eef2ff;
        color: #091958;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #5C78B8;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .panitia-chip:hover {
        background: #eef2ff;
    }
    .panitia-chip-remove {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #5C78B8;
        color: #091958;
        border: none;
        font-size: 11px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        padding: 0;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .panitia-chip-remove:hover {
        background: #dc2626;
        color: #fff;
    }
    .panitia-search-input {
        border: none;
        outline: none;
        font-size: 13px;
        font-weight: 500;
        color: #0D0D12;
        flex: 1;
        min-width: 120px;
        background: transparent;
        padding: 2px 0;
    }
    .panitia-search-input::placeholder {
        color: #666D80;
        font-weight: 400;
    }
    .panitia-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1.5px solid #DFE1E7;
        border-top: none;
        border-radius: 0 0 10px 10px;
        max-height: 220px;
        overflow-y: auto;
        z-index: 200;
        display: none;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .panitia-dropdown.show {
        display: block;
    }
    .panitia-option {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        transition: background 0.15s;
        border-bottom: 1px solid #f9fafb;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panitia-option:hover {
        background: #eef2ff;
        color: #0B266E;
    }
    .panitia-option.selected {
        background: #f0fdf4;
        color: #16a34a;
        pointer-events: none;
        opacity: 0.6;
    }
    .panitia-option .sub-text {
        font-size: 11px;
        color: #666D80;
        font-weight: 400;
    }
    .panitia-option .check-icon {
        margin-left: auto;
        color: #16a34a;
        font-size: 13px;
        display: none;
    }
    .panitia-option.selected .check-icon {
        display: inline;
    }
    .panitia-empty {
        padding: 14px;
        text-align: center;
        font-size: 13px;
        color: #666D80;
        font-weight: 400;
    }
    .panitia-count-badge {
        font-size: 11px;
        font-weight: 600;
        color: #666D80;
        background: #f3f4f6;
        padding: 2px 8px;
        border-radius: 20px;
        margin-left: 6px;
    }
</style>
