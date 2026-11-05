# Admin control inventory

528 registered controls and presets. All IDs are validated on the server.

## Moderation

| Control | Target | Operation |
| --- | --- | --- |
| Ban for 1 day | user | `ban` |
| Ban for 3 days | user | `ban` |
| Ban for 7 days | user | `ban` |
| Ban for 14 days | user | `ban` |
| Ban for 30 days | user | `ban` |
| Ban for 90 days | user | `ban` |
| Ban for 180 days | user | `ban` |
| Ban for 365 days | user | `ban` |
| Issue a warning | user | `warn` |
| Send a reminder | user | `remind` |
| Terminate account | user | `ban` |
| Lift active bans | user | `unban` |
| Dismiss active warnings | user | `unwarn` |
| Add a private case note | user | `note` |
| Read moderation history | user | `history` |
| View private case notes | user | `notes` |
| Revoke login sessions | user | `sessions` |
| Rotate API token | user | `token` |
| Warn for harassment | user | `warn` |
| Warn for spam | user | `warn` |
| Warn for scamming | user | `warn` |
| Warn for impersonation | user | `warn` |
| Warn for inappropriate content | user | `warn` |
| Warn for personal information | user | `warn` |
| Warn for exploiting | user | `warn` |
| Warn for ban evasion | user | `warn` |
| Warn for offsite advertising | user | `warn` |
| Warn for threats | user | `warn` |
| Warn for disruptive behavior | user | `warn` |
| Warn for misleading assets | user | `warn` |
| Active bans | bans | `report` |
| Permanent bans | bans | `report` |
| Warnings awaiting acknowledgement | warning | `report` |
| Moderation overturned | bans | `report` |

## Access restrictions

| Control | Target | Operation |
| --- | --- | --- |
| Block forum posting | user | `restriction` |
| Restore forum posting | user | `restriction` |
| Block private messages | user | `restriction` |
| Restore private messages | user | `restriction` |
| Block asset uploads | user | `restriction` |
| Restore asset uploads | user | `restriction` |
| Block trading | user | `restriction` |
| Restore trading | user | `restriction` |
| Block purchases | user | `restriction` |
| Restore purchases | user | `restriction` |
| Block avatar changes | user | `restriction` |
| Restore avatar changes | user | `restriction` |
| Block avatar rendering | user | `restriction` |
| Restore avatar rendering | user | `restriction` |
| Block profile changes | user | `restriction` |
| Restore profile changes | user | `restriction` |
| Block invite creation | user | `restriction` |
| Restore invite creation | user | `restriction` |
| Block video uploads | user | `restriction` |
| Restore video uploads | user | `restriction` |
| Block place editing | user | `restriction` |
| Restore place editing | user | `restriction` |
| Block comments | user | `restriction` |
| Restore comments | user | `restriction` |
| Block friend requests | user | `restriction` |
| Restore friend requests | user | `restriction` |
| Block election entries | user | `restriction` |
| Restore election entries | user | `restriction` |
| Block account changes | user | `restriction` |
| Restore account changes | user | `restriction` |

## Accounts

| Control | Target | Operation |
| --- | --- | --- |
| Edit username | user | `field` |
| Edit email | user | `field` |
| Edit profile blurb | user | `field` |
| Set place slots | user | `field` |
| Set profile picture URL | user | `field` |
| Set daily Diu date | user | `field` |
| Verify email | user | `field` |
| Require email verification | user | `field` |
| Pause daily Dius | user | `field` |
| Resume daily Dius | user | `field` |
| Clear profile blurb | user | `field` |
| Use default profile picture | user | `field` |
| Switch to classic theme | user | `field` |
| Switch to modern theme | user | `field` |
| Use Finobe branding | user | `field` |
| Use original Finobe logo | user | `field` |
| Use light theme | user | `field` |
| Use dark theme | user | `field` |
| Newest accounts | user | `report` |
| Administrators | user | `report` |
| Unverified accounts | user | `report` |
| Verified accounts | user | `report` |
| Daily Diu holds | user | `report` |
| Classic theme accounts | user | `report` |
| Modern theme accounts | user | `report` |
| Accounts with no blurb | user | `report` |
| Accounts with no place slots | user | `report` |
| Recently active accounts | user | `report` |
| Dormant accounts | user | `report` |
| Inspect account | user | `inspect` |
| Export account record | user | `export` |
| Read account changes | user | `target_audit` |

