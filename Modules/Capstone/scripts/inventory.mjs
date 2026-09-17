import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import { createRequire } from 'node:module';

const root = path.resolve(import.meta.dirname, '../../..');
const moduleRoot = path.join(root, 'Modules/Capstone');
const require = createRequire(path.join(root, 'frontend/package.json'));
const ts = require('typescript');
function walk(dir) { return fs.readdirSync(dir, { withFileTypes: true }).flatMap(e => e.isDirectory() ? walk(path.join(dir, e.name)) : [path.join(dir, e.name)]); }
const sources = walk(path.join(root, 'frontend/src'));
const assets = walk(path.join(root, 'frontend/public'));
const relative = p => path.relative(root, p).replaceAll('\\', '/');
const hash = p => crypto.createHash('sha256').update(fs.readFileSync(p)).digest('hex');
const manifest = sources.filter(p => p.endsWith('/page.tsx') || p.endsWith('\\page.tsx')).map(file => {
    const source = fs.readFileSync(file, 'utf8');
    const ast = ts.createSourceFile(file, source, ts.ScriptTarget.Latest, true, ts.ScriptKind.TSX);
    return { route: relative(file).replace('frontend/src/app', '').replace('/page.tsx', '') || '/', source: relative(file), imports: ast.statements.filter(ts.isImportDeclaration).map(s => s.moduleSpecifier.text) };
});
fs.mkdirSync(path.join(moduleRoot, 'resources/reference'), { recursive: true });
fs.writeFileSync(path.join(moduleRoot, 'resources/reference/pages.json'), JSON.stringify(manifest, null, 2) + '\n');
const snapshotFile = path.join(moduleRoot, 'resources/reference/frontend-hashes.json');
const snapshot = Object.fromEntries([...sources, ...assets].map(p => [relative(p), hash(p)]));
if (!fs.existsSync(snapshotFile)) fs.writeFileSync(snapshotFile, JSON.stringify(snapshot, null, 2) + '\n');
else {
    const old = JSON.parse(fs.readFileSync(snapshotFile));
    const changed = Object.keys({...old, ...snapshot}).filter(k => old[k] !== snapshot[k]);
    if (changed.length) throw new Error('Frontend reference changed: ' + changed.join(', '));
}
console.log(JSON.stringify({ pages: manifest.length, sources: sources.length, assets: assets.length, referenceUnchanged: true }));
