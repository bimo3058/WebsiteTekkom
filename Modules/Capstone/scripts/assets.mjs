import fs from 'node:fs';
import path from 'node:path';
import { createRequire } from 'node:module';
import vm from 'node:vm';

const root = path.resolve(import.meta.dirname, '../../..');
const target = path.join(root, 'Modules/Capstone');
const require = createRequire(path.join(root, 'frontend/package.json'));
const ts = require('typescript');
const walk = dir => fs.readdirSync(dir, {withFileTypes:true}).flatMap(e => e.isDirectory() ? walk(path.join(dir,e.name)) : [path.join(dir,e.name)]);
fs.mkdirSync(path.join(target, 'public'), {recursive:true});
fs.cpSync(path.join(root, 'frontend/public'), path.join(target, 'public'), {recursive:true});
const css = fs.readFileSync(path.join(root,'frontend/src/app/globals.css'),'utf8')
    .replace('@import "tailwindcss";', '@import "tailwindcss" source(none);\n@source "../../views";\n@source "../js";\n@source "../../reference/classes.txt";')
    .replace('@import "tw-animate-css";', '@import "./animations.css";')
    .replace('@import "shadcn/tailwind.css";', '@import "./shadcn.css";');
fs.mkdirSync(path.join(target, 'resources/assets/css'), {recursive:true});
fs.writeFileSync(path.join(target, 'resources/assets/css/theme.css'), css);
for (const [source, dest] of [['tw-animate-css/dist/tw-animate.css','animations.css'],['shadcn/dist/tailwind.css','shadcn.css']]) {
    const file = path.join(root,'frontend/node_modules',source);
    if (!fs.existsSync(file)) throw new Error('Missing asset dependency: '+file);
    fs.copyFileSync(file, path.join(target,'resources/assets/css',dest));
}
const classes = new Set();
const iconNames = new Set();
for (const file of walk(path.join(root,'frontend/src')).filter(p => /\.[jt]sx?$/.test(p))) {
    const ast = ts.createSourceFile(file,fs.readFileSync(file,'utf8'),ts.ScriptTarget.Latest,true,ts.ScriptKind.TSX);
    const visit = n => {
        if (ts.isStringLiteral(n) || ts.isNoSubstitutionTemplateLiteral(n)) classes.add(n.text);
        if (ts.isImportDeclaration(n) && n.moduleSpecifier.text === 'lucide-react') {
            for (const item of n.importClause?.namedBindings?.elements ?? []) iconNames.add((item.propertyName ?? item.name).text);
        }
        ts.forEachChild(n,visit);
    };
    visit(ast);
}
fs.writeFileSync(path.join(target,'resources/reference/classes.txt'),[...classes].join('\n'));
const iconDir = path.join(root,'frontend/node_modules/lucide-react/dist/esm/icons');
const icons = {};
for (const file of fs.readdirSync(iconDir).filter(p=>p.endsWith('.js'))) {
    const source = fs.readFileSync(path.join(iconDir,file),'utf8');
    const match = source.match(/(?:const|var) (\w+) = createLucideIcon\("([^"]+)", ([\s\S]*?)\);/);
    if (!match) continue;
    const expression = match[3] === '__iconNode' ? source.match(/(?:export )?const __iconNode = ([\s\S]*?);/)[1] : match[3];
    const nodes = vm.runInNewContext('('+expression+')');
    icons[match[1]] = nodes;
    icons[match[2]] = nodes;
}
fs.writeFileSync(path.join(target,'resources/reference/icons.json'),JSON.stringify(icons));
console.log(JSON.stringify({assetsCopied:9,icons:Object.keys(icons).length,theme:'original Next.js tokens'}));
