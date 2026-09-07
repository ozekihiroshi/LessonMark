import {copyFile, mkdir, readFile, writeFile} from 'node:fs/promises';
import {dirname, resolve} from 'node:path';
import {fileURLToPath} from 'node:url';

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const sourceRoot = resolve(projectRoot, 'node_modules', 'mermaid');
const targetRoot = resolve(projectRoot, 'plugin', 'lessonmark', 'vendor', 'mermaid');

await mkdir(targetRoot, {recursive: true});
const mermaidSource = await readFile(resolve(sourceRoot, 'dist', 'mermaid.min.js'), 'utf8');
// Mermaid contains bundled UMD dependencies which otherwise detect Moodle's
// RequireJS define.amd and register anonymous modules. Keep those checks local
// while still allowing Mermaid to publish its intended globalThis.mermaid API.
const upstreamExport = 'globalThis["mermaid"] = globalThis.__esbuild_esm_mermaid_nm["mermaid"].default;';
const isolatedExport = 'globalThis["mermaid"] = __esbuild_esm_mermaid_nm["mermaid"].default;';
const exportOffset = mermaidSource.indexOf(upstreamExport);
if (exportOffset === -1 || exportOffset !== mermaidSource.lastIndexOf(upstreamExport)) {
    throw new Error('Unexpected Mermaid global export wrapper.');
}
const scopedSource = mermaidSource.slice(0, exportOffset) + isolatedExport
    + mermaidSource.slice(exportOffset + upstreamExport.length);
const isolatedSource = `(function(define) {\n${scopedSource}\n}());\n`;
await writeFile(resolve(targetRoot, 'mermaid.min.js'), isolatedSource, 'utf8');
await copyFile(resolve(sourceRoot, 'LICENSE'), resolve(targetRoot, 'LICENSE'));
await copyFile(
    resolve(projectRoot, 'thirdparty-src', 'mermaid-render.js'),
    resolve(targetRoot, 'mermaid-render.js')
);

console.log('Built LessonMark Mermaid 11.17.2 assets.');
