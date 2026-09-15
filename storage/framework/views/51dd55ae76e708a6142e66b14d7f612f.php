<style>
/* ─── NProgress Override ─── */
#nprogress .bar { background: #0B266E !important; height: 3px !important; }
#nprogress .peg { display: none !important; }
#nprogress .spinner { display: none !important; }

/* ─── SITKOM Design System — ManajemenPraktikum component layer ─── */

/* Box / Wrap (superadmin pattern) */
.mp-wrap { flex:1 1 0%; overflow:hidden; padding:10px; display:flex; flex-direction:column; min-height:0; min-width:0; }
.mp-box  { flex:1 1 0%; min-height:0; min-width:0; background:#fff; border:1px solid var(--c-border); border-radius:12px;
           box-shadow:0 1px 3px rgba(0,0,0,.06); overflow:hidden; display:flex; flex-direction:column; }
/* Content-sized grid rows keep page-level flex-1 wrappers from compressing cards.
   The bounded body owns vertical scrolling; tables retain their horizontal scroll. */
.mp-box-body { flex:1 1 0%; min-height:0; min-width:0; overflow:auto; padding:20px 24px;
               display:grid; grid-template-columns:minmax(0,1fr); grid-auto-rows:max-content; align-content:start;
               gap:18px; scrollbar-gutter:stable; scrollbar-width:thin; scrollbar-color:var(--c-border-strong) transparent; }
.mp-box-body::-webkit-scrollbar { width:5px; }
.mp-box-body::-webkit-scrollbar-thumb { background:var(--c-border-strong); border-radius:10px; }

/* Flash */
.mp-flash { display:flex; align-items:center; gap:8px; padding:10px 16px; font-size:13px;
            font-weight:500; flex-shrink:0; border-bottom:1px solid transparent; }