## Economy

| Control | Target | Operation |
| --- | --- | --- |
| Set Diu balance | user | `balance` |
| Credit Dius | user | `credit` |
| Debit Dius | user | `debit` |
| Grant 25 Dius | user | `credit` |
| Grant 50 Dius | user | `credit` |
| Grant 100 Dius | user | `credit` |
| Grant 250 Dius | user | `credit` |
| Grant 500 Dius | user | `credit` |
| Grant 1000 Dius | user | `credit` |
| Wealthiest accounts | user | `report` |
| Zero balance accounts | user | `report` |
| Negative balance accounts | user | `report` |
| Recent transactions | purchases | `report` |
| Free grants | purchases | `report` |
| Paid transactions | purchases | `report` |
| Debits | purchases | `report` |
| Inspect transaction | purchases | `inspect` |
| Export transaction | purchases | `export` |
| Grant inventory item | user | `inventory_grant` |
| Revoke inventory item | user | `inventory_revoke` |

## Catalog

| Control | Target | Operation |
| --- | --- | --- |
| Image: inspect | assets | `inspect` |
| Image: export record | assets | `export` |
| Image: edit title | assets | `field` |
| Image: edit description | assets | `field` |
| Image: send to review | assets | `field` |
| Image: view review queue | assets | `report` |
| Image: view published items | assets | `report` |
| Image: view denied items | assets | `report` |
| Image: set thumbnail url | assets | `json` |
| T-shirt: inspect | assets | `inspect` |
| T-shirt: export record | assets | `export` |
| T-shirt: edit title | assets | `field` |
| T-shirt: edit description | assets | `field` |
| T-shirt: send to review | assets | `field` |
| T-shirt: view review queue | assets | `report` |
| T-shirt: view published items | assets | `report` |
| T-shirt: view denied items | assets | `report` |
| T-shirt: take off sale | assets | `json` |
| T-shirt: put on sale | assets | `json` |
| T-shirt: set price | assets | `json` |
| T-shirt: set thumbnail url | assets | `json` |
| Audio: inspect | assets | `inspect` |
| Audio: export record | assets | `export` |
| Audio: edit title | assets | `field` |
| Audio: edit description | assets | `field` |
| Audio: send to review | assets | `field` |
| Audio: view review queue | assets | `report` |
| Audio: view published items | assets | `report` |
| Audio: view denied items | assets | `report` |
| Audio: take off sale | assets | `json` |
| Audio: put on sale | assets | `json` |
| Audio: set price | assets | `json` |
| Audio: set thumbnail url | assets | `json` |
| Mesh: inspect | assets | `inspect` |
| Mesh: export record | assets | `export` |
| Mesh: edit title | assets | `field` |
| Mesh: edit description | assets | `field` |
| Mesh: send to review | assets | `field` |
| Mesh: view review queue | assets | `report` |
| Mesh: view published items | assets | `report` |
| Mesh: view denied items | assets | `report` |
| Mesh: set thumbnail url | assets | `json` |
| Hat: inspect | assets | `inspect` |
| Hat: export record | assets | `export` |
| Hat: edit title | assets | `field` |
| Hat: edit description | assets | `field` |
| Hat: send to review | assets | `field` |
| Hat: view review queue | assets | `report` |
| Hat: view published items | assets | `report` |
| Hat: view denied items | assets | `report` |
| Hat: take off sale | assets | `json` |
| Hat: put on sale | assets | `json` |
| Hat: set price | assets | `json` |
| Hat: set thumbnail url | assets | `json` |
| Shirt: inspect | assets | `inspect` |
| Shirt: export record | assets | `export` |
| Shirt: edit title | assets | `field` |
| Shirt: edit description | assets | `field` |
| Shirt: send to review | assets | `field` |
| Shirt: view review queue | assets | `report` |
| Shirt: view published items | assets | `report` |
| Shirt: view denied items | assets | `report` |
| Shirt: take off sale | assets | `json` |
| Shirt: put on sale | assets | `json` |
| Shirt: set price | assets | `json` |
| Shirt: set thumbnail url | assets | `json` |
| Pants: inspect | assets | `inspect` |
| Pants: export record | assets | `export` |
| Pants: edit title | assets | `field` |
| Pants: edit description | assets | `field` |
| Pants: send to review | assets | `field` |
| Pants: view review queue | assets | `report` |
| Pants: view published items | assets | `report` |
| Pants: view denied items | assets | `report` |
| Pants: take off sale | assets | `json` |
| Pants: put on sale | assets | `json` |
| Pants: set price | assets | `json` |
| Pants: set thumbnail url | assets | `json` |
| Decal: inspect | assets | `inspect` |
| Decal: export record | assets | `export` |
| Decal: edit title | assets | `field` |
| Decal: edit description | assets | `field` |
| Decal: send to review | assets | `field` |
| Decal: view review queue | assets | `report` |
| Decal: view published items | assets | `report` |
| Decal: view denied items | assets | `report` |
| Decal: set thumbnail url | assets | `json` |
| Face: inspect | assets | `inspect` |
| Face: export record | assets | `export` |
| Face: edit title | assets | `field` |
| Face: edit description | assets | `field` |
| Face: send to review | assets | `field` |
| Face: view review queue | assets | `report` |
| Face: view published items | assets | `report` |
| Face: view denied items | assets | `report` |
| Face: take off sale | assets | `json` |
| Face: put on sale | assets | `json` |
| Face: set price | assets | `json` |
| Face: set thumbnail url | assets | `json` |
| Gear: inspect | assets | `inspect` |
| Gear: export record | assets | `export` |
| Gear: edit title | assets | `field` |
| Gear: edit description | assets | `field` |
| Gear: send to review | assets | `field` |
| Gear: view review queue | assets | `report` |
| Gear: view published items | assets | `report` |
| Gear: view denied items | assets | `report` |
| Gear: take off sale | assets | `json` |
| Gear: put on sale | assets | `json` |
| Gear: set price | assets | `json` |
| Gear: set thumbnail url | assets | `json` |
| Approve asset | assets | `asset_approve` |
| Deny asset | assets | `asset_deny` |

