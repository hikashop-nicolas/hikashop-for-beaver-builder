<?php
/**
 * Bootstrap: checks the requirements and hooks everything in.
 *
 * @package HikaShopBeaverBuilder
 */

namespace HikaShopBeaverBuilder;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 */
final class Plugin {

	const MIN_BEAVER_BUILDER = '2.9.0';
	const MIN_HIKASHOP       = '6.8.2';

	/**
	 * Hooks the plugin in once every plugin is loaded.
	 */
	public static function boot() {
		add_action( 'plugins_loaded', array( __CLASS__, 'init' ), 20 );
	}

	/**
	 * Whether HikaShop is installed, active and recent enough.
	 *
	 * @return bool
	 */
	public static function hikashop_ready() {
		// The 6.8.2 packages built since 12 November 2026 carry the product page hook and the SEO of add-on pages
		return defined( 'HIKASHOP_COMPONENT' ) && defined( 'HIKASHOP_WP_VERSION' ) && function_exists( 'hikashop_get' )
			&& version_compare( HIKASHOP_WP_VERSION, self::MIN_HIKASHOP, '>=' ) && function_exists( 'hikashop_siteRendering' );
	}

	/**
	 * Whether Beaver Builder (Lite or the paid edition) is active and recent enough.
	 *
	 * @return bool
	 */
	public static function beaver_builder_ready() {
		return class_exists( '\FLBuilder' ) && defined( 'FL_BUILDER_VERSION' )
			&& version_compare( FL_BUILDER_VERSION, self::MIN_BEAVER_BUILDER, '>=' );
	}

	/**
	 * Registers the hooks, or the admin notice saying what is missing.
	 */
	public static function init() {
		if ( ! self::hikashop_ready() || ! self::beaver_builder_ready() ) {
			add_action( 'admin_notices', array( __CLASS__, 'requirements_notice' ) );
			return;
		}

		require_once HIKASHOP_BB_DIR . 'includes/class-render.php';
		require_once HIKASHOP_BB_DIR . 'includes/class-listing.php';
		require_once HIKASHOP_BB_DIR . 'includes/class-ajax.php';
		require_once HIKASHOP_BB_DIR . 'includes/class-layouts.php';

		add_action( 'init', array( __CLASS__, 'register_modules' ) );
		add_filter( 'fl_builder_custom_fields', array( __CLASS__, 'custom_fields' ) );
		add_filter( 'fl_builder_field_js_config', array( __CLASS__, 'field_config' ) );
		add_action( 'fl_builder_ui_enqueue_scripts', array( __CLASS__, 'builder_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'builder_assets' ) );

		Listing::init();
		Ajax::init();
		Layouts::init();
	}

	/**
	 * Tells the administrator which requirement is missing.
	 */
	public static function requirements_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( ! self::hikashop_ready() ) {
			$message = sprintf(
				/* translators: 1: minimum HikaShop version, 2: link to the HikaShop download page */
				__( 'HikaShop for Beaver Builder needs HikaShop %1$s or newer (a package downloaded since 12 November 2026), installed and active. HikaShop Starter is free: %2$s', 'hikashop-for-beaver-builder' ),
				self::MIN_HIKASHOP,
				'<a href="https://www.hikashop.com/download" target="_blank" rel="noopener noreferrer">' . esc_html__( 'download HikaShop', 'hikashop-for-beaver-builder' ) . '</a>'
			);
		} else {
			$message = sprintf(
				/* translators: %s: minimum Beaver Builder version */
				__( 'HikaShop for Beaver Builder needs Beaver Builder %s or newer (the free Beaver Builder Lite is enough), installed and active.', 'hikashop-for-beaver-builder' ),
				self::MIN_BEAVER_BUILDER
			);
		}

		echo '<div class="notice notice-warning"><p>' . wp_kses(
			$message,
			array(
				'a' => array(
					'href'   => true,
					'target' => true,
					'rel'    => true,
				),
			)
		) . '</p></div>';
	}

	/**
	 * Registers the modules.
	 */
	public static function register_modules() {
		$dir = HIKASHOP_BB_DIR . 'includes/modules/';
		require_once $dir . 'class-module.php';
		require_once $dir . 'class-products.php';
		require_once $dir . 'class-categories.php';
		require_once $dir . 'class-product-part.php';
		require_once $dir . 'class-product-parts.php';
		require_once $dir . 'class-shop-module.php';
		require_once $dir . 'class-shop-modules.php';

		$classes = array_merge(
			array( Modules\Products::class, Modules\Categories::class ),
			Modules\Product_Part::classes(),
			Modules\Shop_Module::classes()
		);
		foreach ( $classes as $class ) {
			$module = new $class();
			\FLBuilder::register_module( $module, $module->form() );
		}
	}

	/**
	 * Fills the category selects, only when the builder sends its forms to the browser.
	 *
	 * @param array $field Field config.
	 * @return array
	 */
	public static function field_config( $field ) {
		if ( is_array( $field ) && ! empty( $field['hikashop_category'] ) ) {
			$field['options'] = Listing::category_options();
		}
		return $field;
	}

	/**
	 * The product picker field type.
	 *
	 * @param array $fields Field type => template file.
	 * @return array
	 */
	public static function custom_fields( $fields ) {
		$fields['hikashop-products'] = HIKASHOP_BB_DIR . 'fields/hikashop-products.php';
		return $fields;
	}

	/**
	 * Script and style of the builder interface: the product picker and the module settings button.
	 */
	public static function builder_scripts() {
		wp_enqueue_style( 'hikashop-bb-builder', HIKASHOP_BB_URL . 'assets/css/builder.css', array(), HIKASHOP_BB_VERSION );
		wp_enqueue_script( 'hikashop-bb-builder', HIKASHOP_BB_URL . 'assets/js/builder.js', array( 'jquery' ), HIKASHOP_BB_VERSION, true );
		wp_localize_script(
			'hikashop-bb-builder',
			'hikashopBeaverBuilder',
			array(
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'hikashop_bb_search' ),
				'settingsUrl' => admin_url( 'admin.php?page=hikashop-widget-options&tmpl=popup' ),
				'remove'      => __( 'Remove', 'hikashop-for-beaver-builder' ),
				'up'          => __( 'Move up', 'hikashop-for-beaver-builder' ),
				'down'        => __( 'Move down', 'hikashop-for-beaver-builder' ),
				'none'        => __( 'No product found.', 'hikashop-for-beaver-builder' ),
			)
		);
	}

	/**
	 * HikaShop's CSS and JavaScript while the builder is open, so the modules look and work as on the site.
	 */
	public static function builder_assets() {
		if ( Render::is_editor() ) {
			Render::enqueue_shop_assets();
			// Without the builder's iframe interface, the settings forms live in this page
			self::builder_scripts();
		}
	}
}
