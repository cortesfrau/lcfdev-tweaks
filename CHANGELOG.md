# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

---

## [1.0.0] - 2026-11-11

### Added

- Settings page under **Settings → Tweaks**, grouped by category. Every tweak is off by default.
- Tweaks that allow more than one scope can apply to visitors who are not logged in or to all visitors.
- **Disable Emojis**: removes the emoji script and styles from pages and `/embed/` pages, and keeps emojis as plain text in feeds.
- **Disable Dashicons**: dequeues the Dashicons stylesheet unless something depends on it, such as the admin bar.
- **Disable XML-RPC**: removes every XML-RPC method, pingbacks included, and the `X-Pingback` header.
- **Block user enumeration**: for visitors who are not logged in, `?author=N` returns a 404 and the REST users endpoint a 401. Removes the users sitemap, and the oEmbed author link points to the home page.
- **Generic login errors**: on `wp-login.php`, the login and lost password forms no longer reveal whether an account exists.
- Spanish (`es_ES`) translation.
- Uninstalling the plugin deletes its settings.
- Release workflow using `cortesfrau/github-workflows` `wp-release.yml@v4`.
