import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
const root = path.resolve(import.meta.dirname,'../../..');
const source = fs.readFileSync(path.join(root,'frontend/src/components/layout/AppSidebar.tsx'),'utf8');
const navigation = source.match(/const navItems:[\s\S]*?= (\{[\s\S]*?\n\});/)[1].replace(/icon: (\w+)/g,'icon: "$1"');
fs.writeFileSync(path.join(root,'Modules/Capstone/resources/reference/navigation.json'),JSON.stringify(vm.runInNewContext('('+navigation+')'),null,2)+'\n');
