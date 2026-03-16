import { getDocument, GlobalWorkerOptions } from './node_modules/pdfjs-dist/legacy/build/pdf.mjs';
import { readFileSync } from 'fs';
import { fileURLToPath } from 'url';
import { resolve } from 'path';

import { createRequire } from 'module';
const require = createRequire(import.meta.url);
const workerPath = new URL('./node_modules/pdfjs-dist/legacy/build/pdf.worker.mjs', import.meta.url).href;
GlobalWorkerOptions.workerSrc = workerPath;

async function extractText(filePath) {
  const data = new Uint8Array(readFileSync(filePath));
  const pdf = await getDocument({ data, useWorkerFetch: false, isEvalSupported: false, useSystemFonts: true }).promise;
  let fullText = '';
  for (let i = 1; i <= pdf.numPages; i++) {
    const page = await pdf.getPage(i);
    const content = await page.getTextContent();
    const pageText = content.items.map(item => item.str).join(' ');
    fullText += `\n--- PAGE ${i} ---\n${pageText}`;
  }
  return fullText;
}

console.log('=== FEE SCHEDULE ===');
console.log(await extractText('./Media/AMW-Fee-Schedule-Template-WCA-Tier-II.pdf'));

console.log('\n\n=== STATEMENT OF SERVICE ===');
console.log(await extractText('./Media/Advisor-Statement-of-Service-AMW2025.pdf'));
