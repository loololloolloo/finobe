# Finobe administration restoration

Admin pages reuse the existing Finobe website framework. Administrator accounts with status=admin can access:

- /admin/users : user directory and search
- /admin/users/{id} : compact account summary with navigation on the top right
- Moderation, staff notes, Dius adjustments, account email updates and audit records
- /admin/servers : server listing, registration and removal
- /admin/blog : blog publishing, authors, tags and settings

The user management template deliberately avoids nested PHP Blade directives; empty profile blurbs and duplicate side-profile cards are not rendered.

Run php artisan migrate --force before accessing these pages; new notes, audit and blog tables are required. The legacy website and game/RCC routes are otherwise retained. Test these features before production rollout.