## Places

| Control | Target | Operation |
| --- | --- | --- |
| Place: inspect | assets | `inspect` |
| Place: export record | assets | `export` |
| Place: edit title | assets | `field` |
| Place: edit description | assets | `field` |
| Place: send to review | assets | `field` |
| Place: view review queue | assets | `report` |
| Place: view published items | assets | `report` |
| Place: view denied items | assets | `report` |
| Place: set thumbnail url | assets | `json` |
| Feature place | assets | `json` |
| Unfeature place | assets | `json` |
| Set place client version | assets | `json` |
| Set place capacity | assets | `json` |
| Clear place description | assets | `field` |
| All places | assets | `report` |
| 2012 places | assets | `report` |
| 2016 places | assets | `report` |
| Featured places | assets | `report` |

## Avatar

| Control | Target | Operation |
| --- | --- | --- |
| Head: bright yellow | user | `avatar_color` |
| Head: white | user | `avatar_color` |
| Head: black | user | `avatar_color` |
| Head: bright red | user | `avatar_color` |
| Head: bright blue | user | `avatar_color` |
| Head: dark green | user | `avatar_color` |
| Head: br. yellowish green | user | `avatar_color` |
| Head: medium stone grey | user | `avatar_color` |
| Head: dark stone grey | user | `avatar_color` |
| Head: medium blue | user | `avatar_color` |
| Head: nougat | user | `avatar_color` |
| Head: reddish brown | user | `avatar_color` |
| Torso: bright yellow | user | `avatar_color` |
| Torso: white | user | `avatar_color` |
| Torso: black | user | `avatar_color` |
| Torso: bright red | user | `avatar_color` |
| Torso: bright blue | user | `avatar_color` |
| Torso: dark green | user | `avatar_color` |
| Torso: br. yellowish green | user | `avatar_color` |
| Torso: medium stone grey | user | `avatar_color` |
| Torso: dark stone grey | user | `avatar_color` |
| Torso: medium blue | user | `avatar_color` |
| Torso: nougat | user | `avatar_color` |
| Torso: reddish brown | user | `avatar_color` |
| Left arm: bright yellow | user | `avatar_color` |
| Left arm: white | user | `avatar_color` |
| Left arm: black | user | `avatar_color` |
| Left arm: bright red | user | `avatar_color` |
| Left arm: bright blue | user | `avatar_color` |
| Left arm: dark green | user | `avatar_color` |
| Left arm: br. yellowish green | user | `avatar_color` |
| Left arm: medium stone grey | user | `avatar_color` |
| Left arm: dark stone grey | user | `avatar_color` |
| Left arm: medium blue | user | `avatar_color` |
| Left arm: nougat | user | `avatar_color` |
| Left arm: reddish brown | user | `avatar_color` |
| Right arm: bright yellow | user | `avatar_color` |
| Right arm: white | user | `avatar_color` |
| Right arm: black | user | `avatar_color` |
| Right arm: bright red | user | `avatar_color` |
| Right arm: bright blue | user | `avatar_color` |
| Right arm: dark green | user | `avatar_color` |
| Right arm: br. yellowish green | user | `avatar_color` |
| Right arm: medium stone grey | user | `avatar_color` |
| Right arm: dark stone grey | user | `avatar_color` |
| Right arm: medium blue | user | `avatar_color` |
| Right arm: nougat | user | `avatar_color` |
| Right arm: reddish brown | user | `avatar_color` |
| Left leg: bright yellow | user | `avatar_color` |
| Left leg: white | user | `avatar_color` |
| Left leg: black | user | `avatar_color` |
| Left leg: bright red | user | `avatar_color` |
| Left leg: bright blue | user | `avatar_color` |
| Left leg: dark green | user | `avatar_color` |
| Left leg: br. yellowish green | user | `avatar_color` |
| Left leg: medium stone grey | user | `avatar_color` |
| Left leg: dark stone grey | user | `avatar_color` |
| Left leg: medium blue | user | `avatar_color` |
| Left leg: nougat | user | `avatar_color` |
| Left leg: reddish brown | user | `avatar_color` |
| Right leg: bright yellow | user | `avatar_color` |
| Right leg: white | user | `avatar_color` |
| Right leg: black | user | `avatar_color` |
| Right leg: bright red | user | `avatar_color` |
| Right leg: bright blue | user | `avatar_color` |
| Right leg: dark green | user | `avatar_color` |
| Right leg: br. yellowish green | user | `avatar_color` |
| Right leg: medium stone grey | user | `avatar_color` |
| Right leg: dark stone grey | user | `avatar_color` |
| Right leg: medium blue | user | `avatar_color` |
| Right leg: nougat | user | `avatar_color` |
| Right leg: reddish brown | user | `avatar_color` |
| Inspect avatar data | user | `avatar_inspect` |
| Export avatar data | user | `avatar_export` |
| Unequip hats | user | `avatar_clear_hats` |
| Unequip gear | user | `avatar_clear_gear` |
| Unequip shirt | user | `avatar_clear_shirt` |
| Unequip pants | user | `avatar_clear_pants` |
| Unequip T-shirt | user | `avatar_clear_tshirt` |
| Unequip face | user | `avatar_clear_face` |
| Reset avatar to classic noob | user | `avatar_noob` |

