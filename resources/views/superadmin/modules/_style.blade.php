<style>
.sitkom-content:has(.mod-wrap){padding:0!important;display:flex;flex-direction:column;overflow:hidden}
.mod-wrap{display:flex;flex-direction:column;height:calc(100vh - 60px);padding:10px;min-width:0;font-family:'Inter Tight',sans-serif;color:var(--c-fg)}
.mod-box{display:flex;flex-direction:column;flex:1;min-height:0;background:#fff;border:1px solid var(--c-border);border-radius:12px;box-shadow:0 1px 3px #10182808;overflow:hidden}
.mod-box-header{padding:16px 24px;border-bottom:1px solid var(--c-border);flex-shrink:0}
.mod-box-header h1{font-size:22px!important;font-weight:700!important;line-height:1.3!important}
.mod-box-header p{font-size:13px!important;line-height:1.5;margin:5px 0 0!important}
.mod-box-header a{min-height:36px;border-radius:7px!important}
.mod-box-body{padding:20px 24px;overflow:auto;flex:1;min-height:0;display:flex;flex-direction:column;gap:16px}
.mod-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}
.mod-card{border-radius:10px!important;min-width:0}.mod-card>div{display:flex;flex-direction:column;height:100%;padding:12px!important}.mod-card>div>div:first-child{margin-bottom:10px!important}
.mod-card h3{font-size:14px!important;font-weight:600!important;white-space:normal!important;overflow-wrap:anywhere;margin:0 0 6px!important}
.mod-card h3+p{font-size:11px!important;line-height:1.5!important;min-height:32px;margin:0 0 10px!important}
.mod-card h3+p+div{margin-top:auto}.mod-card h3+p+div>div{border-radius:6px!important;padding:6px!important;min-width:0}.mod-card h3+p+div>div p:first-child{font-size:10px!important;font-weight:500!important;text-transform:none!important;letter-spacing:normal!important;color:var(--c-fg-muted)!important}.mod-card h3+p+div>div p:last-child{font-size:12px!important;overflow-wrap:anywhere}
.mod-manage{min-height:36px;border-radius:7px!important;font-size:12px!important;font-weight:600!important;letter-spacing:normal!important;text-transform:none!important}
.mod-modal{z-index:1100!important;font-family:'Inter Tight',sans-serif}
.mod-modal>div:last-child{max-width:520px!important;max-height:calc(100dvh - 32px);overflow-y:auto!important;border-radius:12px!important;overscroll-behavior:contain}
.mod-modal h3{font-size:18px!important;margin:0}.mod-modal label{font-size:12px!important;text-transform:none!important;letter-spacing:normal!important;font-weight:500!important}
.mod-modal input:not([type=checkbox]){min-height:38px;border-radius:7px!important;min-width:0}.mod-modal button{min-height:36px;letter-spacing:normal!important;text-transform:none!important;font-weight:600!important}
.mod-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.mod-alert{padding:12px 16px;border:1px solid #abefc6;background:#ecfdf3;color:#067647;font-size:13px;border-radius:8px}.mod-alert-error{background:#fef3f2;border-color:#fecdca;color:#b42318}
.mod-empty{grid-column:1 / -1;padding:40px;text-align:center;border:1px dashed var(--c-border);border-radius:10px;color:var(--c-fg-muted);font-size:13px}
.mod-wrap :is(button,a):focus-visible,.mod-modal :is(input,button):focus-visible{outline:2px solid var(--c-primary);outline-offset:2px}
@media(max-width:1100px){.mod-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:767px){
 .sitkom-content:has(.mod-wrap){display:block;overflow:visible;padding:8px!important}.mod-wrap{height:auto;padding:0}.mod-box{overflow:visible}
 .mod-box-header{padding:14px}.mod-box-header h1{font-size:20px!important}.mod-box-body{padding:12px;overflow:visible;gap:14px}
 .mod-grid{grid-template-columns:1fr;gap:12px}.mod-manage{min-height:42px}
 .mod-form-grid{grid-template-columns:1fr}.mod-modal input:not([type=checkbox]){font-size:16px!important;min-height:42px}.mod-modal button{min-height:42px}
 .mod-modal form>div:last-child{gap:8px;flex-wrap:wrap;padding:12px!important}.mod-modal form>div:last-child button{flex:1;justify-content:center;white-space:nowrap}
}
</style>
