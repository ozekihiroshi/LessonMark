import {copyFile, mkdir, readdir} from 'node:fs/promises';
import {dirname, resolve} from 'node:path';
import {fileURLToPath} from 'node:url';
import {build} from 'esbuild';

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const katexRoot = resolve(projectRoot, 'node_modules', 'katex');
const asciiMathRoot = resolve(projectRoot, 'node_modules', 'asciimath-parser');
const pluginRoot = resolve(projectRoot, 'plugin', 'lessonmark');
const katexTarget = resolve(pluginRoot, 'vendor', 'katex');
const mathTarget = resolve(pluginRoot, 'vendor', 'math');

await mkdir(resolve(katexTarget, 'fonts'), {recursive: true});
await mkdir(mathTarget, {recursive: true});

await build({
    entryPoints: [resolve(projectRoot, 'thirdparty-src', 'math-render-source.js')],
    bundle: true,
    minify: true,
    format: 'iife',
    target: ['es2018'],
    legalComments: 'none',
    outfile: resolve(mathTarget, 'math-render.min.js')
});

await copyFile(resolve(katexRoot, 'dist', 'katex.min.css'), resolve(katexTarget, 'katex.min.css'));
await copyFile(resolve(katexRoot, 'LICENSE'), resolve(katexTarget, 'LICENSE'));
await copyFile(resolve(asciiMathRoot, 'LICENSE'), resolve(mathTarget, 'ASCIIMATH-LICENSE'));

for (const filename of await readdir(resolve(katexRoot, 'dist', 'fonts'))) {
    await copyFile(resolve(katexRoot, 'dist', 'fonts', filename), resolve(katexTarget, 'fonts', filename));
}

console.log('Built LessonMark KaTeX 0.18.5 and asciimath-parser 0.6.11 assets.');