## Forum

| Control | Target | Operation |
| --- | --- | --- |
| Thread: inspect | forum_threads | `inspect` |
| Thread: export | forum_threads | `export` |
| Thread: edit body | forum_threads | `field` |
| Thread: replace body with moderation notice | forum_threads | `field` |
| Thread: show recent posts | forum_threads | `report` |
| Thread: show edited posts | forum_threads | `report` |
| Reply: inspect | forum_replies | `inspect` |
| Reply: export | forum_replies | `export` |
| Reply: edit body | forum_replies | `field` |
| Reply: replace body with moderation notice | forum_replies | `field` |
| Reply: show recent posts | forum_replies | `report` |
| Reply: show edited posts | forum_replies | `report` |
| Lock thread | forum_threads | `field` |
| Unlock thread | forum_threads | `field` |
| Pin thread | forum_threads | `field` |
| Unpin thread | forum_threads | `field` |
| Mark thread edited | forum_threads | `field` |
| Clear edited marker | forum_threads | `field` |
| Edit thread title | forum_threads | `field` |
| Move thread to category | forum_threads | `field` |
| Stick reply | forum_replies | `field` |
| Unstick reply | forum_replies | `field` |
| Locked threads | forum_threads | `report` |
| Pinned threads | forum_threads | `report` |
| Open threads | forum_threads | `report` |
| Sticky replies | forum_replies | `report` |

## Messages

