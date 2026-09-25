<style>
.sitkom-content:has(.edit-wrap){padding:0!important;display:flex;flex-direction:column;overflow:hidden}
.edit-wrap{display:flex;flex-direction:column;height:calc(100vh - 60px);padding:10px;min-width:0;font-family:'Inter Tight',sans-serif;color:var(--c-fg)}
.edit-box{display:flex;flex-direction:column;flex:1;min-height:0;background:#fff;border:1px solid var(--c-border);border-radius:12px;box-shadow:0 1px 3px #10182808;overflow:hidden}
.edit-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 24px;border-bottom:1px solid var(--c-border);flex-shrink:0}
.edit-header h1{font-size:22px;font-weight:700;line-height:1.3;margin:0}
.edit-header p{font-size:13px;line-height:1.5;margin:5px 0 0;color:var(--c-fg-muted);overflow-wrap:anywhere}
.edit-header-actions{display:flex;align-items:center;gap:8px;flex-shrink:0}
.edit-header-actions :is(a,button){min-height:36px;padding:8px 12px!important;border-radius:7px!important;white-space:nowrap}
.edit-header-actions button{background:var(--c-primary)!important;font-weight:600!important}
.edit-body{padding:20px 24px;overflow:auto;flex:1;min-height:0;display:flex;flex-direction:column;gap:16px}
.edit-section{border:1px solid var(--c-border);border-radius:10px;background:#fff;flex-shrink:0;min-width:0}
.edit-section-heading{padding:12px 16px;border-bottom:1px solid var(--c-border)}
.edit-section-heading h2{font-size:14px!important;font-weight:600!important;color:var(--c-fg)!important;margin-bottom:4px!important}
.edit-section-heading p{font-size:12px!important;font-weight:400!important;color:var(--c-fg-muted)!important}
.edit-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));padding:16px;gap:14px 20px}
.edit-wrap .input-group{min-width:0}
.edit-wrap .input-group label{display:block;font-size:12px;font-weight:500;color:var(--c-fg-sec);margin-bottom:6px}
.edit-wrap .input-field{width:100%;min-height:38px;padding:8px 11px;font-family:inherit;font-size:13px;color:var(--c-fg);background:#fff;border:1px solid var(--c-border);border-radius:7px;outline:none}
.edit-wrap .input-field:focus{border-color:var(--c-primary);box-shadow:0 0 0 3px #0b266e14}
.edit-access{padding:16px;min-width:0}
.edit-role-picker{max-width:560px;margin-bottom:16px}
.edit-role-picker>label{font-size:12px!important;font-weight:500!important;letter-spacing:normal!important;text-transform:none!important;color:var(--c-fg-sec)!important}
.edit-role-picker [role=button]:focus-visible{outline:2px solid var(--c-primary);outline-offset:2px}
.edit-role-picker [role=button]>div>div{max-width:100%;white-space:normal!important;overflow-wrap:anywhere}
.edit-modules{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.edit-modules .module-box{min-width:0;border-color:var(--c-border)!important;border-radius:9px!important;box-shadow:none!important}
.edit-modules .module-box>div:first-child{padding:9px 12px!important;background:var(--c-bg)!important;gap:8px;flex-wrap:wrap}
.edit-modules .module-box>div:first-child>div>span{font-size:13px!important;font-weight:600!important;overflow-wrap:anywhere}
.edit-permissions{padding:6px;display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,150px),1fr));gap:2px 6px}
.edit-permissions label{padding:4px 6px;border:1px solid transparent;border-radius:5px;min-width:0;min-height:28px;gap:6px!important;align-items:flex-start!important}
.edit-permissions label:has(input:checked){background:#eef2fa;border-color:#dbe3f5}
.edit-permissions label:hover{background:var(--c-bg)}
.edit-permissions input{flex-shrink:0;margin-top:2px}
.edit-permissions span{font-size:12px!important;font-weight:400!important;line-height:1.4;overflow-wrap:anywhere}
.edit-errors{padding:12px 16px;background:#fef3f2;border:1px solid #fecdca;color:#b42318;border-radius:8px;font-size:12px}
.edit-errors ul{list-style:disc;padding-left:18px;margin-top:6px}
.edit-wrap [x-cloak]{display:none!important}
.edit-wrap :is(a,button,input):focus-visible{outline:2px solid var(--c-primary);outline-offset:2px}
@media(max-width:767px){
 .sitkom-content:has(.edit-wrap){padding:8px!important;display:block;overflow:visible}
 .edit-wrap{height:auto;padding:0}.edit-box{overflow:visible}.edit-header{padding:14px;flex-direction:column;align-items:flex-start}.edit-header h1{font-size:20px}
 .edit-header-actions{width:100%}.edit-header-actions button{flex:1}.edit-header-actions :is(a,button){min-height:42px}
 .edit-body{padding:10px;overflow:visible;gap:12px}.edit-fields{grid-template-columns:1fr;padding:12px;gap:12px}.edit-fields>div:not(.input-group){display:none!important}
 .edit-section-heading,.edit-access{padding:12px}.edit-modules{grid-template-columns:1fr;gap:10px}.edit-wrap .input-field{min-height:42px;font-size:16px}
 .edit-permissions label{min-height:32px}.edit-role-picker{max-width:100%}
}
</style>
