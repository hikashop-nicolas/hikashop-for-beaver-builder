<?php
/**
 * Products module: a HikaShop product listing.
 *
 * @package HikaShopBeaverBuilder
 */

namespace HikaShopBeaverBuilder\Modules;

use HikaShopBeaverBuilder\Listing;
use HikaShopBeaverBuilder\Render;

defined( 'ABSPATH' ) || exit;

/**
 * Product listing.
 */
class Products extends Module {

	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'        => __( 'HikaShop Products', 'hikashop-for-beaver-builder' ),
				'description' => __( 'A listing of HikaShop products.', 'hikashop-for-beaver-builder' ),
				'slug'        => 'hikashop-products',
				'icon'        => 'products',
			)
		);
	}

	/**
	 * Settings form.
	 *
	 * @return array
	 */
	public function form() {
		$query_fields = array( 'category', 'subcategories', 'limit' );
		return array(
			'general' => array(
				'title'    => __( 'Products', 'hikashop-for-beaver-builder' ),
				'sections' => array(
					'query'  => array(
						'title'  => '',
						'fields' => array(
							'source'        => array(
								'type'    => 'select',
								'label'   => __( 'Source', 'hikashop-for-beaver-builder' ),
								'default' => 'category',
								'options' => array(
									'category' => __( 'Products of a category', 'hikashop-for-beaver-builder' ),
									'manual'   => __( 'Products chosen by hand', 'hikashop-for-beaver-builder' ),
									'sale'     => __( 'Products on sale', 'hikashop-for-beaver-builder' ),
									'recent'   => __( 'Recently viewed by the visitor', 'hikashop-for-beaver-builder' ),
									'related'  => __( 'Related to the product shown', 'hikashop-for-beaver-builder' ),
								),
								'toggle'  => array(
									'category' => array( 'fields' => $query_fields ),
									'sale'     => array( 'fields' => $query_fields ),
									'recent'   => array( 'fields' => $query_fields ),
									'manual'   => array( 'fields' => array( 'products' ) ),
									'related'  => array( 'fields' => array( 'related_note', 'limit' ) ),
								),
							),
							'category'      => $this->category_field( __( 'Category', 'hikashop-for-beaver-builder' ) ),
							'subcategories' => $this->yes_no_field( __( 'Include subcategories', 'hikashop-for-beaver-builder' ), '1' ),
							'products'      => array(
								'type'  => 'hikashop-products',
								'label' => __( 'Products', 'hikashop-for-beaver-builder' ),
							),
							'related_note'  => array(
								'type'    => 'raw',
								'label'   => '',
								'content' => '<p class="fl-builder-settings-description">' . esc_html__( 'Place this listing in the product page layout: it shows the related products of the product being viewed.', 'hikashop-for-beaver-builder' ) . '</p>',
							),
							'order'         => array(
								'type'    => 'select',
								'label'   => __( 'Order by', 'hikashop-for-beaver-builder' ),
								'default' => 'ordering',
								'options' => array(
									'ordering'              => __( 'HikaShop ordering', 'hikashop-for-beaver-builder' ),
									'manual'                => __( 'Order chosen above', 'hikashop-for-beaver-builder' ),
									'product_name'          => __( 'Name', 'hikashop-for-beaver-builder' ),
									'product_sort_price'    => __( 'Price', 'hikashop-for-beaver-builder' ),
									'product_created'       => __( 'Creation date', 'hikashop-for-beaver-builder' ),
									'product_modified'      => __( 'Modification date', 'hikashop-for-beaver-builder' ),
									'product_sales'         => __( 'Sales', 'hikashop-for-beaver-builder' ),
									'product_hit'           => __( 'Views', 'hikashop-for-beaver-builder' ),
									'product_average_score' => __( 'Rating', 'hikashop-for-beaver-builder' ),
									'random'                => __( 'Random', 'hikashop-for-beaver-builder' ),
								),
							),
							'order_dir'     => array(
								'type'    => 'select',
								'label'   => __( 'Direction', 'hikashop-for-beaver-builder' ),
								'default' => 'ASC',
								'options' => array(
									'ASC'  => __( 'Ascending', 'hikashop-for-beaver-builder' ),
									'DESC' => __( 'Descending', 'hikashop-for-beaver-builder' ),
								),
							),
							'limit'         => $this->number_field( __( 'Number of products', 'hikashop-for-beaver-builder' ), 8 ),
						),
					),
					'layout' => array(
						'title'  => __( 'Layout', 'hikashop-for-beaver-builder' ),
						'fields' => array(
							'columns'       => $this->number_field( __( 'Columns', 'hikashop-for-beaver-builder' ), 4 ),
							'item_layout'   => array(
								'type'    => 'select',
								'label'   => __( 'Product layout', 'hikashop-for-beaver-builder' ),
								'default' => 'inherit',
								'options' => $this->item_layout_options(),
							),
							'show_price'    => $this->switch_field( __( 'Price', 'hikashop-for-beaver-builder' ) ),
							'add_to_cart'   => $this->switch_field( __( 'Add to cart button', 'hikashop-for-beaver-builder' ) ),
							'quantity'      => $this->switch_field( __( 'Quantity field', 'hikashop-for-beaver-builder' ) ),
							'wishlist'      => $this->switch_field( __( 'Add to wishlist button', 'hikashop-for-beaver-builder' ) ),
							'rating'        => $this->switch_field( __( 'Rating', 'hikashop-for-beaver-builder' ) ),
							'badges'        => $this->switch_field( __( 'Badges', 'hikashop-for-beaver-builder' ) ),
							'product_links' => $this->switch_field( __( 'Links to the product page', 'hikashop-for-beaver-builder' ) ),
						),
					),
				),
			),
			'style'   => array(
				'title'    => __( 'Style', 'hikashop-for-beaver-builder' ),
				'sections' => array(
					'title' => $this->text_style_section( 'title', __( 'Product name', 'hikashop-for-beaver-builder' ) ),
					'price' => $this->text_style_section( 'price', __( 'Price', 'hikashop-for-beaver-builder' ) ),
					'box'   => array(
						'title'  => __( 'Products', 'hikashop-for-beaver-builder' ),
						'fields' => array(
							'text_align' => array(
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
	 * Frontend and builder output.
	 */
	public function render_html() {
		$html = Listing::products( $this->settings_array(), $this->div_name( 'hikashop_bb_products' ) );
		if ( '' === $html ) {
			Render::placeholder( $this->name, __( 'No product to show with these settings.', 'hikashop-for-beaver-builder' ) );
			return;
		}
		Render::output( $html );
	}

	/**
	 * CSS of the style settings.
	 *
	 * @param string $id Node id.
	 */
	public function render_css( $id ) {
		$node = ".fl-node-$id";
		$this->text_style_css( 'title', "$node .hikashop_product_name, $node .hikashop_product_name a" );
		$this->text_style_css( 'price', "$node .hikashop_product_price" );
		\FLBuilderCSS::responsive_rule(
			array(
				'settings'     => $this->settings,
				'setting_name' => 'text_align',
				'selector'     => "$node .hikashop_listing_img_title, $node .hikashop_subcontainer",
				'prop'         => 'text-align',
			)
		);
	}
}
