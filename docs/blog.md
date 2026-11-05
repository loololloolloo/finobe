# Finobe blog restoration

This blog is implemented within the existing Laravel website on the website branch.

## Deployment

Run migrations with php artisan migrate --force, pointing at the existing finobe database. The new migrations add the blog and admin-user tables. Set BLOG_URL=https://blog.finobe.wtf and configure DNS and HTTPS so this hostname serves the same Laravel public/ directory. The administrative blog editor stays on the main website at /admin/blog.

Clear caches using php artisan optimize:clear. Test public homepage, tags, authors, a published article, post create/edit/publish, and permissions before deploying.

## Content origin

This is a new implementation, not a restored copy of the old Ghost/Casper blog source or its database. Existing blog posts are not migrated automatically. Its public appearance is a simple editorial style, not a verified pixel-identical recreation of the historical Finobe blog.

## Administration

/admin/blog lists drafts and published articles.
/admin/blog/authors controls author profiles.
/admin/blog/tags controls article tags.
/admin/blog/settings controls site name, description, logo, cover, community URL and accent color.

Article content uses sanitized Markdown. The existing navbar and game/RCC controllers remain unchanged.
