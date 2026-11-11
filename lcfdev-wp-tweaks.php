<?php

/**
 * Plugin Name:       WP Tweaks
 * Plugin URI:        https://github.com/cortesfrau/lcfdev-wp-tweaks
 * Description:       Disables WordPress features the site does not need, such as emojis or Dashicons on the frontend for visitors who are not logged in.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Lluis Cortes
 * Author URI:        https://github.com/cortesfrau
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lcfdev-wp-tweaks
 */

defined('ABSPATH') || exit;

use LcfDev\WpTweaks\Plugin;

define('LCFDEV_WP_TWEAKS_PATH', plugin_dir_path(__FILE__));
define('LCFDEV_WP_TWEAKS_URL', plugin_dir_url(__FILE__));

require_once __DIR__ . '/vendor/autoload.php';

add_action('plugins_loaded', function () {
    Plugin::instance()->run();
});
