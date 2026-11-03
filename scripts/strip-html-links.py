#!/usr/bin/env python3
"""Rewrite internal href="....html" links to extensionless URLs.

Cloudflare Pages 308-redirects /page.html to /page, so linking to the
extensionless URL saves a redirect on every click and crawler hop.

  our-team.html          -> our-team
  Pages/schedule.html    -> Pages/schedule
  faq.html#fees          -> faq#fees
  ../index.html          -> ../
  index.html             -> ./
  https://www.rubiqfinancial.com/Pages/x.html -> https://www.rubiqfinancial.com/Pages/x

Left alone: other domains, mailto:/tel:/javascript:, anchors, PDFs, images,
CSS/JS and anything else not ending in .html.

Usage: python3 scripts/strip-html-links.py [--check] FILE...
  --check  report what would change, write nothing; exit 1 if anything would.
"""
import re
import sys

HREF = re.compile(r'href="([^"]*)"')
SITE = re.compile(r'^(https?://(?:www\.)?rubiqfinancial\.com)(/.*)?$', re.I)


def rewrite(url):
    if not url or url.startswith(("#", "mailto:", "tel:", "javascript:", "data:")):
        return url
    prefix = ""
    m = re.match(r"^[a-z][a-z0-9+.-]*:", url, re.I)
    if m or url.startswith("//"):
        site = SITE.match(url)
        if not site:
            return url  # external
        prefix, url = site.group(1), site.group(2) or "/"
    cut = len(url)
    for ch in "?#":
        i = url.find(ch)
        if i != -1:
            cut = min(cut, i)
    path, tail = url[:cut], url[cut:]
    if not path.lower().endswith(".html"):
        return prefix + url
    if path.lower() == "index.html" or path.lower().endswith("/index.html"):
        path = path[: -len("index.html")] or "./"
    else:
        path = path[: -len(".html")]
    return prefix + path + tail


def main(argv):
    check = "--check" in argv
    files = [a for a in argv if a != "--check"]
    total = changed_files = 0
    for f in files:
        src = open(f, encoding="utf-8").read()
        n = 0

        def sub(m):
            nonlocal n
            new = rewrite(m.group(1))
            if new != m.group(1):
                n += 1
            return 'href="' + new + '"'

        out = HREF.sub(sub, src)
        if n:
            total += n
            changed_files += 1
            if not check:
                with open(f, "w", encoding="utf-8", newline="") as fh:
                    fh.write(out)
    verb = "would rewrite" if check else "rewrote"
    print(f"{verb} {total} links in {changed_files} files")
    return 1 if (check and total) else 0


if __name__ == "__main__":
    sys.exit(main(sys.argv[1:]))
