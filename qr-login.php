<?php
/**
 * @wordpress-plugin
 * Plugin Name: QRLogin
 * Plugin URI:  https://github.com/Labs64/qr-login
 * Description: The best way for publishers and bloggers to monetize their content.
 * Author:      Labs64
 * Author URI:  http://www.labs64.com
 * Version:     0.1.0
 * Text Domain: qr-login
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 3.5.1
 * Tested up to: 4.0
 *
 * @package   QRLogin
 * @author    Labs64 <info@labs64.com>
 * @license   GPL-2.0+
 * @link      http://www.labs64.com
 * @copyright 2014 Labs64
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// main
require_once(plugin_dir_path(__FILE__) . 'public/qr-login-class.php');
require_once(plugin_dir_path(__FILE__) . 'public/qr-login-functions.php');

// utils
require_once(plugin_dir_path(__FILE__) . 'includes/netlicensing/netlicensing.php');
require_once(plugin_dir_path(__FILE__) . 'includes/curl/curl.php');
require_once(plugin_dir_path(__FILE__) . 'includes/curl/curl_response.php');


// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook(__FILE__, array('QRLogin', 'activate'));
register_deactivation_hook(__FILE__, array('QRLogin', 'deactivate'));

add_action('plugins_loaded', array('QRLogin', 'get_instance'));

if (is_admin()) {
    require_once(plugin_dir_path(__FILE__) . 'admin/qr-login-class-admin.php');
    add_action('plugins_loaded', array('QRLogin_Admin', 'get_instance'));
}