| Control | Target | Operation |
| --- | --- | --- |
| Send staff message | user | `message` |
| Send account reminder | user | `message` |
| Send appeal decision | user | `message` |
| Message: inspect | messages | `inspect` |
| Message: show recent | messages | `report` |
| Message: show unread | messages | `report` |
| Message: mark read | messages | `field` |
| Message: mark unread | messages | `field` |
| Message: edit text | messages | `field` |
| Notification: inspect | pms | `inspect` |
| Notification: show recent | pms | `report` |
| Notification: show unread | pms | `report` |
| Notification: mark read | pms | `field` |
| Notification: mark unread | pms | `field` |
| Notification: edit text | pms | `field` |
| Archive message | messages | `field` |
| Unarchive message | messages | `field` |
| Edit message subject | messages | `field` |
| Archived messages | messages | `report` |
| Inbox messages | messages | `report` |

## Invites

| Control | Target | Operation |
| --- | --- | --- |
| Create an invite | user | `invite` |
| Create five invites | user | `invite` |
| Create ten invites | user | `invite` |
| All invite keys | invitekeys | `report` |
| Unused invite keys | invitekeys | `report` |
| Used invite keys | invitekeys | `report` |
| Inspect invite | invitekeys | `inspect` |
| Revoke unused invite | invitekeys | `invite_revoke` |

## Videos

| Control | Target | Operation |
| --- | --- | --- |
| Edit video title | videos | `field` |
| Edit video description | videos | `field` |
| Set video thumbnail | videos | `field` |
| Clear video description | videos | `field` |
| Inspect video | videos | `inspect` |
| Export video record | videos | `export` |
| Recent videos | videos | `report` |

## Announcements

| Control | Target | Operation |
| --- | --- | --- |
| Create announcement | announcements | `announcement` |
| Inspect announcement | announcements | `inspect` |
| Recent announcements | announcements | `report` |
| Edit announcement text | announcements | `field` |
| Set announcement expiry | announcements | `field` |
| Use success announcement style | announcements | `field` |
| Use primary announcement style | announcements | `field` |
| Use danger announcement style | announcements | `field` |
| Use info announcement style | announcements | `field` |
| Use warning announcement style | announcements | `field` |
| Expire announcement now | announcements | `expire` |
| Extend announcement by 1 days | announcements | `extend` |
| Extend announcement by 3 days | announcements | `extend` |
| Extend announcement by 7 days | announcements | `extend` |
| Extend announcement by 14 days | announcements | `extend` |
| Extend announcement by 30 days | announcements | `extend` |

## Site settings

| Control | Target | Operation |
| --- | --- | --- |
| Pause forum posting | none | `site_switch` |
| Resume forum posting | none | `site_switch` |
| Pause private messages | none | `site_switch` |
| Resume private messages | none | `site_switch` |
| Pause asset uploads | none | `site_switch` |
| Resume asset uploads | none | `site_switch` |
| Pause trading | none | `site_switch` |
| Resume trading | none | `site_switch` |
| Pause purchases | none | `site_switch` |
| Resume purchases | none | `site_switch` |
| Pause avatar changes | none | `site_switch` |
| Resume avatar changes | none | `site_switch` |
| Pause avatar rendering | none | `site_switch` |
| Resume avatar rendering | none | `site_switch` |
| Pause profile changes | none | `site_switch` |
| Resume profile changes | none | `site_switch` |
| Pause invite creation | none | `site_switch` |
| Resume invite creation | none | `site_switch` |
| Pause video interactions | none | `site_switch` |
| Resume video interactions | none | `site_switch` |
| Pause place editing | none | `site_switch` |
| Resume place editing | none | `site_switch` |
| Pause comments | none | `site_switch` |
| Resume comments | none | `site_switch` |
| Pause friend requests | none | `site_switch` |
| Resume friend requests | none | `site_switch` |
| Pause election entries | none | `site_switch` |
| Resume election entries | none | `site_switch` |
| Pause account changes | none | `site_switch` |
| Resume account changes | none | `site_switch` |
| Pause registration | none | `site_switch` |
| Resume registration | none | `site_switch` |
| Pause new logins | none | `site_switch` |
| Resume new logins | none | `site_switch` |
| Set forum posting cooldown | none | `cooldown` |
| Set private messages cooldown | none | `cooldown` |
| Set asset uploads cooldown | none | `cooldown` |
| Set trading cooldown | none | `cooldown` |
| Set purchases cooldown | none | `cooldown` |
| Set avatar changes cooldown | none | `cooldown` |
| Set avatar rendering cooldown | none | `cooldown` |
| Set profile changes cooldown | none | `cooldown` |
| Set invite creation cooldown | none | `cooldown` |
| Set video uploads cooldown | none | `cooldown` |
| Set place editing cooldown | none | `cooldown` |
| Set comments cooldown | none | `cooldown` |
| Set friend requests cooldown | none | `cooldown` |
| Set election entries cooldown | none | `cooldown` |
| Set account changes cooldown | none | `cooldown` |

