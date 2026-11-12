<?php
/**
 * Removes the settings of the plugin. The layouts stay: they are the site's content.
 *
 * @package HikaShopBeaverBuilder
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'hikashop_bb_product_layout' );
