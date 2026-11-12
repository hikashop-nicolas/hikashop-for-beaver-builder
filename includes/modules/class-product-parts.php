<?php
/**
 * The product page parts.
 *
 * @package HikaShopBeaverBuilder
 */

namespace HikaShopBeaverBuilder\Modules;

defined( 'ABSPATH' ) || exit;

/**
 * Adds the "Inherit / Show / Hide" selects HikaShop's product blocks take.
 */
trait Block_Switches {

	/**
	 * One select per block attribute.
	 *
	 * @param array $switches Block attribute => label.
	 * @return array Fields.
	 */
	protected function block_switch_fields( $switches ) {
		$fields = array();
		foreach ( $switches as $name => $label ) {
			$fields[ $name ] = array(
				'type'    => 'select',
				'label'   => $label,
				'default' => 'inherit',
				'options' => array(
					'inherit' => __( 'Inherit from HikaShop', 'hikashop-for-beaver-builder' ),
					'show'    => __( 'Show', 'hikashop-for-beaver-builder' ),
					'hide'    => __( 'Hide', 'hikashop-for-beaver-builder' ),
				),
			);
		}
		return $fields;
	}

	/**
	 * Block attributes from the selects.
	 *
	 * @param array $settings Module settings.
	 * @param array $names    Block attribute names.
	 * @return array
	 */
	protected function block_switches( $settings, $names ) {
		$attributes = array();
		foreach ( $names as $name ) {
			$value = $settings[ $name ] ?? 'inherit';
			if ( in_array( $value, array( 'show', 'hide' ), true ) ) {
				$attributes[ $name ] = $value;
			}
		}
		return $attributes;
	}
}

/**
 * Product name.
 */
class Product_Title extends Product_Part {
	/**
	 * Style target.
	 *
	 * @var string
	 */
	protected $style_selector = '.hikashop_product_name_main';

	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'title', __( 'Product Title', 'hikashop-for-beaver-builder' ), 'heading' );
	}

	/**
	 * Settings of the part.
	 *
	 * @return array
	 */
	protected function part_fields() {
		return array(
			'heading_level' => array(
				'type'    => 'select',
				'label'   => __( 'HTML tag', 'hikashop-for-beaver-builder' ),
				'default' => '1',
				'options' => array(
					'1' => 'H1',
					'2' => 'H2',
					'3' => 'H3',
					'4' => 'H4',
					'5' => 'H5',
					'6' => 'H6',
				),
			),
			'show_code'     => $this->yes_no_field( __( 'Product code', 'hikashop-for-beaver-builder' ), '0' ),
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Module settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return array(
			'headingLevel' => max( 1, min( 6, (int) ( $settings['heading_level'] ?? 1 ) ) ),
			'showCode'     => ! empty( $settings['show_code'] ),
		);
	}
}

/**
 * Product images.
 */
class Product_Gallery extends Product_Part {
	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'gallery', __( 'Product Images', 'hikashop-for-beaver-builder' ), 'format-gallery' );
	}
}

/**
 * Product price.
 */
class Product_Price extends Product_Part {
	use Block_Switches;

	/**
	 * Style target.
	 *
	 * @var string
	 */
	protected $style_selector = '.hikashop_product_price';

	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'price', __( 'Product Price', 'hikashop-for-beaver-builder' ), 'tag' );
	}

	/**
	 * Settings of the part.
	 *
	 * @return array
	 */
	protected function part_fields() {
		return $this->block_switch_fields(
			array(
				'showOriginalPrice' => __( 'Price before discount', 'hikashop-for-beaver-builder' ),
				'showDiscount'      => __( 'Discount', 'hikashop-for-beaver-builder' ),
			)
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Module settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return $this->block_switches( $settings, array( 'showOriginalPrice', 'showDiscount' ) );
	}
}

/**
 * Quantity and add to cart button.
 */
class Product_Add_To_Cart extends Product_Part {
	use Block_Switches;

	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'add-to-cart', __( 'Add to Cart', 'hikashop-for-beaver-builder' ), 'cart' );
	}

	/**
	 * Settings of the part.
	 *
	 * @return array
	 */
	protected function part_fields() {
		return $this->block_switch_fields(
			array(
				'showQuantity' => __( 'Quantity field', 'hikashop-for-beaver-builder' ),
			)
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Module settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return $this->block_switches( $settings, array( 'showQuantity' ) );
	}
}

/**
 * Product description.
 */
class Product_Description extends Product_Part {
	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'description', __( 'Product Description', 'hikashop-for-beaver-builder' ), 'text' );
	}
}

/**
 * Characteristic selectors of a product with variants.
 */
class Product_Variants extends Product_Part {
	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'variants', __( 'Product Variants', 'hikashop-for-beaver-builder' ), 'screenoptions' );
	}
}

/**
 * Product options.
 */
class Product_Options extends Product_Part {
	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'options', __( 'Product Options', 'hikashop-for-beaver-builder' ), 'list-view' );
	}
}

/**
 * Product specifications (custom product fields shown as a table).
 */
class Product_Specifications extends Product_Part {
	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'specifications', __( 'Product Specifications', 'hikashop-for-beaver-builder' ), 'editor-table' );
	}
}

/**
 * Item custom fields the customer fills before adding to the cart.
 */
class Product_Custom_Fields extends Product_Part {
	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'custom-fields', __( 'Product Custom Fields', 'hikashop-for-beaver-builder' ), 'forms' );
	}
}

/**
 * Weight and dimensions.
 */
class Product_Dimensions extends Product_Part {
	use Block_Switches;

	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'dimensions', __( 'Product Dimensions', 'hikashop-for-beaver-builder' ), 'image-crop' );
	}

	/**
	 * Settings of the part.
	 *
	 * @return array
	 */
	protected function part_fields() {
		return $this->block_switch_fields(
			array(
				'showWeight'     => __( 'Weight', 'hikashop-for-beaver-builder' ),
				'showDimensions' => __( 'Dimensions', 'hikashop-for-beaver-builder' ),
			)
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Module settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return $this->block_switches( $settings, array( 'showWeight', 'showDimensions' ) );
	}
}

/**
 * Products bundled with the product.
 */
class Product_Bundled extends Product_Part {
	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'bundled', __( 'Bundled Products', 'hikashop-for-beaver-builder' ), 'archive' );
	}
}

/**
 * Files attached to the product.
 */
class Product_Files extends Product_Part {
	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'files', __( 'Product Files', 'hikashop-for-beaver-builder' ), 'download' );
	}
}

/**
 * Rating stars.
 */
class Product_Rating extends Product_Part {
	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'rating', __( 'Product Rating', 'hikashop-for-beaver-builder' ), 'star-filled' );
	}
}

/**
 * Social sharing buttons.
 */
class Product_Social extends Product_Part {
	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct( 'social', __( 'Product Sharing', 'hikashop-for-beaver-builder' ), 'share' );
	}
}
