<style>
    .sitkom-content:has(.rp-wrap){padding:0!important;display:flex;flex-direction:column;overflow:hidden}
    .rp-wrap{height:calc(100vh - 60px);padding:10px;display:flex;flex-direction:column;min-width:0;color:var(--c-fg);font-family:'Inter Tight',sans-serif}
    .rp-box{display:flex;flex-direction:column;flex:1;min-height:0;background:#fff;border:1px solid var(--c-border);border-radius:12px;box-shadow:0 1px 3px #10182808;overflow:hidden}
    .rp-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:20px 24px;border-bottom:1px solid var(--c-border)}
    .rp-header h1{font-size:22px;font-weight:700;line-height:1.3;margin:0}
    .rp-header p{font-size:13px;color:var(--c-fg-muted);margin:6px 0 0;line-height:1.6}
    .rp-body{padding:20px 24px;overflow:auto;min-height:0;flex:1;display:flex;flex-direction:column;gap:20px}
    .rp-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}
    .rp-stat{padding:16px 18px;border:1px solid var(--c-border);border-radius:10px;background:#fff}
    .rp-stat>span{font-size:12px;font-weight:500;color:var(--c-fg-sec)}
    .rp-stat strong{display:block;font-size:28px;line-height:1.3;font-weight:700;margin:10px 0 6px;color:var(--c-primary)}
    .rp-stat p{font-size:11px;line-height:1.5;color:var(--c-fg-muted);margin:0}
    .rp-table-card{border:1px solid var(--c-border);border-radius:10px;background:#fff;flex-shrink:0}
    .rp-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 16px;flex-wrap:wrap}
    .rp-toolbar h2{font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;margin:0}
    .rp-count{font-size:11px;padding:2px 7px;background:var(--c-bg);border:1px solid var(--c-border);border-radius:5px}
    .rp-filters{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .rp-button{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:36px;padding:8px 12px;background:#fff;border:1px solid var(--c-border);border-radius:7px;font-family:inherit;font-size:12px;font-weight:600;color:var(--c-fg-sec);text-decoration:none;white-space:nowrap;cursor:pointer}
    .rp-button:hover{background:var(--c-bg)}
    .rp-primary{background:var(--c-primary);border-color:var(--c-primary);color:#fff}
    .rp-primary:hover{background:#153b8e}
    .rp-search{display:flex;align-items:center;gap:8px;border:1px solid var(--c-border);padding:0 10px;border-radius:7px;color:var(--c-fg-muted);min-width:0}
    .rp-search input{width:180px;min-width:0;min-height:34px;border:0;padding:6px 0;background:none;font:inherit;font-size:12px;color:var(--c-fg);outline:none;box-shadow:none}
    .rp-search:focus-within{outline:2px solid var(--c-primary);outline-offset:2px}
    .rp-sort{position:relative}
    .rp-sort-menu{position:absolute;right:0;top:calc(100% + 6px);z-index:40;width:180px;padding:5px;border:1px solid var(--c-border);border-radius:8px;background:#fff;box-shadow:0 8px 24px #1018281a}
    .rp-sort-menu a,.rp-action-menu a{display:block;padding:10px;border-radius:5px;font-family:'Inter Tight',sans-serif;font-size:12px;line-height:1.5;text-decoration:none;color:var(--c-fg-sec)}
    .rp-sort-menu a:hover,.rp-sort-menu a[aria-current],.rp-action-menu a:hover{background:var(--c-bg);color:var(--c-primary)}
    .rp-action-menu{position:fixed;inset:auto;margin:0;width:190px;max-height:calc(100dvh - 16px);overflow:auto;padding:5px;background:#fff;border:1px solid var(--c-border);border-radius:8px;box-shadow:0 8px 24px #1018281a}
    .rp-table-scroll{overflow-x:auto;border-top:1px solid var(--c-border)}
    .rp-table{width:100%;min-width:720px;text-align:left;border-collapse:collapse}
    .rp-table th{padding:12px 16px;font-size:11px;font-weight:600;color:var(--c-fg-muted);background:var(--c-bg)}
    .rp-table td{padding:16px;border-top:1px solid var(--c-border);vertical-align:middle}
    .rp-table tbody tr:hover{background:#fafbfc}
    .rp-table th:last-child,.rp-table td:last-child{width:72px;text-align:center}
    .rp-role-name{font-size:13px;font-weight:600;color:var(--c-fg);text-decoration:none}
    .rp-role-name:hover{color:var(--c-primary)}
    .rp-role-meta{display:flex;align-items:center;gap:7px;margin-top:7px;flex-wrap:wrap}
    .rp-muted{font-size:11px;color:var(--c-fg-muted);margin:0}
    .rp-access-count{font-size:12px;font-weight:500;color:var(--c-fg-sec)}
    .rp-access-count>span{margin:0 5px;color:var(--c-fg-muted)}
    .rp-tags{display:flex;flex-wrap:wrap;gap:5px;margin-top:8px;max-width:380px}
    .rp-tags>span{font-size:10px;color:var(--c-fg-sec);border:1px solid var(--c-border);padding:3px 6px;border-radius:5px;background:#fff}
    .rp-tags b{font-weight:500;margin-left:4px;color:var(--c-fg-muted)}
    .rp-user-count{font-size:15px;font-weight:600;color:var(--c-primary);text-decoration:none}
    .rp-user-count span{font-size:11px;font-weight:400;color:var(--c-fg-muted)}
    .rp-more{padding:8px;width:36px}
    .rp-pagination>div{border-radius:0 0 10px 10px}
    .rp-pagination>div>div{flex-wrap:wrap;max-width:100%}
    .rp-empty{text-align:center;padding:36px 12px}
    .rp-empty h3{font-size:15px;font-weight:600;margin:0}
    .rp-empty p{font-size:12px;color:var(--c-fg-muted);margin:8px 0 16px}
    .rp-wrap [x-cloak]{display:none!important}
    .rp-wrap :is(button,a,[tabindex]):focus-visible,.rp-action-menu a:focus-visible{outline:2px solid var(--c-primary);outline-offset:2px}
    @media(max-width:1100px){.rp-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:767px){
        .sitkom-content:has(.rp-wrap){padding:8px!important;display:block;overflow:visible}
        .rp-wrap{height:auto;padding:0}.rp-box{overflow:visible}.rp-header{padding:16px;align-items:flex-start;flex-direction:column}.rp-header h1{font-size:20px}
        .rp-body{padding:12px;overflow:visible;gap:14px}.rp-stats{gap:8px}.rp-stat{padding:12px}.rp-stat strong{font-size:24px}
        .rp-toolbar{padding:12px}.rp-filters{width:100%}.rp-search{width:100%}.rp-search input{width:100%;min-height:42px;font-size:14px}
        .rp-button{min-height:42px}.rp-sort{flex:1}.rp-sort>.rp-button{width:100%}.rp-sort-menu{left:0;right:auto}
        .rp-pagination>div{padding:12px!important}.rp-pagination a,.rp-pagination button{min-height:36px}
    }
</style>
