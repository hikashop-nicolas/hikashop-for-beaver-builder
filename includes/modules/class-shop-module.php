<?php
/**
 * Base of the modules wrapping a HikaShop module (cart, wishlist, currency, filters).
 *
 * @package HikaShopBeaverBuilder
 */

namespace HikaShopBeaverBuilder\Modules;

use HikaShopBeaverBuilder\Render;

defined( 'ABSPATH' ) || exit;

/**
 * Renders one of HikaShop's WordPress widgets inside Beaver Builder.
 */
abstract class Shop_Module extends Module {

	/**
	 * HikaShop widget class doing the rendering.
	 *
	 * @var string
	 */
	protected $hikashop_class = '';

	/**
	 * Module name, for the HikaShop settings page.
	 *
	 * @var string
	 */
	protected $module_name = '';

	/**
	 * HikaShop view editing the module settings, empty when the module has none.
	 *
	 * @var string
	 */
	protected $options_view = '';

	/**
	 * Field holding the settings in that view.
	 *
	 * @var string
	 */
	protected $options_field = '';

	/**
	 * Classes of every shop module.
	 *
	 * @return string[]
	 */
	public static function classes() {
		return array(
			Cart::class,
			Wishlist::class,
			Currency::class,
			Filter::class,
		);
	}

	/**
	 * Id under which HikaShop stores the settings of this module instance.
	 *
	 * @param string $node_id Beaver Builder node id.
	 * @return int
	 */
	public static function numeric_id( $node_id ) {
		$node_id = strtolower( (string) $node_id );
		if ( ! preg_match( '/^[0-9a-z]{6,}$/', $node_id ) ) {
			return 0;
		}
		// Same formula as assets/js/builder.js, which opens the settings of this id
		return 1000000000 + ( (int) base_convert( substr( $node_id, 0, 6 ), 36, 10 ) % 1000000000 );
	}

	/**
	 * Settings form.
	 *
	 * @return array
	 */
	public function form() {
		$fields = array();
		if ( '' !== $this->options_view && current_user_can( 'manage_options' ) ) {
			$fields['hikashop_note']     = array(
				'type'    => 'raw',
				'label'   => '',
				'content' => '<p class="fl-builder-settings-description">' . esc_html__( 'This module uses the settings of the HikaShop module. They open in a new window; the preview refreshes when you save them.', 'hikashop-for-beaver-builder' ) . '</p>'
					. sprintf(
						'<p><button type="button" class="fl-builder-button fl-builder-button-primary hikashop-bb-module-settings" data-module="%1$s" data-view="%2$s" data-field="%3$s">%4$s</button></p>',
						esc_attr( $this->module_name ),
						esc_attr( $this->options_view ),
						esc_attr( $this->options_field ),
						esc_html__( 'Edit the HikaShop settings', 'hikashop-for-beaver-builder' )
					),
			);
			$fields['hikashop_saved_at'] = array(
				'type'    => 'hidden',
				'default' => '',
			);
		} else {
			$fields['hikashop_note'] = array(
				'type'    => 'raw',
				'label'   => '',
				'content' => '<p class="fl-builder-settings-description">' . esc_html__( 'This module shows the HikaShop module with its default settings.', 'hikashop-for-beaver-builder' ) . '</p>',
			);
		}

		return array(
			'general' => array(
				'title'    => __( 'General', 'hikashop-for-beaver-builder' ),
				'sections' => array(
					'module' => array(
						'title'  => '',
						'fields' => $fields,
					),
				),
			),
		);
	}

	/**
	 * Frontend and builder output.
	 */
	public function render_html() {
		Render::prepare();
		if ( ! class_exists( $this->hikashop_class ) && defined( 'HIKASHOP_WP_PLUGIN_DIR' ) && is_file( HIKASHOP_WP_PLUGIN_DIR . 'widgets.php' ) ) {
			require_once HIKASHOP_WP_PLUGIN_DIR . 'widgets.php';
		}
		if ( ! class_exists( $this->hikashop_class ) ) {
			return;
		}

		$widget     = new $this->hikashop_class();
		$numeric_id = self::numeric_id( $this->node );
		if ( $numeric_id && method_exists( $widget, 'set_block_numeric_id' ) ) {
			$widget->set_block_numeric_id( $numeric_id );
		}
		$html = Render::as_site(
			function () use ( $widget ) {
				return $widget->render_module( array() );
			}
		);

		if ( '' === trim( wp_strip_all_tags( $html ) ) && ! preg_match( '#<(img|input|select|button)\b#i', $html ) ) {
			Render::placeholder( $this->name, __( 'Empty for now: it fills in on the site (a cart with products, filters on a listing page).', 'hikashop-for-beaver-builder' ) );
			return;
		}
		Render::output( $html );
	}
}
