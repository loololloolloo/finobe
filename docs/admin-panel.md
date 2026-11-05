# Finobe administration

The admin panel is at `/admin`. It uses the existing `v2/Modules/nav.twig` navbar, normal Finobe logo, and the site's light/night stylesheets. The entry page restores the original Finobe icon-card grid. Each category opens its existing controls, with a return link to the grid. Account moderation, 108 essential controls, asset review, and blog management remain available. There is no separate admin navbar, custom wordmark, or numbered sidebar.

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

`config/admin_controls.json` is the server-authoritative catalog. The catalog contains 108 essential controls. Duplicate asset-type copies, warning-reason presets, fixed currency amounts, color presets, and fun effects have been removed. Asset controls accept the asset ID regardless of type; staff permissions use a checked scope selector. They use shared validated handlers in `app/Admin/Workbench.php`.

Sections cover moderation, account restrictions, accounts, economy, catalog assets, places, avatar data, forums, messages, invites, announcements, site settings, staff permissions, audit history, and operations. The complete inventory is in [admin-controls.md](admin-controls.md).

Manage users at `/admin/users`: search by username or ID and open `/admin/users/{id}`. The dedicated page shows account details, moderation history, and only the account actions allowed by your staff scopes. Its controls prefill and lock the target to that account. Missing users return 404; unrelated staff scopes cannot access the directory or lookup API. Other tools require the content's own ID. Search filters the current section. Stars are local browser preferences. Reports paginate in batches of 50; JSON download exports the current result page.

Workbench writes require a reason. High-impact actions also require the confirmation checkbox. Each mutation and its audit record are committed together, and duplicate request IDs cannot double-apply a change. Passwords, tokens, session payloads, and IP fields are excluded from account inspection and audit snapshots. Session revocation supports the existing database-session setup and rotates the account token and remember token.

Warnings display a real acknowledgement page. Temporary and permanent bans block site requests; staff can lift them without deleting their history. Permanent suspension is labeled “Terminate account” but does not erase the account or its records.

Per-account restrictions and global feature pauses/cooldowns are checked by `AdminSitePolicy` on the existing website routes. Administrators can still manage paused features. Restrictions are website permissions; they do not add new engine-side in-game permissions.

Catalog approval and denial check asset files and move them through the configured CDN/review directories. Missing files produce an error instead of falsely reporting success. Texture dependencies explicitly identified by the asset's metadata are included. The handler does not guess dependencies from adjacent asset IDs.

Avatar controls inspect the existing avatar data, remove worn items, or reset the avatar. They do not replace meshes, lighting, or the renderer. Run the site's existing avatar render after a change to update its PNG. This work does not add an arbiter or a native Linux RCC worker.

## Verification

```sh
php vendor/bin/phpunit tests/Feature/AdminWorkbenchTest.php
php vendor/bin/phpunit --testsuite Unit
node --check public/s/admin/workbench.js
php artisan view:cache
```

The feature suite migrates an isolated SQLite database, executes every catalog entry against populated tables, and verifies authorization, permission revocation, missing-target checks, idempotency, warnings/acknowledgement, bans, restrictions, avatar edits, and user management routes. Production MySQL, a live CDN, and real RCC output require deployment verification. No production user data is used in the tests or screenshots.

The original repository's unrelated default homepage example test assumes a running MySQL/Redis website; it is not part of the isolated admin feature suite.

Local browser verification uses a disposable database and preview-only authentication outside the tracked repository. Those preview routes and fixtures are not shipped.

Third-party navbar dependencies: jQuery 3.6.0 (MIT) and Bootstrap 4.6.2 bundle (MIT), stored locally under `public/s/admin`. Their license headers are preserved. These support the existing navbar's dropdowns and mobile collapse.

Blog publishing is available under **Admin → Blog**. See [blog.md](blog.md) for its setup, public routes, and editor workflow.
