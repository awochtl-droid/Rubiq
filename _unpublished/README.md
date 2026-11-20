# Temporarily unpublished pages (Nov 20, 2026)

These pages were pulled from the live site at Andreas's request (everything published from
"SBLOC Rates: How a Securities-Based Line of Credit Works" onward). Nothing was deleted.

To restore a page: `git mv _unpublished/<name>.html Pages/<name>.html`, delete its line in
`_redirects`, and re-add it to sitemap.xml, Pages/sitemap.html, Pages/insights.html and the
related-link spots (see the unpublish commit's diff, which can be reverted with `git revert`).
