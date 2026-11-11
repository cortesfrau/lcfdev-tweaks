# WP Tweaks

Disables WordPress features the site does not need, such as emojis or Dashicons on the frontend for visitors who are not logged in.

## Requirements

- WordPress 6.0 or later
- PHP 8.0 or later

## Installation

1. Install dependencies:

   ```bash
   composer install
   ```

2. Copy the plugin into `wp-content/plugins/`, or work from this directory if it is already there.
3. Activate **WP Tweaks** in the WordPress admin.

`vendor/` is not in the repository. Run `composer install` before activating the plugin.

## Development

PHP lives in `src/`, autoloaded with PSR-4 under the `LcfDev\WpTweaks` namespace.

## Release

Push a `v*` tag from `main`. The `release.yml` workflow builds the zip with `cortesfrau/github-workflows` (`wp-release.yml@v4`) and leaves out what `.distignore` lists.

## License

GPL-2.0-or-later
