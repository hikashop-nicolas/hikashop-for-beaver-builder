<?php
/**
 * The shop modules.
 *
 * @package HikaShopBeaverBuilder
 */

namespace HikaShopBeaverBuilder\Modules;

defined( 'ABSPATH' ) || exit;

/**
 * Cart summary.
 */
class Cart extends Shop_Module {

	/**
	 * HikaShop widget class.
	 *
	 * @var string
	 */
	protected $hikashop_class = 'HikaShop_Widget_Cart';

	/**
	 * Module name.
	 *
	 * @var string
	 */
	protected $module_name = 'mod_hikashop_cart';

	/**
	 * Settings view.
	 *
	 * @var string
	 */
	protected $options_view = 'cartmodules';

	/**
	 * Settings field.
	 *
	 * @var string
	 */
	protected $options_field = 'hikashopcartmodule';

	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'        => __( 'HikaShop Cart', 'hikashop-for-beaver-builder' ),
				'description' => __( 'The cart summary.', 'hikashop-for-beaver-builder' ),
				'slug'        => 'hikashop-cart',
				'icon'        => 'cart',
			)
		);
	}
}

/**
 * Wishlist summary.
 */
class Wishlist extends Shop_Module {

	/**
	 * HikaShop widget class.
	 *
	 * @var string
	 */
	protected $hikashop_class = 'HikaShop_Widget_Wishlist';

	/**
	 * Module name.
	 *
	 * @var string
	 */
	protected $module_name = 'mod_hikashop_wishlist';

	/**
	 * Settings view.
	 *
	 * @var string
	 */
	protected $options_view = 'cartmodules';

	/**
	 * Settings field.
	 *
	 * @var string
	 */
	protected $options_field = 'hikashopwishlistmodule';

	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'        => __( 'HikaShop Wishlist', 'hikashop-for-beaver-builder' ),
				'description' => __( 'The wishlist summary.', 'hikashop-for-beaver-builder' ),
				'slug'        => 'hikashop-wishlist',
				'icon'        => 'heart',
			)
		);
	}
}

/**
 * Currency switcher.
 */
class Currency extends Shop_Module {

	/**
	 * HikaShop widget class.
	 *
	 * @var string
	 */
	protected $hikashop_class = 'HikaShop_Widget_Currency';

	/**
	 * Module name.
	 *
	 * @var string
	 */
	protected $module_name = 'mod_hikashop_currency';

	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'        => __( 'HikaShop Currency Switcher', 'hikashop-for-beaver-builder' ),
				'description' => __( 'The currency selector.', 'hikashop-for-beaver-builder' ),
				'slug'        => 'hikashop-currency',
				'icon'        => 'money-alt',
			)
		);
	}
}

/**
 * Product filters.
 */
class Filter extends Shop_Module {

	/**
	 * HikaShop widget class.
	 *
	 * @var string
	 */
	protected $hikashop_class = 'HikaShop_Widget_Filter';

	/**
	 * Module name.
	 *
	 * @var string
	 */
	protected $module_name = 'mod_hikashop_filter';

	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'        => __( 'HikaShop Filters', 'hikashop-for-beaver-builder' ),
				'description' => __( 'The product filters of the listings.', 'hikashop-for-beaver-builder' ),
				'slug'        => 'hikashop-filter',
				'icon'        => 'filter',
			)
		);
	}
}
