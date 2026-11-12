<?php
/**
 * Template of the product picker field: the ids, in order, in a hidden input that assets/js/builder.js manages.
 *
 * @package HikaShopBeaverBuilder
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="hikashop-bb-picker">
	<input type="hidden" class="hikashop-bb-picker-value" name="{{data.name}}" value="{{data.value}}" />
	<ol class="hikashop-bb-picker-list"></ol>
	<input type="search" class="text text-full hikashop-bb-picker-search" placeholder="<?php esc_attr_e( 'Search by name, code or id', 'hikashop-for-beaver-builder' ); ?>" autocomplete="off" />
	<ul class="hikashop-bb-picker-results"></ul>
</div>
