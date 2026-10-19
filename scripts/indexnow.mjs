// Notify IndexNow (Bing, Yandex, Seznam, Naver, ...) that pages changed.
//
// Usage (from the project root, after the deploy is live):
//   node scripts/indexnow.mjs                      -> submit every URL in sitemap.xml
//   node scripts/indexnow.mjs Pages/tax-planning   -> submit specific pages (paths or full URLs)
//   node scripts/indexnow.mjs --dry-run ...        -> print the URLs without submitting
//
// The key file must be live at https://www.rubiqfinancial.com/<KEY>.txt.
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const HOST = 'www.rubiqfinancial.com';
const KEY = 'b05e9a454e4aeb34e7b88b7eaeac8f13';
const ENDPOINT = 'https://api.indexnow.org/indexnow';

const root = path.join(path.dirname(fileURLToPath(import.meta.url)), '..');
const args = process.argv.slice(2);
const dryRun = args.includes('--dry-run');
const targets = args.filter(a => a !== '--dry-run');

function toUrl(p) {
  if (/^https?:\/\//.test(p)) return p;
  const clean = p.replace(/^\/+/, '').replace(/\.html$/, '').replace(/^index$/, '');
  return `https://${HOST}/${clean}`;
}

const urlList = targets.length
  ? targets.map(toUrl)
  : [...fs.readFileSync(path.join(root, 'sitemap.xml'), 'utf8').matchAll(/<loc>([^<]+)<\/loc>/g)].map(m => m[1]);

if (!urlList.length) { console.error('No URLs to submit.'); process.exit(1); }
console.log(`${urlList.length} URL(s):\n  ${urlList.join('\n  ')}`);
if (dryRun) process.exit(0);

const keyCheck = await fetch(`https://${HOST}/${KEY}.txt`);
if (!keyCheck.ok || (await keyCheck.text()).trim() !== KEY) {
  console.error(`Key file not live at https://${HOST}/${KEY}.txt (status ${keyCheck.status}). Deploy it first.`);
  process.exit(1);
}

const res = await fetch(ENDPOINT, {
  method: 'POST',
  headers: { 'Content-Type': 'application/json; charset=utf-8' },
  body: JSON.stringify({ host: HOST, key: KEY, keyLocation: `https://${HOST}/${KEY}.txt`, urlList }),
});
// 200 = accepted, 202 = accepted (key validation pending); anything else is an error.
console.log(`IndexNow responded ${res.status} ${res.statusText}`);
const body = await res.text();
if (body) console.log(body);
process.exit(res.status === 200 || res.status === 202 ? 0 : 1);
