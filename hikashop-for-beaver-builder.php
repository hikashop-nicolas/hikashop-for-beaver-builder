<?php
/**
 * Plugin Name:       HikaShop for Beaver Builder
 * Plugin URI:        https://github.com/hikashop-nicolas/hikashop-for-beaver-builder
 * Description:       HikaShop product listings, product page parts, cart and filters as Beaver Builder modules, and HikaShop product pages designed with Beaver Builder.
 * Version:           1.0.0
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * Author:            Hikari Software
 * Author URI:        https://www.hikashop.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hikashop-for-beaver-builder
 *
 * @package HikaShopBeaverBuilder
 */

defined( 'ABSPATH' ) || exit;

define( 'HIKASHOP_BB_VERSION', '1.0.0' );
define( 'HIKASHOP_BB_FILE', __FILE__ );
define( 'HIKASHOP_BB_DIR', plugin_dir_path( __FILE__ ) );
define( 'HIKASHOP_BB_URL', plugin_dir_url( __FILE__ ) );

require_once HIKASHOP_BB_DIR . 'includes/class-plugin.php';

\HikaShopBeaverBuilder\Plugin::boot();
