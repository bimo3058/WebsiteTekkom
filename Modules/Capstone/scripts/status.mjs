import fs from 'node:fs';
import path from 'node:path';

const root=path.resolve(import.meta.dirname,'..');
const pages=JSON.parse(fs.readFileSync(path.join(root,'resources/reference/pages.json'),'utf8'));
const status=pages.map(page=>{
    const name=page.route.replace(/\[([^\]]+)\]/g,'_$1_').replace(/^\/|\/$/g,'') || 'home';
    const view=`resources/views/pages/${name}.blade.php`;
    return {route:page.route,source:page.source,view,status:['/login','/auth/exchange'].includes(page.route)?'session-redirect':['/','/mahasiswa/ta-defense'].includes(page.route)?'page-redirect':fs.existsSync(path.join(root,view))?'implemented':'pending'};
});
const counts=status.reduce((result,page)=>(result[page.status]=(result[page.status] || 0)+1,result),{});
fs.writeFileSync(path.join(root,'resources/reference/blade-status.json'),JSON.stringify({total:pages.length,counts,pages:status},null,2)+'\n');
console.log(JSON.stringify({total:pages.length,...counts},null,2));
