<?php
/**
 * Common base of the HikaShop modules.
 *
 * @package HikaShopBeaverBuilder
 */

namespace HikaShopBeaverBuilder\Modules;

defined( 'ABSPATH' ) || exit;

/**
 * Shared settings and rendering of every HikaShop module.
 */
abstract class Module extends \FLBuilderModule {

	/**
	 * Sets the module up; every HikaShop module renders through modules/hikashop/includes/.
	 *
	 * @param array $params Module name, slug, description and icon.
	 */
	public function __construct( $params ) {
		parent::__construct(
			array_merge(
				array(
					'category'        => __( 'HikaShop', 'hikashop-for-beaver-builder' ),
					'dir'             => HIKASHOP_BB_DIR . 'modules/hikashop/',
					'url'             => HIKASHOP_BB_URL . 'modules/hikashop/',
					'partial_refresh' => true,
					'editor_export'   => false,
				),
				$params
			)
		);
	}

	/**
	 * Settings form of the module.
	 *
	 * @return array
	 */
	abstract public function form();

	/**
	 * Prints the module output.
	 */
	abstract public function render_html();

	/**
	 * Adds the CSS rules of the style settings.
	 *
	 * @param string $id Node id.
	 */
	public function render_css( $id ) {
		unset( $id );
	}

	/**
	 * Settings as an array.
	 *
	 * @return array
	 */
	protected function settings_array() {
		$settings = array();
		foreach ( (array) $this->settings as $key => $value ) {
			$settings[ $key ] = is_scalar( $value ) || is_array( $value ) ? $value : '';
		}
		return $settings;
	}

	/**
	 * Node id, safe for HTML ids.
	 *
	 * @return string
	 */
	protected function node_id() {
		return preg_replace( '/[^a-z0-9]/i', '', (string) $this->node );
	}

	/**
	 * Unique id of this module instance for HikaShop's listing.
	 *
	 * @param string $prefix Prefix of the id.
	 * @return string
	 */
	protected function div_name( $prefix ) {
		return $prefix . '_' . $this->node_id();
	}

	/**
	 * The "Inherit from HikaShop / Show / Hide" choices.
	 *
	 * @return array
	 */
	protected function switch_options() {
		return array(
			''  => __( 'Inherit from HikaShop', 'hikashop-for-beaver-builder' ),
			'1' => __( 'Show', 'hikashop-for-beaver-builder' ),
			'0' => __( 'Hide', 'hikashop-for-beaver-builder' ),
		);
	}

	/**
	 * An "Inherit / Show / Hide" select.
	 *
	 * @param string $label Label.
	 * @return array
	 */
	protected function switch_field( $label ) {
		return array(
			'type'    => 'select',
			'label'   => $label,
			'default' => '',
			'options' => $this->switch_options(),
		);
	}

	/**
	 * A "Yes / No" select.
	 *
	 * @param string $label   Label.
	 * @param string $default Default value, '1' or '0'.
	 * @return array
	 */
	protected function yes_no_field( $label, $default ) {
		return array(
			'type'    => 'select',
			'label'   => $label,
			'default' => $default,
			'options' => array(
				'1' => __( 'Yes', 'hikashop-for-beaver-builder' ),
				'0' => __( 'No', 'hikashop-for-beaver-builder' ),
			),
		);
	}

	/**
	 * A number field.
	 *
	 * @param string $label   Label.
	 * @param int    $default Default value.
	 * @return array
	 */
	protected function number_field( $label, $default ) {
		return array(
			'type'    => 'unit',
			'label'   => $label,
			'default' => (string) $default,
			'slider'  => false,
		);
	}

	/**
	 * Category select; Plugin::field_config() fills in the categories when the builder loads the form.
	 *
	 * @param string $label Label.
	 * @return array
	 */
	protected function category_field( $label ) {
		return array(
			'type'              => 'select',
			'label'             => $label,
			'default'           => '0',
			'options'           => array( '0' => __( 'All categories', 'hikashop-for-beaver-builder' ) ),
			'hikashop_category' => true,
		);
	}

	/**
	 * The item layouts of HikaShop listings.
	 *
	 * @return array
	 */
	protected function item_layout_options() {
		return array(
			'inherit'           => __( 'Inherit from HikaShop', 'hikashop-for-beaver-builder' ),
			'img_title'         => __( 'Image and title', 'hikashop-for-beaver-builder' ),
			'img'               => __( 'Image', 'hikashop-for-beaver-builder' ),
			'title'             => __( 'Title', 'hikashop-for-beaver-builder' ),
			'img_desc'          => __( 'Image and description', 'hikashop-for-beaver-builder' ),
			'img_description'   => __( 'Image, title and description', 'hikashop-for-beaver-builder' ),
			'img_pane'          => __( 'Image with a sliding pane', 'hikashop-for-beaver-builder' ),
			'fade'              => __( 'Fade', 'hikashop-for-beaver-builder' ),
			'hover_x_img_title' => __( 'Image swap on hover', 'hikashop-for-beaver-builder' ),
			'slider_horizontal' => __( 'Horizontal slider', 'hikashop-for-beaver-builder' ),
			'slider_vertical'   => __( 'Vertical slider', 'hikashop-for-beaver-builder' ),
		);
	}

	/**
	 * Color and typography fields of one element of the module.
	 *
	 * @param string $prefix Setting name prefix.
	 * @param string $title  Section title.
	 * @return array Section.
	 */
	protected function text_style_section( $prefix, $title ) {
		return array(
			'title'  => $title,
			'fields' => array(
				$prefix . '_color'      => array(
					'type'        => 'color',
					'label'       => __( 'Color', 'hikashop-for-beaver-builder' ),
					'show_reset'  => true,
					'show_alpha'  => true,
					'responsive'  => true,
					'connections' => array( 'color' ),
				),
				$prefix . '_typography' => array(
					'type'       => 'typography',
					'label'      => __( 'Typography', 'hikashop-for-beaver-builder' ),
					'responsive' => true,
				),
			),
		);
	}

	/**
	 * CSS rules of a text_style_section().
	 *
	 * @param string $prefix   Setting name prefix.
	 * @param string $selector CSS selector.
	 */
	protected function text_style_css( $prefix, $selector ) {
		\FLBuilderCSS::responsive_rule(
			array(
				'settings'     => $this->settings,
				'setting_name' => $prefix . '_color',
				'selector'     => $selector,
				'prop'         => 'color',
				'enabled'      => 'not_empty',
			)
		);
		\FLBuilderCSS::typography_field_rule(
			array(
				'settings'     => $this->settings,
				'setting_name' => $prefix . '_typography',
				'selector'     => $selector,
			)
		);
	}
}
