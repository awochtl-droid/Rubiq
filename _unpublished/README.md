# Temporarily unpublished pages (Nov 20, 2026)

Pulled from the live site at Andreas's request: every page added since Oct 15, 2026
(39 pages, listed in `_redirects`), plus the buffered ETF backtest CSV files (`data/`).
Nothing was deleted. The contact form and thank-you pages stay live.

To restore a page: `git mv _unpublished/<name>.html Pages/<name>.html`, delete its line in
`_redirects`, and re-add it to sitemap.xml, Pages/sitemap.html, Pages/insights.html and the
related-link spots. The unpublish commits can be reverted with `git revert`.
