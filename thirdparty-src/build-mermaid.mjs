import {copyFile, mkdir} from 'node:fs/promises';
import {dirname, resolve} from 'node:path';
import {fileURLToPath} from 'node:url';

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const sourceRoot = resolve(projectRoot, 'node_modules', 'mermaid');
const targetRoot = resolve(projectRoot, 'plugin', 'lessonmark', 'vendor', 'mermaid');

await mkdir(targetRoot, {recursive: true});
await copyFile(resolve(sourceRoot, 'dist', 'mermaid.min.js'), resolve(targetRoot, 'mermaid.min.js'));
await copyFile(resolve(sourceRoot, 'LICENSE'), resolve(targetRoot, 'LICENSE'));
await copyFile(
    resolve(projectRoot, 'thirdparty-src', 'mermaid-render.js'),
    resolve(targetRoot, 'mermaid-render.js')
);

console.log('Built LessonMark Mermaid 11.17.2 assets.');
