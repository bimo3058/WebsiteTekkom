<style>
.sitkom-content:has(.show-wrap){padding:0!important;display:flex;flex-direction:column;overflow:hidden}
.show-wrap{display:flex;flex-direction:column;height:calc(100vh - 60px);padding:10px;min-width:0;font-family:'Inter Tight',sans-serif;color:var(--c-fg)}
.show-box{display:flex;flex-direction:column;flex:1;min-height:0;background:#fff;border:1px solid var(--c-border);border-radius:12px;box-shadow:0 1px 3px #10182808;overflow:hidden}
.show-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 24px;border-bottom:1px solid var(--c-border);flex-shrink:0}
.show-heading{display:flex;align-items:center;gap:12px;min-width:0}.show-heading>a{flex-shrink:0}
.show-heading h1{font-size:22px;font-weight:700;line-height:1.3;margin:0}.show-heading p{font-size:13px;line-height:1.5;margin:5px 0 0;color:var(--c-fg-muted)}
.show-actions{display:flex;align-items:center;gap:8px;flex-shrink:0}
.show-actions :is(a,button){display:inline-flex;align-items:center;justify-content:center;min-height:36px;padding:8px 12px!important;border-radius:7px!important;font-family:inherit;font-size:12px!important;font-weight:600!important;white-space:nowrap}
.show-actions>a{background:var(--c-primary)!important}.show-actions>button{background:#fff!important;color:#b42318!important;border:1px solid #fecdca!important}
.show-body{padding:20px 24px;overflow:auto;flex:1;min-height:0;display:flex;flex-direction:column;gap:16px}
.show-profile,.show-access{border:1px solid var(--c-border);border-radius:10px;flex-shrink:0;min-width:0;background:#fff}
.show-profile{padding:20px}.show-profile-row{display:flex;align-items:flex-start;gap:20px}.show-profile-content{flex:1;min-width:0}
.show-profile-title{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:6px}.show-profile-title h2{font-size:20px!important;font-weight:600!important;overflow-wrap:anywhere;min-width:0}
.show-statuses{display:flex;gap:6px;flex-wrap:wrap}.show-wrap .badge-outline{display:inline-flex;align-items:center;gap:5px;padding:3px 7px;border-radius:5px;font-size:10px;font-weight:500;white-space:nowrap}.show-wrap .dot{width:5px;height:5px;border-radius:50%;flex-shrink:0}
.show-profile-content>p{font-size:12px!important;color:var(--c-fg-muted)!important;overflow-wrap:anywhere}
.show-info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 24px;padding-top:14px;border-top:1px solid var(--c-border)}
.show-info-grid>div{flex-direction:column;align-items:flex-start!important;gap:5px;min-width:0}.show-info-grid>div>div{max-width:100%}
.show-wrap .info-label{font-size:11px;color:var(--c-fg-muted);font-weight:400}.show-wrap .info-value{font-size:13px;font-weight:500;color:var(--c-fg);overflow-wrap:anywhere;max-width:100%}
.show-section-heading{padding:12px 16px;border-bottom:1px solid var(--c-border)}.show-section-heading h3{font-size:14px!important;font-weight:600!important;margin-bottom:4px!important}.show-section-heading p{font-size:12px!important;font-weight:400!important;color:var(--c-fg-muted)!important}
.show-access-content{padding:16px}.show-modules{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.show-modules .module-card{border:1px solid var(--c-border);border-radius:9px;overflow:hidden;min-width:0}
.show-modules .module-card>div:first-child{padding:9px 12px!important;background:var(--c-bg)!important;gap:8px;flex-wrap:wrap}.show-modules .module-card>div:first-child>div{min-width:0}
.show-modules .module-card>div:first-child>div>span{font-size:13px!important;font-weight:600!important;letter-spacing:normal!important;text-transform:none!important;overflow-wrap:anywhere}
.show-permissions{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,150px),1fr));gap:2px 6px;padding:6px}
.show-permissions>div{padding:4px 6px;min-height:28px;min-width:0;align-items:flex-start!important;border:1px solid transparent;border-radius:5px;gap:6px!important}
.show-permissions>div:has(input:checked){background:#eef2fa;border-color:#dbe3f5}.show-permissions input{flex-shrink:0;margin-top:2px}.show-permissions span{font-size:12px!important;font-weight:400!important;line-height:1.4;overflow-wrap:anywhere}
.show-wrap :is(a,button):focus-visible{outline:2px solid var(--c-primary);outline-offset:2px}
body:has(.show-wrap) #modalSuspend{z-index:1100}
@media(max-width:767px){
 .sitkom-content:has(.show-wrap){display:block;overflow:visible;padding:8px!important}.show-wrap{height:auto;padding:0}.show-box{overflow:visible}
 .show-header{padding:14px;align-items:flex-start;flex-direction:column}.show-heading h1{font-size:20px}.show-actions{width:100%;flex-wrap:wrap}.show-actions :is(a,button){min-height:42px}.show-actions>a{flex:1}
 .show-body{padding:10px;overflow:visible;gap:12px}.show-profile{padding:14px}.show-profile-row{flex-direction:column;gap:12px}.show-profile-content{width:100%}
 .show-info-grid{grid-template-columns:1fr;gap:12px}.show-section-heading,.show-access-content{padding:12px}.show-modules{grid-template-columns:1fr;gap:10px}
}
</style>
