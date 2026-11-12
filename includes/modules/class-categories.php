<?php
/**
 * Categories module: a HikaShop category listing.
 *
 * @package HikaShopBeaverBuilder
 */

namespace HikaShopBeaverBuilder\Modules;

use HikaShopBeaverBuilder\Listing;
use HikaShopBeaverBuilder\Render;

defined( 'ABSPATH' ) || exit;

/**
 * Category listing.
 */
class Categories extends Module {

	/**
	 * Module details.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'        => __( 'HikaShop Categories', 'hikashop-for-beaver-builder' ),
				'description' => __( 'A listing of HikaShop categories.', 'hikashop-for-beaver-builder' ),
				'slug'        => 'hikashop-categories',
				'icon'        => 'category',
			)
		);
	}

	/**
	 * Settings form.
	 *
	 * @return array
	 */
	public function form() {
		return array(
			'general' => array(
				'title'    => __( 'Categories', 'hikashop-for-beaver-builder' ),
				'sections' => array(
					'query'  => array(
						'title'  => '',
						'fields' => array(
							'category'      => $this->category_field( __( 'Parent category', 'hikashop-for-beaver-builder' ) ),
							'subcategories' => $this->yes_no_field( __( 'Include all levels below', 'hikashop-for-beaver-builder' ), '0' ),
							'hide_empty'    => $this->yes_no_field( __( 'Hide empty categories', 'hikashop-for-beaver-builder' ), '1' ),
							'order'         => array(
								'type'    => 'select',
								'label'   => __( 'Order by', 'hikashop-for-beaver-builder' ),
								'default' => 'category_ordering',
								'options' => array(
									'category_ordering' => __( 'HikaShop ordering', 'hikashop-for-beaver-builder' ),
									'category_name'     => __( 'Name', 'hikashop-for-beaver-builder' ),
									'category_created'  => __( 'Creation date', 'hikashop-for-beaver-builder' ),
									'category_modified' => __( 'Modification date', 'hikashop-for-beaver-builder' ),
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
							'limit'         => $this->number_field( __( 'Number of categories', 'hikashop-for-beaver-builder' ), 8 ),
						),
					),
					'layout' => array(
						'title'  => __( 'Layout', 'hikashop-for-beaver-builder' ),
						'fields' => array(
							'columns'     => $this->number_field( __( 'Columns', 'hikashop-for-beaver-builder' ), 4 ),
							'item_layout' => array(
								'type'    => 'select',
								'label'   => __( 'Category layout', 'hikashop-for-beaver-builder' ),
								'default' => 'inherit',
								'options' => $this->item_layout_options(),
							),
							'count'       => $this->yes_no_field( __( 'Number of products', 'hikashop-for-beaver-builder' ), '0' ),
						),
					),
				),
			),
			'style'   => array(
				'title'    => __( 'Style', 'hikashop-for-beaver-builder' ),
				'sections' => array(
					'title' => $this->text_style_section( 'title', __( 'Category name', 'hikashop-for-beaver-builder' ) ),
				),
			),
		);
	}

	/**
	 * Frontend and builder output.
	 */
	public function render_html() {
		$html = Listing::categories( $this->settings_array(), $this->div_name( 'hikashop_bb_categories' ) );
		if ( '' === $html ) {
			Render::placeholder( $this->name, __( 'No category to show with these settings.', 'hikashop-for-beaver-builder' ) );
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
		$this->text_style_css( 'title', "$node .hikashop_category_name, $node .hikashop_category_name a" );
	}
}
