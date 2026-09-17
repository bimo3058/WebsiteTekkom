import fs from 'node:fs';
import path from 'node:path';
import {spawn} from 'node:child_process';

const root=path.resolve(import.meta.dirname,'..');
let running=false,pending=false,timer;
function build(){
    if(running){pending=true;return;}
    running=true;
    const child=spawn(process.execPath,[path.join(root,'scripts/build.mjs')],{cwd:root,stdio:'inherit',windowsHide:true});
    child.on('error',error=>console.error(error.message));
    child.on('close',()=>{running=false;if(pending){pending=false;build();}});
}
fs.watch(path.join(root,'resources'),{recursive:true},()=>{
    clearTimeout(timer);timer=setTimeout(build,150);
});
build();
console.log('Watching Capstone Blade templates and assets. Refresh the Laravel page after each build.');
