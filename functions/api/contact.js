// Cloudflare Pages Function: POST /api/contact
// Emails website contact-form submissions to Andreas.
//
// Environment variables (Cloudflare Pages → Settings → Variables and Secrets):
//   RESEND_API_KEY    required. API key from resend.com (domain rubiqfinancial.com verified there).
//   CONTACT_TO        optional. Default: andreas@rubiqfinancial.com
//   CONTACT_FROM      optional. Default: Rubiq Website <website@rubiqfinancial.com>
//   TURNSTILE_SECRET  optional. If set, a Cloudflare Turnstile token is required.

const TO_DEFAULT = 'andreas@rubiqfinancial.com';
const FROM_DEFAULT = 'Rubiq Website <website@rubiqfinancial.com>';
const WHERE = {
  wirehouse: 'Wirehouse (Merrill, Morgan Stanley, UBS, Wells Fargo)',
  private_bank: 'Private bank',
  independent: 'Independent advisor',
  self: 'I manage it myself',
  other: 'Other',
  '': 'Not answered',
};

const json = (body, status = 200) =>
  new Response(JSON.stringify(body), { status, headers: { 'Content-Type': 'application/json', 'Cache-Control': 'no-store' } });

const clean = (v, max) => String(v ?? '').replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F]/g, '').trim().slice(0, max);

export async function onRequestPost({ request, env }) {
  let data;
  try {
    const type = request.headers.get('Content-Type') || '';
    data = type.includes('application/json') ? await request.json() : Object.fromEntries(await request.formData());
  } catch {
    return json({ ok: false, error: 'bad_request' }, 400);
  }

  // Spam checks: hidden field must stay empty; form must not be submitted instantly.
  if (clean(data.company_website, 200)) return json({ ok: true });
  const elapsed = Number(data.elapsed_ms || 0);
  if (!(elapsed >= 3000)) return json({ ok: false, error: 'too_fast' }, 400);

  if (env.TURNSTILE_SECRET) {
    const form = new FormData();
    form.append('secret', env.TURNSTILE_SECRET);
    form.append('response', clean(data['cf-turnstile-response'], 4096));
    form.append('remoteip', request.headers.get('CF-Connecting-IP') || '');
    const r = await fetch('https://challenges.cloudflare.com/turnstile/v0/siteverify', { method: 'POST', body: form });
    const v = await r.json().catch(() => ({}));
    if (!v.success) return json({ ok: false, error: 'verification_failed' }, 400);
  }

  const name = clean(data.name, 120);
  const email = clean(data.email, 200);
  const phone = clean(data.phone, 40);
  const preferred = clean(data.preferred, 20) === 'phone' ? 'Phone' : 'Email';
  const where = WHERE[clean(data.where, 20)] ?? WHERE.other;
  const message = clean(data.message, 3000);
  const page = clean(data.page, 300);

  if (!name || !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) return json({ ok: false, error: 'invalid' }, 400);
  if (preferred === 'Phone' && !phone) return json({ ok: false, error: 'phone_required' }, 400);

  if (!env.RESEND_API_KEY) return json({ ok: false, error: 'not_configured' }, 503);

  const text = [
    'New inquiry from rubiqfinancial.com',
    '',
    `Name: ${name}`,
    `Email: ${email}`,
    `Phone: ${phone || '(not given)'}`,
    `Prefers: ${preferred}`,
    `Accounts today: ${where}`,
    '',
    'Message:',
    message || '(none)',
    '',
    `Sent from: ${page || '/Pages/contact'}`,
    `Received: ${new Date().toISOString()}`,
  ].join('\n');

  const send = await fetch('https://api.resend.com/emails', {
    method: 'POST',
    headers: { Authorization: `Bearer ${env.RESEND_API_KEY}`, 'Content-Type': 'application/json' },
    body: JSON.stringify({
      from: env.CONTACT_FROM || FROM_DEFAULT,
      to: [env.CONTACT_TO || TO_DEFAULT],
      reply_to: email,
      subject: `Website inquiry: ${name}`,
      text,
    }),
  });
  if (!send.ok) return json({ ok: false, error: 'send_failed' }, 502);
  return json({ ok: true });
}

// Any other method (onRequestPost above takes precedence for POST).
export const onRequest = () => new Response('Method Not Allowed', { status: 405, headers: { Allow: 'POST' } });
