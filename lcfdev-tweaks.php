<?php

/**
 * Plugin Name:       Tweaks
 * Plugin URI:        https://github.com/cortesfrau/lcfdev-tweaks
 * Description:       A lightweight plugin to disable WordPress features your site does not need, improving performance and hardening security through configurable settings.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Lluis Cortes
 * Author URI:        https://github.com/cortesfrau
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lcfdev-tweaks
 * Domain Path:       /languages
 */

defined('ABSPATH') || exit;

use LcfDev\Tweaks\Plugin;

define('LCFDEV_TWEAKS_PATH', plugin_dir_path(__FILE__));

require_once __DIR__ . '/vendor/autoload.php';

add_action('plugins_loaded', function () {
    Plugin::create()->run();
});
