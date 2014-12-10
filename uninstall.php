<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   QRLogin
 * @author    Labs64 <info@labs64.com>
 * @license   GPL-2.0+
 * @link      http://www.labs64.com
 * @copyright 2014 Labs64
 */

// If uninstall, not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('QRL_OPTIONS');
