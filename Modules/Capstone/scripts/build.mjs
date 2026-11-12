import path from 'node:path';
import fs from 'node:fs';
import { createRequire } from 'node:module';
const root = path.resolve(import.meta.dirname, '../../..');
const moduleRoot = path.join(root, 'Modules/Capstone');
const require = createRequire(path.join(moduleRoot, 'package.json'));
const postcss = require('postcss');
const tailwind = require('@tailwindcss/postcss');
const esbuild = require('esbuild');
const cssFile = path.join(moduleRoot, 'resources/assets/css/app.css');
fs.mkdirSync(path.join(moduleRoot, 'public/build'), { recursive: true });
// 1) Bundle JS (Phosphor CSS import emits a sibling app.css — capture it separately).
await esbuild.build({entryPoints:[path.join(moduleRoot,'resources/assets/js/app.js')],bundle:true,minify:true,format:'iife',outfile:path.join(moduleRoot,'public/build/app.js'),target:['es2020'],loader:{'.woff2':'file','.woff':'file','.ttf':'file','.svg':'file'},assetNames:'fonts/[name]-[hash]'});
let phosphorCss = '';
try { phosphorCss = fs.readFileSync(path.join(moduleRoot, 'public/build/app.css'), 'utf8'); } catch {}
// 2) Build Tailwind CSS, then append Phosphor CSS so one app.css serves both.
const css = await postcss([tailwind({base:root,optimize:true})]).process(fs.readFileSync(cssFile,'utf8'), {from:cssFile});
fs.writeFileSync(path.join(moduleRoot,'public/build/app.css'), css.css + '\n' + phosphorCss);
console.log('Capstone Blade assets built in Modules/Capstone/public/build');