.mp-flash-success { background:#DDF2EE; color:#174E43; border-color:#40C4AA; }
.mp-flash-error   { background:#FADAE1; color:#7C1028; border-color:#DF1C41; }

/* Page header (like superadmin _header) */
.mp-page-header { display:flex; align-items:flex-start; justify-content:space-between;
                  gap:16px; flex-wrap:wrap; flex-shrink:0; }
.mp-page-title  { font-size:20px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2; margin:0; }
.mp-page-sub    { font-size:12px; color:var(--c-fg-muted); margin-top:3px; }
.mp-page-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }

/* Badges */
.mp-badge { display:inline-flex; align-items:center; gap:4px; font-size:12px; font-weight:600;
            padding:3px 10px; border-radius:9999px; letter-spacing:0.01em; line-height:1;
            white-space:nowrap; font-family:'Inter Tight',sans-serif; border:1px solid transparent; }
.mp-badge.sm { font-size:11px; padding:2px 8px; }
.mp-badge.lg { font-size:13px; padding:4px 12px; }
.mp-badge.primary  { background:#EEF2FF; color:#0B266E; border-color:#C7D2FE; }
.mp-badge.primary-fill { background:#0B266E; color:#fff; border-color:#0B266E; }
.mp-badge.success  { background:#F0FDF9; color:#0D9488; border-color:#99F6E4; }
.mp-badge.success-fill { background:#0D9488; color:#fff; border-color:#0D9488; }
.mp-badge.success .dot { display:none; }
.mp-badge.warning  { background:#FFFBEB; color:#92400E; border-color:#FCD34D; }
.mp-badge.warning-fill { background:#D97706; color:#fff; border-color:#D97706; }
.mp-badge.error    { background:#FFF1F2; color:#BE123C; border-color:#FECDD3; }
.mp-badge.error-fill { background:#DF1C41; color:#fff; border-color:#DF1C41; }
.mp-badge.sky      { background:#EFF8FF; color:#075985; border-color:#BAE6FD; }
.mp-badge.sky-fill { background:#0369A1; color:#fff; border-color:#0369A1; }
.mp-badge.neutral  { background:#F8FAFC; color:#475569; border-color:#CBD5E1; }
.mp-badge.neutral-fill { background:#334155; color:#fff; border-color:#334155; }
.mp-badge .dot { display:none; }

/* Buttons */
.mp-btn { display:inline-flex; align-items:center; gap:6px; font-family:'Inter Tight',sans-serif;
          font-weight:600; border-radius:8px; border:none; cursor:pointer; transition:all .15s;
          letter-spacing:0.01em; line-height:1; text-decoration:none; white-space:nowrap; }
.mp-btn.lg { padding:10px 18px; font-size:14px; }
.mp-btn.md { padding:8px 14px; font-size:13px; }
.mp-btn.sm { padding:6px 10px; font-size:12px; }
.mp-btn.primary     { background:#0B266E; color:#fff; box-shadow:0 2px 6px rgba(11,38,110,.22); }
.mp-btn.primary:hover { background:#091958; box-shadow:0 4px 12px rgba(11,38,110,.3); }
.mp-btn.secondary   { background:#fff; color:var(--c-fg-sec); border:1px solid var(--c-border);
                      box-shadow:0 1px 2px rgba(0,0,0,.04); }
.mp-btn.secondary:hover { background:var(--c-bg); border-color:var(--c-border-strong); }
.mp-btn.destructive { background:#DF1C41; color:#fff; }
.mp-btn.destructive:hover { background:#95122B; }
.mp-btn.ghost       { background:rgba(11,38,110,.08); color:#0B266E; }
.mp-btn.ghost:hover { background:rgba(11,38,110,.13); }

/* Stat cards */
.mp-stats-grid { display:grid; gap:14px; }
.mp-stats-grid.cols-2 { grid-template-columns:repeat(2,1fr); }
.mp-stats-grid.cols-3 { grid-template-columns:repeat(3,1fr); }
.mp-stats-grid.cols-4 { grid-template-columns:repeat(4,1fr); }
.mp-stat { background:#fff; border:1px solid var(--c-border); border-radius:14px; padding:16px 20px;
           box-shadow:var(--shadow-card); transition:border-color .15s, box-shadow .15s; }
.mp-stat:hover { border-color:var(--c-primary-border); box-shadow:0 4px 14px rgba(11,38,110,.07); }
.mp-stat-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center;
                justify-content:center; flex-shrink:0; }
.mp-stat-label { font-size:12px; font-weight:500; color:var(--c-fg-muted); margin-top:12px; margin-bottom:6px; }
.mp-stat-value { font-size:28px; font-weight:700; color:var(--c-fg); line-height:1; letter-spacing:-.02em; }
.mp-stat-sub   { font-size:11px; color:var(--c-fg-placeholder); margin-top:4px; }

/* Table / Card containers */
.mp-card { background:#fff; border:1px solid var(--c-border); border-radius:14px;
           box-shadow:var(--shadow-card); display:flex; flex-direction:column; width:100%; min-width:0; }
.mp-card > :first-child { border-top-left-radius: 13px; border-top-right-radius: 13px; }
.mp-card > :last-child { border-bottom-left-radius: 13px; border-bottom-right-radius: 13px; }
.mp-card-header { padding:14px 18px; background:#fff; border-bottom:1px solid var(--c-border);
                  display:flex; align-items:center; gap:10px; flex-shrink:0; }
.mp-card-title { font-size:15px; font-weight:700; color:var(--c-fg); }
.mp-card-header .right { margin-left:auto; display:flex; align-items:center; gap:8px; }
.mp-card-body  { flex:1; overflow-y:auto; }
.mp-card-body > :first-child { border-top-left-radius: 13px; border-top-right-radius: 13px; }
.mp-th { font-size:11px; font-weight:600; color:var(--c-fg-placeholder); text-transform:uppercase;
         letter-spacing:.06em; }
.mp-tr { border-bottom:1px solid #F8F9FB; }
.mp-tr:last-child { border-bottom:none; }
.mp-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
.mp-table { width:100%; border-collapse:collapse; }
.mp-table th { padding:10px 14px; text-align:left; font-size:11px; font-weight:600;
               color:var(--c-fg-placeholder); text-transform:uppercase; letter-spacing:.06em;
               background:#F8F9FB; border-bottom:1px solid var(--c-border); white-space:nowrap; }
.mp-table td { padding:10px 14px; text-align:left; font-size:13px; color:var(--c-fg);
               border-bottom:1px solid #F2F4F7; white-space:nowrap; vertical-align:middle; }
.mp-table tbody tr:last-child td { border-bottom:none; }
.mp-table tbody tr:hover td { background:#FAFBFC; }

/* Form inputs */
.mp-input { width:100%; padding:8px 12px; border:1px solid var(--c-border); border-radius:8px;
            font-size:13px; color:var(--c-fg); font-family:'Inter Tight',sans-serif; outline:none;
            transition:border-color .15s, box-shadow .15s; background:#fff; }
.mp-input:focus { border-color:#0B266E; box-shadow:0 0 0 3px rgba(11,38,110,.08); }
.mp-select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23666D80' stroke-width='2' stroke-linecap='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
             background-repeat:no-repeat; background-position:right 10px center; padding-right:30px; }

/* Alert / notice box */
.mp-alert { border-radius:10px; padding:12px 16px; font-size:13px; font-weight:500; }
.mp-alert.info    { background:#D1F0F9; color:#0C4D6E; border:1px solid rgba(16,106,151,.25); }
.mp-alert.warning { background:#F9ECCB; color:#5B3D1E; border:1px solid rgba(211,156,61,.3); }
.mp-alert.success { background:#DDF2EE; color:#174E43; border:1px solid rgba(64,196,170,.3); }

/* Divider */
.mp-divider { height:1px; background:var(--c-border); margin:0 -24px; flex-shrink:0; }

/* Section title — accent bar pattern (§6.2) */
.sec-head   { display:flex; align-items:center; gap:10px; flex-shrink:0; }
.sec-bar    { width:4px; height:22px; background:#0B266E; border-radius:19px; flex-shrink:0; }
.sec-title  { font-family:'Inter Tight',sans-serif; font-size:15px; font-weight:700; color:#0D0D12; white-space:nowrap; }
.sec-rule   { flex:1; height:1px; background:#DFE1E7; }
.sec-action { font-family:'Inter Tight',sans-serif; font-size:13px; font-weight:600; color:#0B266E; text-decoration:none; white-space:nowrap; }
.sec-action:hover { text-decoration:underline; }

/* Stat icon tone variants */
.mp-stat-icon.navy   { background:#EEF1FA; color:#0E1E54; }
.mp-stat-icon.sky    { background:#D1F0F9; color:#106A97; }
.mp-stat-icon.green  { background:#DDF2EE; color:#174E43; }
.mp-stat-icon.yellow { background:#F9ECCB; color:#956321; }
.mp-stat-icon.red    { background:#FADAE1; color:#95122B; }

/* Avatar (table/chip) */
.mp-av { width:28px; height:28px; border-radius:999px; display:grid; place-items:center;
         font:700 11px/1 'Inter Tight',sans-serif; flex-shrink:0; }
.mp-av.sm { width:22px; height:22px; font-size:9px; }
.mp-av.lg { width:36px; height:36px; font-size:13px; }
.mp-av.navy   { background:#EEF1FA; color:#0E1E54; }
.mp-av.sky    { background:#D1F0F9; color:#106A97; }
.mp-av.green  { background:#DDF2EE; color:#174E43; }
.mp-av.yellow { background:#F9ECCB; color:#956321; }
.mp-av.red    { background:#FADAE1; color:#95122B; }
.mp-av.violet { background:#E4DFFD; color:#6B39F4; }

/* Mobile overrides */
@media (max-width:767px) {
    .mp-wrap { padding:8px 8px 80px; }
    .mp-box  { border-radius:10px; }
    .mp-box-body { padding:14px; }
    .mp-stats-grid.cols-4 { grid-template-columns:repeat(2,1fr); }
    .mp-stats-grid.cols-3 { grid-template-columns:repeat(2,1fr); }
}
@media (max-width:480px) {
    .mp-stats-grid.cols-2,
    .mp-stats-grid.cols-3,
    .mp-stats-grid.cols-4 { grid-template-columns:repeat(2,1fr); }
}
</style>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/EOffice\resources/views/manajemen-praktikum/partials/_styles.blade.php ENDPATH**/ ?>