## Staff

| Control | Target | Operation |
| --- | --- | --- |
| Grant moderation access | user | `staff_scope` |
| Revoke moderation access | user | `staff_scope` |
| Grant access restrictions access | user | `staff_scope` |
| Revoke access restrictions access | user | `staff_scope` |
| Grant accounts access | user | `staff_scope` |
| Revoke accounts access | user | `staff_scope` |
| Grant economy access | user | `staff_scope` |
| Revoke economy access | user | `staff_scope` |
| Grant catalog access | user | `staff_scope` |
| Revoke catalog access | user | `staff_scope` |
| Grant places access | user | `staff_scope` |
| Revoke places access | user | `staff_scope` |
| Grant avatar access | user | `staff_scope` |
| Revoke avatar access | user | `staff_scope` |
| Grant forum access | user | `staff_scope` |
| Revoke forum access | user | `staff_scope` |
| Grant messages access | user | `staff_scope` |
| Revoke messages access | user | `staff_scope` |
| Grant invites access | user | `staff_scope` |
| Revoke invites access | user | `staff_scope` |
| Grant videos access | user | `staff_scope` |
| Revoke videos access | user | `staff_scope` |
| Grant announcements access | user | `staff_scope` |
| Revoke announcements access | user | `staff_scope` |
| Grant site settings access | user | `staff_scope` |
| Revoke site settings access | user | `staff_scope` |
| Grant staff access | user | `staff_scope` |
| Revoke staff access | user | `staff_scope` |
| Grant audit access | user | `staff_scope` |
| Revoke audit access | user | `staff_scope` |
| Grant operations access | user | `staff_scope` |
| Revoke operations access | user | `staff_scope` |
| Grant fun access | user | `staff_scope` |
| Revoke fun access | user | `staff_scope` |
| List delegated staff | none | `staff_list` |
| Revoke all delegated access | user | `staff_revoke` |
| Show staff permissions | user | `staff_inspect` |
| Grant blog access | user | `staff_scope` |
| Revoke blog access | user | `staff_scope` |

## Audit

| Control | Target | Operation |
| --- | --- | --- |
| Recent admin actions | none | `audit` |
| My admin actions | none | `audit_mine` |
| Actions on this account | user | `target_audit` |
| Export audit page | none | `audit_export` |

## Operations

| Control | Target | Operation |
| --- | --- | --- |
| Database table counts | none | `counts` |
| Check database connection | none | `health` |
| Inspect pending jobs | none | `queue_pending` |
| Inspect failed jobs | none | `queue_failed` |
| Forget a failed job | none | `queue_forget` |
| Clear cached user count | none | `cache_users` |

## Fun

| Control | Target | Operation |
| --- | --- | --- |
| Give title: Certified brick | user | `flair` |
| Give title: Professional noob | user | `flair` |
| Give title: Stud inspector | user | `flair` |
| Give title: Guest of honor | user | `flair` |
| Give title: Obby survivor | user | `flair` |
| Give title: CEO of oof | user | `flair` |
| Give title: Brick enthusiast | user | `flair` |
| Give title: Definitely not a bot | user | `flair` |
| Use party hat | user | `flair` |
| Use upside-down card | user | `flair` |
| Use tiny profile | user | `flair` |
| Use golden frame | user | `flair` |
| Use retro scanlines | user | `flair` |
| Use rainbow frame | user | `flair` |
| Set red profile accent | user | `flair` |
| Set blue profile accent | user | `flair` |
| Set green profile accent | user | `flair` |
| Set purple profile accent | user | `flair` |
| Set orange profile accent | user | `flair` |
| Set pink profile accent | user | `flair` |
| Clear funny title | user | `flair_clear_title` |
| Clear profile effect | user | `flair_clear_effect` |
| Clear profile accent | user | `flair_clear_accent` |
| Reset all fun changes | user | `flair_reset` |
| Inspect profile flair | user | `flair_inspect` |
