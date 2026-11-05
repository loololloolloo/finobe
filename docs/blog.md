# Finobe blog – source, theme and deployment

This code lives on the website branch alongside the Finobe app, not in a second blog service.

## Historical target and confidence

Requested visual target: the original Finobe blog (Wayback reference:
https://web.archive.org/web/20210125203354/https://blog.finobe.com/).

The exact archived homepage HTML, stylesheet, and assets were **not recoverable** from the available archive interface while doing this pass. Consequently **a pixel-identical 1:1 result has not been verified**. Do not describe these templates as a recovered original.

The public interface is a deliberate **2020 Ghost Casper 3.x-era reconstruction** instead of the former generic two-column blog. Historical Casper layout research establishes first and every sixth post use a large card, with other cards in a three-column feed. A contemporary reference was documented by Ghost's developer forum in January 2020:
https://forum.ghost.org/t/first-blog-post-on-homepage-wont-resize-correctly/11217

The 2020 design reference is NOT evidence that Finobe itself used an unmodified Casper theme. If the real Finobe homepage HTML/CSS/screenshot is recovered, compare it first and replace these assumptions with measured markup and asset paths.

## Functionality

Public:

- Root path – chronological posts, large feature card every sixth post, responsive feed
- /page/2/ – Ghost-style pagination
- /tag/{slug}/ and /tag/{slug}/page/2/ – tag archive
- /author/{slug}/ and /author/{slug}/page/2/ – author archive
- /{post-slug}/ – post detail, author/byline, Markdown, tags and next/previous
- /rss/ – latest published posts (RSS 2.0)

Only published posts whose published_at is not in the future are publicly visible. Drafts remain hidden. Article bodies render sanitized Markdown.

Admin continues at /admin/blog, with posts, drafts, publishing, authors, tags and blog settings. Blog data stays in the Laravel finobe database.

## Deployment

1. Run the blog and user-admin migrations with php artisan migrate --force. The migrations create the blog and admin-user tables.
2. Configure BLOG_URL=https://blog.finobe.wtf or the actual blog hostname in the app environment; the domain must route to the same Laravel application and the same public assets.
3. Clear Laravel caches with php artisan optimize:clear.
4. Test homepage, article, tags, authors, page 2, RSS and drafts on the blog hostname. Test the /admin/blog editor as a logged-in admin.
5. Run php artisan test --filter=FinobeBlogThemeTest as well as the full suite. These tests were authored in the repository, but weren't executed by the connector.

## Remaining accuracy work

Obtain a dated original screenshot plus its HTML/CSS and cover images from the January 2021 archive or an owned backup. Use screenshot overlays and responsive viewport comparisons before claiming 1:1 accuracy. Recover old posts and author metadata separately; do not invent original article content.
