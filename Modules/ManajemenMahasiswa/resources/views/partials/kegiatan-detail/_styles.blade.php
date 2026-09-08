<style>
    /* ── Back Button & Header ── */
    .detail-header { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
    .btn-back { width: 40px; height: 40px; border-radius: 50%; background: #ffffff; border: 1px solid #DFE1E7; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #374151; font-size: 18px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06); transition: all 0.2s; flex-shrink: 0; }
    .btn-back:hover { background: #f3f4f6; border-color: #C1C7CF; color: #0D0D12; }

    /* ── Banner ── */
    .detail-banner { width: 100%; aspect-ratio: 16 / 9; border-radius: 12px; overflow: hidden; margin-bottom: 24px; background: linear-gradient(135deg, rgba(11,38,110,0.06) 0%, rgba(11,38,110,0.12) 100%); display: flex; align-items: center; justify-content: center; }
    .detail-banner img { width: 100%; height: 100%; object-fit: cover; }
    .detail-banner .placeholder-icon { font-size: 64px; opacity: 0.4; }

    /* ── Info Card (matching forum-card style) ── */
    .detail-card { background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); margin-bottom: 20px; }
    .detail-card-title { font-weight: 700; font-size: 16px; color: #0D0D12; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

    /* ── Badges ── */
    .badge-bidang { font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; background: #eef2ff; color: #0B266E; }
    .badge-kategori { font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; background: #FFFBEB; color: #92400e; }

    /* ── Metadata Grid ── */
    .meta-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px; margin-top: 18px; }
    .meta-item { background: #f9fafb; border-radius: 10px; padding: 14px 16px; border: 1px solid #f3f4f6; }
    .meta-item-label { font-size: 11px; font-weight: 700; color: #666D80; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 4px; }
    .meta-item-value { font-size: 14px; font-weight: 600; color: #0D0D12; display: flex; align-items: center; gap: 6px; }

    /* ── Description ── */
    .detail-description { font-size: 14px; color: #374151; line-height: 1.75; white-space: pre-line; }

    /* ── Photo Gallery ── */
    .gallery-header { display: flex; align-items: center; justify-content: space-between; }
    .gallery-count { font-size: 12px; font-weight: 600; color: #666D80; background: #f3f4f6; padding: 4px 12px; border-radius: 20px; }
    .photo-gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; margin-top: 16px; }
    .photo-gallery-item { position: relative; border-radius: 12px; overflow: hidden; cursor: pointer; aspect-ratio: 4/3; background: #f3f4f6; border: 1px solid #DFE1E7; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .photo-gallery-item:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(11, 38, 110, 0.15); border-color: #5C78B8; }
    .photo-gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
    .photo-gallery-item:hover img { transform: scale(1.05); }
    .photo-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 50%); opacity: 0; transition: opacity 0.3s ease; display: flex; flex-direction: column; justify-content: flex-end; padding: 14px; }
    .photo-gallery-item:hover .photo-overlay { opacity: 1; }
    .photo-overlay .photo-name { color: #fff; font-size: 12px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* ── Lightbox Modal ── */
    .lightbox-modal { display: none; position: fixed; inset: 0; z-index: 10000; background: rgba(0, 0, 0, 0.92); align-items: center; justify-content: center; animation: lightboxFadeIn 0.25s ease; }
    .lightbox-modal.active { display: flex; }
    @keyframes lightboxFadeIn { from { opacity: 0; } to { opacity: 1; } }
    .lightbox-content { position: relative; max-width: 90vw; max-height: 85vh; display: flex; align-items: center; justify-content: center; }
    .lightbox-content img { max-width: 90vw; max-height: 82vh; object-fit: contain; border-radius: 8px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4); animation: lightboxZoomIn 0.3s ease; }
    @keyframes lightboxZoomIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .lightbox-close { position: fixed; top: 20px; right: 24px; width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; z-index: 10001; }
    .lightbox-close:hover { background: rgba(255,255,255,0.2); transform: scale(1.05); }
    .lightbox-nav { position: fixed; top: 50%; transform: translateY(-50%); width: 48px; height: 48px; border-radius: 50%; background: rgba(255,255,255,0.1); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.15); color: #fff; font-size: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; z-index: 10001; }
    .lightbox-nav:hover { background: rgba(255,255,255,0.25); transform: translateY(-50%) scale(1.08); }
    .lightbox-nav.prev { left: 20px; }
    .lightbox-nav.next { right: 20px; }
    .lightbox-info { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); text-align: center; z-index: 10001; }
    .lightbox-info .lightbox-title { color: #fff; font-size: 14px; font-weight: 600; margin-bottom: 4px; }
    .lightbox-info .lightbox-counter { color: rgba(255,255,255,0.6); font-size: 12px; font-weight: 500; }

    /* ── Document Cards ── */
    .document-list { display: flex; flex-direction: column; gap: 10px; margin-top: 16px; }
    .document-card { display: flex; align-items: center; gap: 14px; padding: 16px 18px; background: #f9fafb; border: 1px solid #DFE1E7; border-radius: 12px; text-decoration: none !important; transition: all 0.25s ease; }
    .document-card:hover { background: #eef2ff; border-color: #5C78B8; transform: translateX(4px); box-shadow: 0 4px 12px rgba(11, 38, 110, 0.08); }
    .document-card .doc-icon-wrapper { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
    .doc-icon-pdf { background: #fee2e2; } .doc-icon-word { background: #dbeafe; } .doc-icon-excel { background: #ECFDF5; } .doc-icon-ppt { background: #FFFBEB; } .doc-icon-other { background: #f3f4f6; }
    .document-card .doc-details { flex: 1; min-width: 0; }
    .document-card .doc-title { font-size: 14px; font-weight: 600; color: #0D0D12; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 2px; }
    .document-card .doc-meta { font-size: 12px; color: #666D80; font-weight: 500; display: flex; align-items: center; gap: 8px; }
    .document-card .doc-ext-badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.3px; }
    .ext-pdf { background: #fee2e2; color: #dc2626; } .ext-doc, .ext-docx { background: #dbeafe; color: #2563eb; } .ext-xls, .ext-xlsx { background: #ECFDF5; color: #16a34a; } .ext-ppt, .ext-pptx { background: #FFFBEB; color: #d97706; } .ext-default { background: #f3f4f6; color: #666D80; }
    .document-card .doc-download-btn { width: 38px; height: 38px; border-radius: 10px; background: #eef2ff; border: 1px solid #5C78B8; color: #0B266E; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s; }
    .document-card:hover .doc-download-btn { background: #0B266E; color: #fff; border-color: #0B266E; }

    /* ── Empty State ── */
    .empty-luaran { text-align: center; padding: 40px 20px; }
    .empty-luaran-icon { width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #eef2ff, #eef2ff); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 32px; }
</style>
