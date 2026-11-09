<style>
 .sitkom-content:has(.perm-wrap){padding:0!important;display:flex;flex-direction:column;overflow:hidden}
 .perm-wrap{display:flex;flex-direction:column;height:calc(100vh - 60px);padding:10px;min-width:0;font-family:'Inter Tight',sans-serif;color:var(--c-fg)}
 .perm-box{display:flex;flex-direction:column;flex:1;min-height:0;background:#fff;border:1px solid var(--c-border);border-radius:12px;box-shadow:0 1px 3px #10182808;overflow:hidden}
 .perm-header{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 20px;border-bottom:1px solid var(--c-border);flex-shrink:0}
 .perm-header h1{font-size:22px;font-weight:700;line-height:1.3;margin:0}
 .perm-header p{font-size:13px;color:var(--c-fg-muted);margin:4px 0 0;line-height:1.5}
 .perm-header-actions{display:flex;align-items:center;gap:8px}
 .perm-header-actions :is(a,button){min-height:36px;padding:8px 12px!important;border-radius:7px!important;white-space:nowrap}
 .perm-header-actions button{background:var(--c-primary)!important;font-weight:600!important}
 .perm-body{flex:1;min-height:0;overflow:auto;padding:14px 20px;display:flex;flex-direction:column;gap:12px}
 .perm-section{border:1px solid var(--c-border);border-radius:10px;background:#fff;min-width:0;flex-shrink:0}
 .perm-section-heading{padding:10px 12px;border-bottom:1px solid var(--c-border)}
 .perm-section-heading>p:first-child{font-size:14px!important;font-weight:600!important;color:var(--c-fg)!important}
 .perm-section-heading>p:last-child{font-size:12px!important;font-weight:400!important;color:var(--c-fg-muted)!important}
 .perm-section-heading br{display:none}
 .perm-section-content{padding:12px;min-width:0}
 .perm-module-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
 .perm-module-grid .module-box{border:1px solid var(--c-border)!important;border-radius:9px!important;box-shadow:none!important;min-width:0}
 .perm-module-grid .module-box>div:first-child{padding:8px 10px!important;background:var(--c-bg)!important;gap:8px;flex-wrap:wrap}
 .perm-module-grid .module-box>div:first-child>div{min-width:0}
 .perm-module-grid .module-box>div:first-child>div>span{font-size:13px!important;font-weight:600!important;overflow-wrap:anywhere}
 .perm-options{padding:6px;display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,150px),1fr));gap:2px 6px}
 .perm-options label{padding:4px 6px;border:1px solid transparent;border-radius:5px;min-width:0;min-height:28px;gap:6px!important;align-items:flex-start!important}
 .perm-options label:has(input:checked){background:#eef2fa;border-color:#dbe3f5}
 .perm-options label:hover{background:var(--c-bg)}
 .perm-options input{flex-shrink:0;margin-top:2px}
 .perm-options span{font-size:12px!important;font-weight:400!important;line-height:1.4;overflow-wrap:anywhere}
 .perm-users{display:flex;flex-direction:column;gap:10px}
 .perm-search-form{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
 .perm-search-form>button{background:var(--c-primary)!important}
 .perm-search-form>button,.perm-search-form>div>button,.perm-search-form input[type=text]{min-height:36px;font-size:12px!important;border-radius:7px!important}
 .perm-table-scroll{overflow-x:auto}
 .perm-table-scroll th{background:var(--c-bg)!important;font-size:11px!important;color:var(--c-fg-muted)!important;padding-top:9px!important;padding-bottom:9px!important}
 .perm-table-scroll tbody tr:hover{background:#fafbfc}
 .perm-wrap [x-cloak]{display:none!important}
 .perm-wrap :is(a,button,input,[tabindex]):focus-visible{outline:2px solid var(--c-primary);outline-offset:2px}
 .perm-table-scroll td{padding-top:10px!important;padding-bottom:10px!important}
 @media(max-width:767px){
  .sitkom-content:has(.perm-wrap){display:block;overflow:visible;padding:8px!important}
  .perm-wrap{height:auto;padding:0}.perm-box{overflow:visible}.perm-header{padding:12px;flex-direction:column;align-items:flex-start}
  .perm-header h1{font-size:20px}.perm-header-actions{width:100%}.perm-header-actions button{flex:1}
  .perm-header-actions :is(a,button){min-height:42px}.perm-body{padding:10px;overflow:visible;gap:10px}
  .perm-section-heading,.perm-section-content{padding:10px}.perm-module-grid{grid-template-columns:1fr;gap:8px}
  .perm-options label{min-height:32px;align-items:center!important}.perm-search-form{width:100%}
  .perm-search-form>div:has(input[name=search]){order:-1;width:100%}.perm-search-form input[name=search]{width:100%!important;min-height:42px;font-size:14px!important}
  .perm-search-form>button,.perm-search-form>div>button{min-height:42px}.perm-search-form>button{flex:1}
 }
</style>
