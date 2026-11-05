# Finobe administration

The admin panel is at `/admin`. It uses the existing `v2/Modules/nav.twig` navbar, normal Finobe logo, and the site's light/night stylesheets. The entry page restores the original Finobe icon-card grid. Each category opens its existing controls, with a return link to the grid. Account moderation, all 528 controls/presets, asset review, and blog management remain available. There is no separate admin navbar, custom wordmark, or numbered sidebar.

## Install

Use the repository's existing PHP 8.2+, Composer, MySQL, and Redis setup. Configure the `finobe` database connection in `.env` before migrating. Back up an existing database first.

```sh
composer install
php artisan migrate --database=finobe --force
php artisan view:clear
```

The additive admin migration creates case notes, audit records, delegated permissions, feature restrictions, feature settings, and reversible profile decorations. It also supplies missing users columns that the source dump's controllers already reference. Existing columns are preserved.

An existing account with `users.status = 'admin'` can open the panel. Set `ADMIN_OWNER_IDS` to comma-separated owner user IDs to restrict who can change administrator accounts or delegate permissions. If omitted, existing full administrators retain owner authority. Delegated staff receive only their granted sections and cannot promote themselves.

Optional `.env` configuration:

```dotenv
ADMIN_OWNER_IDS=1
FINOBE_CDN_PATH=/var/www/cdn.finobe.wtf
```

Use your actual owner IDs and CDN path. No accounts or passwords are seeded into a live site by this change.

## Controls

`config/admin_controls.json` is the server-authoritative catalog. The 528 entries are controls and presets, not 526 independent subsystems. For example, ban durations, warning reasons, asset-type workflows, and native avatar colors have individual entries. They use shared validated handlers in `app/Admin/Workbench.php`.

Sections cover moderation, account restrictions, accounts, economy, catalog assets, places, avatar data, forums, messages, invites, videos, announcements, site settings, staff permissions, audit history, operations, and reversible profile effects. The complete inventory is in [admin-controls.md](admin-controls.md).

Account lookup accepts a username or user ID. Other tools require the content's own ID. Content tools verify the asset type before applying changes. Search filters the current section. Stars are local browser preferences. Reports paginate in batches of 50; JSON download exports the current result page.

Workbench writes require a reason. High-impact actions also require the confirmation checkbox. Each mutation and its audit record are committed together, and duplicate request IDs cannot double-apply a change. Passwords, tokens, session payloads, and IP fields are excluded from account inspection and audit snapshots. Session revocation supports the existing database-session setup and rotates the account token and remember token.

Warnings display a real acknowledgement page. Temporary and permanent bans block site requests; staff can lift them without deleting their history. Permanent suspension is labeled “Terminate account” but does not erase the account or its records.

Per-account restrictions and global feature pauses/cooldowns are checked by `AdminSitePolicy` on the existing website routes. Administrators can still manage paused features. Restrictions are website permissions; they do not add new engine-side in-game permissions.

Catalog approval and denial check asset files and move them through the configured CDN/review directories. Missing files produce an error instead of falsely reporting success. Texture dependencies explicitly identified by the asset's metadata are included. The handler does not guess dependencies from adjacent asset IDs.

Avatar controls edit the existing avatar data and native body-color IDs. They do not replace meshes, lighting, or the renderer. Run the site's existing avatar render after a change to update its PNG. This work does not add an arbiter or a native Linux RCC worker.

Fun controls store a profile title, accent, or presentation effect. They appear in both profile templates and have corresponding clear/reset controls. They do not execute user-provided client code or create fake moderation actions.

## Verification

```sh
php vendor/bin/phpunit tests/Feature/AdminWorkbenchTest.php
php vendor/bin/phpunit --testsuite Unit
node --check public/s/admin/workbench.js
php artisan view:cache
```

The feature suite migrates an isolated SQLite database, executes every catalog entry against populated tables, and verifies authorization, permission revocation, target-type checks, idempotency, warnings/acknowledgement, bans, restrictions, avatar edits, and reversible profile changes. Production MySQL, a live CDN, and real RCC output require deployment verification. No production user data is used in the tests or screenshots.

The original repository's unrelated default homepage example test assumes a running MySQL/Redis website; it is not part of the isolated admin feature suite.

Local browser verification uses a disposable database and preview-only authentication outside the tracked repository. Those preview routes and fixtures are not shipped.

Third-party navbar dependencies: jQuery 3.6.0 (MIT) and Bootstrap 4.6.2 bundle (MIT), stored locally under `public/s/admin`. Their license headers are preserved. These support the existing navbar's dropdowns and mobile collapse.

Blog publishing is available under **Admin → Blog**. See [blog.md](blog.md) for its setup, public routes, and editor workflow.
