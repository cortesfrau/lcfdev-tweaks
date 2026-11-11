# Tweaks

A lightweight plugin to disable WordPress features your site does not need, improving performance and hardening security through configurable settings.

Every tweak is off until you turn it on in **Settings → Tweaks**, so activating the plugin changes nothing on its own.

## Requirements

- WordPress 6.5 or later
- PHP 8.1 or later

## Installation

### From a release

1. Download the zip from the latest GitHub release.
2. Upload it in **Plugins → Add New → Upload Plugin** and activate **Tweaks**.
3. Open **Settings → Tweaks** and turn on the tweaks you need.

Release zips include `vendor/`.

### From the repository

1. Install dependencies:

   ```bash
   composer install
   ```

2. Copy the plugin into `wp-content/plugins/`, or work from this directory if it is already there.
3. Activate **Tweaks** and open **Settings → Tweaks**.

`vendor/` is not in the repository. Run `composer install` before activating the plugin.

## Tweaks

| Tweak | Category | Applies to | What it does |
|---|---|---|---|
| Disable Emojis | Performance | Visitors who are not logged in, or all visitors | Removes the emoji detection script and styles from pages and `/embed/` pages, and keeps emojis as plain text in feeds. |
| Disable Dashicons | Performance | Visitors who are not logged in, or all visitors | Dequeues the Dashicons stylesheet. It still loads when something depends on it, such as the admin bar, so nothing breaks for logged-in users. |
| Disable XML-RPC | Security | Everywhere | Removes every XML-RPC method, pingbacks included, and the `X-Pingback` header. Jetpack and some older apps need XML-RPC. |
| Block user enumeration | Security | Everywhere | For visitors who are not logged in: `?author=N` returns a 404 and the REST users endpoint returns a 401, embedded authors (`?_embed`) included. The users sitemap is removed and the oEmbed author link points to the home page. |
| Generic login errors | Security | Everywhere | The login form shows the same error whether the username exists or not. On `wp-login.php`, a lost password request for an unknown account shows the same confirmation as a known one. |

### Where a tweak applies

Tweaks that allow more than one scope show a select with **Off** and each scope. Tweaks that allow one scope show a checkbox.

- **Visitors who are not logged in**: frontend pages for visitors without a session.
- **All visitors**: frontend pages for everyone, logged-in users included.
- **Everywhere**: every request.

Frontend pages are any page a visitor sees, including feeds, `/embed/` pages and `wp-login.php`. Admin screens, REST, AJAX, cron, XML-RPC and WP-CLI requests are not frontend pages.

### What these tweaks do not do

- **XML-RPC**: requests to `xmlrpc.php` still start WordPress. To stop them before PHP runs, block the file on the web server, for example `location = /xmlrpc.php { deny all; }` in nginx.
- **User enumeration**: author archives (`/author/<slug>/`) still exist, so the slug shows wherever the theme links to them.
- **Login**: there is no rate limiting. Use a dedicated plugin or the web server for that. The generic errors only cover `wp-login.php`: forms from other plugins, such as WooCommerce, keep their own messages. A request for an unknown user still answers slightly faster, because WordPress skips the password check, and if the server cannot send email, the lost password form only shows that error for accounts that exist.

## Settings

Settings are stored in the `lcfdev_tweaks_settings` option as `tweak id => scope`, or `null` when the tweak is off. A value that the tweak does not allow counts as off. Uninstalling the plugin deletes the option.

The settings page requires the `activate_plugins` capability.

## Development

PHP lives in `src/`, autoloaded with PSR-4 under the `LcfDev\Tweaks` namespace.

```
src/
├── Admin/SettingsPage.php        Settings → Tweaks
├── Contracts/Tweak.php           What every tweak implements
├── Enums/Scope.php               Where a tweak applies
├── Enums/TweakCategory.php       Settings page sections
├── Plugin.php                    Bootstrap
├── Registry/TweakRegistry.php
├── Settings/SettingsRepository.php
├── Support/Context.php           Answers "which request is this, and who is asking"
└── Tweaks/
    ├── Catalog.php               The list of available tweaks
    └── {Performance,Security}/
```

On `init`, `Plugin` registers each tweak that is turned on and whose scope matches the current request. A tweak never checks the scope itself. When it needs to know who is asking after `init` (for example in a REST callback, where application passwords authenticate later), it calls `$context->isGuest()` at that point.

### Adding a tweak

1. Create a final class in `src/Tweaks/<Category>/` that implements `Contracts\Tweak`:
   - `id()`: stable and unique. It is the key in the stored settings, so changing it turns the tweak off on existing sites.
   - `label()` and `description()`: shown on the settings page. Wrap them in `__()` with the `lcfdev-tweaks` text domain.
   - `category()`: the settings page section.
   - `scopes()`: the scopes the tweak allows. The first one is the default.
   - `register(Context $context)`: hooks the tweak into WordPress. It runs on `init`, so it cannot rely on earlier hooks.
2. Add it to `Tweaks\Catalog::all()`.
3. Update the translations.

### Translations

The text domain is `lcfdev-tweaks`, loaded from `languages/`. After changing any translatable string, run with [WP-CLI](https://wp-cli.org/):

```bash
wp i18n make-pot . languages/lcfdev-tweaks.pot --exclude=vendor,node_modules
wp i18n update-po languages/lcfdev-tweaks.pot languages/
# Translate the new strings in each .po file
wp i18n make-mo languages/
wp i18n make-php languages/
```

Release zips ship the compiled `.mo` and `.l10n.php` files, not the `.po` and `.pot` sources.

Included translations: Spanish (`es_ES`).

## Release

1. Move the `[Unreleased]` entries in `CHANGELOG.md` under `## [x.y.z] - YYYY-MM-DD`.
2. Update `Version` in `lcfdev-tweaks.php`.
3. Merge into `main` and push a `vx.y.z` tag from `main`.

The `release.yml` workflow builds the zip with `cortesfrau/github-workflows` (`wp-release.yml@v4`) and leaves out what `.distignore` lists.

## License

GPL-2.0-or-later
