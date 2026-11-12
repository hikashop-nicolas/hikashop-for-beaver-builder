<?php
/**
 * Base of the product page parts, each one wrapping a HikaShop product block.
 *
 * @package HikaShopBeaverBuilder
 */

namespace HikaShopBeaverBuilder\Modules;

use HikaShopBeaverBuilder\Render;

defined( 'ABSPATH' ) || exit;

/**
 * One part of the product page (title, price, gallery...), for the current product.
 */
abstract class Product_Part extends Module {

	/**
	 * Name of the HikaShop block rendering the part, without the "hikashop/product-" prefix.
	 *
	 * @var string
	 */
	protected $part = '';

	/**
	 * CSS selector the style settings apply to, below the node.
	 *
	 * @var string
	 */
	protected $style_selector = '';

	/**
	 * Classes of every part.
	 *
	 * @return string[]
	 */
	public static function classes() {
		return array(
			Product_Title::class,
			Product_Gallery::class,
			Product_Price::class,
			Product_Add_To_Cart::class,
			Product_Description::class,
			Product_Variants::class,
			Product_Options::class,
			Product_Specifications::class,
			Product_Custom_Fields::class,
			Product_Dimensions::class,
			Product_Bundled::class,
			Product_Files::class,
			Product_Rating::class,
			Product_Social::class,
		);
	}

	/**
	 * Module details.
	 *
	 * @param string $part Block name.
	 * @param string $name Module name.
	 * @param string $icon Dashicon.
	 */
	public function __construct( $part, $name, $icon ) {
		$this->part = $part;
		parent::__construct(
			array(
				'name'        => $name,
				'description' => __( 'A part of the product page, for the product being viewed.', 'hikashop-for-beaver-builder' ),
				'slug'        => 'hikashop-product-' . $part,
				'icon'        => $icon,
			)
		);
	}

	/**
	 * Settings of the part, after the note.
	 *
	 * @return array
	 */
	protected function part_fields() {
		return array();
	}

	/**
	 * Block attributes built from the settings.
	 *
	 * @param array $settings Module settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		unset( $settings );
		return array();
	}

	/**
	 * Settings form.
	 *
	 * @return array
	 */
	public function form() {
		return array(
			'general' => array(
				'title'    => __( 'General', 'hikashop-for-beaver-builder' ),
				'sections' => array(
					'part' => array(
						'title'  => '',
						'fields' => array_merge(
							array(
								'part_note' => array(
									'type'    => 'raw',
									'label'   => '',
									'content' => '<p class="fl-builder-settings-description">' . esc_html__( 'Shows the product being viewed: place it in the product page layout chosen in Settings > HikaShop for Beaver Builder. In the builder, the latest product stands in.', 'hikashop-for-beaver-builder' ) . '</p>',
								),
							),
							$this->part_fields()
						),
					),
				),
			),
			'style'   => array(
				'title'    => __( 'Style', 'hikashop-for-beaver-builder' ),
				'sections' => array(
					'text' => array(
						'title'  => '',
						'fields' => array(
							'text_color'      => array(
								'type'        => 'color',
								'label'       => __( 'Text color', 'hikashop-for-beaver-builder' ),
								'show_reset'  => true,
								'show_alpha'  => true,
								'responsive'  => true,
								'connections' => array( 'color' ),
							),
							'text_typography' => array(
								'type'       => 'typography',
								'label'      => __( 'Typography', 'hikashop-for-beaver-builder' ),
								'responsive' => true,
							),
							'text_align'      => array(
								'type'       => 'align',
								'label'      => __( 'Alignment', 'hikashop-for-beaver-builder' ),
								'default'    => '',
								'responsive' => true,
							),
						),
					),
				),
			),
		);
	}

	/**
	 * CSS of the style settings.
	 *
	 * @param string $id Node id.
	 */
	public function render_css( $id ) {
		$node = ".fl-node-$id";
		$this->text_style_css( 'text', '' === $this->style_selector ? "$node .fl-module-content" : "$node " . $this->style_selector );
		\FLBuilderCSS::responsive_rule(
			array(
				'settings'     => $this->settings,
				'setting_name' => 'text_align',
				'selector'     => "$node .fl-module-content",
				'prop'         => 'text-align',
			)
		);
	}

	/**
	 * Points HikaShop at a product while the builder shows a layout.
	 */
	protected function prepare_product() {
		$input = \hikaInput::get();
		if ( $input->getInt( 'cid', 0 ) || $input->getInt( 'product_id', 0 ) || ! Render::is_editor() ) {
			return;
		}
		$product_id = self::preview_product_id();
		if ( $product_id ) {
			$input->set( 'cid', $product_id );
		}
	}

	/**
	 * Product shown in the builder: the latest published main product.
	 *
	 * @return int
	 */
	public static function preview_product_id() {
		static $product_id = null;
		if ( null === $product_id ) {
			$db = \JFactory::getDBO();
			$db->setQuery(
				'SELECT product_id FROM ' . hikashop_table( 'product' )
				. ' WHERE product_published = 1 AND product_type = ' . $db->quote( 'main' )
				. ' ORDER BY product_id DESC',
				0,
				1
			);
			$product_id = (int) $db->loadResult();
			/**
			 * Filters the product the builder shows in the product page parts.
			 *
			 * @param int $product_id HikaShop product id.
			 */
			$product_id = (int) apply_filters( 'hikashop_bb_preview_product', $product_id );
		}
		return $product_id;
	}

	/**
	 * Frontend and builder output.
	 */
	public function render_html() {
		if ( ! function_exists( 'render_block' ) || ! \WP_Block_Type_Registry::get_instance()->is_registered( 'hikashop/product-' . $this->part ) ) {
			return;
		}
		Render::prepare();
		$this->prepare_product();

		$block = array(
			'blockName'    => 'hikashop/product-' . $this->part,
			'attrs'        => $this->block_attributes( $this->settings_array() ),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);
		$html  = Render::as_site(
			function () use ( $block ) {
				return render_block( $block );
			}
		);

		if ( '' === trim( wp_strip_all_tags( $html ) ) && ! preg_match( '#<(img|input|select|button|iframe)\b#i', $html ) ) {
			Render::placeholder( $this->name, __( 'Nothing to show for this product.', 'hikashop-for-beaver-builder' ) );
			return;
		}
		Render::output( $html );
	}
}
