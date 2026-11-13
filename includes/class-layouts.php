<?php
/**
 * HikaShop product pages rendered with a Beaver Builder layout.
 *
 * @package HikaShopBeaverBuilder
 */

namespace HikaShopBeaverBuilder;

defined( 'ABSPATH' ) || exit;

/**
 * The "HikaShop layouts" post type, the product page layout setting and its rendering.
 */
final class Layouts {

	const POST_TYPE = 'hikashop_bb_layout';
	const OPTION    = 'hikashop_bb_product_layout';

	/**
	 * Hooks the post type, the setting and the page rendering.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_filter( 'fl_builder_post_types', array( __CLASS__, 'enable_builder' ) );
		add_action( 'template_redirect', array( __CLASS__, 'protect_layout_pages' ) );
		add_filter( 'wp_robots', array( __CLASS__, 'layout_robots' ) );
		add_action( 'wp', array( __CLASS__, 'enqueue_layout_assets' ), 13 );
		add_filter( 'hikashop_wp_page_output', array( __CLASS__, 'page_output' ), 10, 4 );
		add_action( 'admin_init', array( __CLASS__, 'register_setting' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_page' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( HIKASHOP_BB_FILE ), array( __CLASS__, 'action_links' ) );
	}

	/**
	 * Layouts are edited with Beaver Builder on their own URL, which only editors can open.
	 */
	public static function register_post_type() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'          => __( 'HikaShop layouts', 'hikashop-for-beaver-builder' ),
					'singular_name' => __( 'HikaShop layout', 'hikashop-for-beaver-builder' ),
					'add_new_item'  => __( 'Add a HikaShop layout', 'hikashop-for-beaver-builder' ),
					'edit_item'     => __( 'Edit the HikaShop layout', 'hikashop-for-beaver-builder' ),
					'all_items'     => __( 'HikaShop layouts', 'hikashop-for-beaver-builder' ),
				),
				'public'              => false,
				'publicly_queryable'  => true,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_nav_menus'   => false,
				'show_in_rest'        => false,
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => self::POST_TYPE,
				'supports'            => array( 'title', 'editor', 'revisions' ),
				'capability_type'     => 'page',
				'map_meta_cap'        => true,
			)
		);
	}

	/**
	 * Turns Beaver Builder on for the layouts.
	 *
	 * @param array $post_types Post types the builder edits.
	 * @return array
	 */
	public static function enable_builder( $post_types ) {
		$post_types   = (array) $post_types;
		$post_types[] = self::POST_TYPE;
		return array_values( array_unique( $post_types ) );
	}

	/**
	 * A layout's own URL is a 404 for anyone who cannot edit it.
	 */
	public static function protect_layout_pages() {
		if ( ! is_singular( self::POST_TYPE ) || current_user_can( 'edit_post', get_queried_object_id() ) ) {
			return;
		}
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}

	/**
	 * Search engines never index a layout's own URL.
	 *
	 * @param array $robots Robots directives.
	 * @return array
	 */
	public static function layout_robots( $robots ) {
		if ( is_singular( self::POST_TYPE ) ) {
			$robots['noindex']  = true;
			$robots['nofollow'] = true;
		}
		return $robots;
	}

	/**
	 * Post types a product page layout can come from: ours, and the saved layouts of the paid Beaver Builder.
	 *
	 * @return string[]
	 */
	private static function layout_post_types() {
		$types = array( self::POST_TYPE );
		if ( post_type_exists( 'fl-builder-template' ) ) {
			$types[] = 'fl-builder-template';
		}
		return $types;
	}

	/**
	 * Id of the chosen layout, 0 when HikaShop shows its own product page.
	 *
	 * @return int
	 */
	public static function layout_id() {
		$id = absint( get_option( self::OPTION, 0 ) );
		if ( ! $id || ! in_array( get_post_type( $id ), self::layout_post_types(), true ) || 'publish' !== get_post_status( $id ) ) {
			return 0;
		}
		return $id;
	}

	/**
	 * Whether the request is a HikaShop product page.
	 *
	 * @return bool
	 */
	private static function is_product_page() {
		if ( ! function_exists( 'hikashop_wp_seo_data' ) ) {
			return false;
		}
		$data = hikashop_wp_seo_data();
		return is_array( $data ) && isset( $data['type'] ) && 'product' === $data['type'];
	}

	/**
	 * The layout's CSS and JavaScript go in the head, before HikaShop renders the page in the content.
	 */
	public static function enqueue_layout_assets() {
		if ( is_admin() || ! self::layout_id() || ! self::is_product_page() ) {
			return;
		}
		\FLBuilder::enqueue_layout_styles_scripts_by_id( self::layout_id() );
		Render::enqueue_shop_assets();
	}

	/**
	 * Replaces HikaShop's product page by the layout.
	 *
	 * @param string|null $output  Output of an earlier filter, null to let HikaShop render.
	 * @param string      $view    HikaShop view.
	 * @param string      $layout  HikaShop layout.
	 * @param string      $content Page content.
	 * @return string|null
	 */
	public static function page_output( $output, $view, $layout, $content = '' ) {
		unset( $content );
		if ( null !== $output || 'product' !== $view || 'show' !== $layout ) {
			return $output;
		}
		$layout_id = self::layout_id();
		if ( ! $layout_id || ! self::product_viewable() ) {
			return $output;
		}

		Render::enqueue_shop_assets();
		ob_start();
		\FLBuilder::render_content_by_id( $layout_id );
		$html = (string) ob_get_clean();
		if ( '' === trim( $html ) ) {
			return $output;
		}
		return '<div class="hikashop-bb-product-page hikashop_product_page">' . $html . '</div>';
	}

	/**
	 * Whether the product of the request exists and the visitor may see it; otherwise HikaShop answers.
	 *
	 * @return bool
	 */
	private static function product_viewable() {
		$product_id = \hikaInput::get()->getInt( 'cid', 0 );
		if ( ! $product_id ) {
			$product_id = \hikaInput::get()->getInt( 'product_id', 0 );
		}
		if ( ! $product_id ) {
			return false;
		}
		$product_class = hikashop_get( 'class.product' );
		$product       = $product_class->get( $product_id );
		if ( empty( $product ) || 'main' !== $product->product_type || empty( $product->product_published ) ) {
			return false;
		}
		if ( method_exists( $product_class, 'isHiddenInLanguage' ) && $product_class->isHiddenInLanguage( $product ) ) {
			return false;
		}
		if ( function_exists( 'hikashop_isAllowed' ) && isset( $product->product_access ) && ! hikashop_isAllowed( $product->product_access ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Registers the option.
	 */
	public static function register_setting() {
		register_setting(
			'hikashop_bb',
			self::OPTION,
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);
	}

	/**
	 * Adds Settings > HikaShop for Beaver Builder.
	 */
	public static function add_page() {
		add_options_page(
			__( 'HikaShop for Beaver Builder', 'hikashop-for-beaver-builder' ),
			__( 'HikaShop for Beaver Builder', 'hikashop-for-beaver-builder' ),
			'manage_options',
			'hikashop-for-beaver-builder',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Settings link in the plugin list.
	 *
	 * @param array $links Plugin action links.
	 * @return array
	 */
	public static function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=hikashop-for-beaver-builder' ) ) . '">' . esc_html__( 'Settings', 'hikashop-for-beaver-builder' ) . '</a>' );
		return $links;
	}

	/**
	 * The settings page: the layout setting and the list of layouts.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$layouts = get_posts(
			array(
				'post_type'      => self::layout_post_types(),
				'post_status'    => array( 'publish', 'draft' ),
				'posts_per_page' => 200,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		$current = absint( get_option( self::OPTION, 0 ) );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'HikaShop for Beaver Builder', 'hikashop-for-beaver-builder' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'hikashop_bb' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="hikashop_bb_product_layout"><?php esc_html_e( 'Product page layout', 'hikashop-for-beaver-builder' ); ?></label></th>
						<td>
							<select name="<?php echo esc_attr( self::OPTION ); ?>" id="hikashop_bb_product_layout">
								<option value="0"><?php esc_html_e( 'None: HikaShop shows its own product page', 'hikashop-for-beaver-builder' ); ?></option>
								<?php foreach ( $layouts as $layout ) : ?>
									<?php
									if ( 'publish' !== $layout->post_status ) {
										continue;
									}
									?>
									<option value="<?php echo esc_attr( $layout->ID ); ?>" <?php selected( $current, $layout->ID ); ?>><?php echo esc_html( $layout->post_title ? $layout->post_title : '#' . $layout->ID ); ?></option>
								<?php endforeach; ?>
							</select>
							<p class="description"><?php esc_html_e( 'Every HikaShop product page is shown with this layout. Design it with the product modules of the HikaShop group (title, images, price, add to cart...): they show the product being viewed.', 'hikashop-for-beaver-builder' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>

			<h2><?php esc_html_e( 'Layouts', 'hikashop-for-beaver-builder' ); ?></h2>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . self::POST_TYPE ) ); ?>"><?php esc_html_e( 'Add a layout', 'hikashop-for-beaver-builder' ); ?></a>
				&nbsp;
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . self::POST_TYPE ) ); ?>"><?php esc_html_e( 'Manage the layouts', 'hikashop-for-beaver-builder' ); ?></a>
			</p>
			<?php if ( empty( $layouts ) ) : ?>
				<p><?php esc_html_e( 'No layout yet. Add one, then open it with Beaver Builder and place the HikaShop product modules in it.', 'hikashop-for-beaver-builder' ); ?></p>
			<?php else : ?>
				<ul>
					<?php foreach ( $layouts as $layout ) : ?>
						<li>
							<?php echo esc_html( $layout->post_title ? $layout->post_title : '#' . $layout->ID ); ?>
							<?php if ( 'publish' !== $layout->post_status ) : ?>
								(<?php esc_html_e( 'draft', 'hikashop-for-beaver-builder' ); ?>)
							<?php endif; ?>
							&middot; <a href="<?php echo esc_url( \FLBuilderModel::get_edit_url( $layout->ID ) ); ?>"><?php esc_html_e( 'Edit with Beaver Builder', 'hikashop-for-beaver-builder' ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}
}
