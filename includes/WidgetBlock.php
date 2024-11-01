<?php
namespace GPLSCorePro\GPLS_PLUGIN_SSIG;

/**
 * Widgets as block Class.
 */
class WidgetBlock {

	/**
	 * Singular Instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Plugin Info Array.
	 *
	 * @var array
	 */
	protected static $plugin_info;

	/**
	 * Constructor.
	 *
	 * @param array $plugin_info Plugin Info Array.
	 */
	private function __construct( $plugin_info ) {
		self::$plugin_info = $plugin_info;
		$this->hooks();
	}

	/**
	 * Init Function.
	 *
	 * @return mixed
	 */
	public static function init( $plugin_info ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $plugin_info );
		}
		return self::$instance;
	}

	/**
	 * Hooks Function.
	 *
	 * @return void
	 */
	public function hooks() {
		add_filter( 'block_categories_all', array( $this, 'widgets_category' ), 100, 2 );
		add_action( 'init', array( $this, 'register_widgets_as_blocks' ), 1000 );
	}

	/**
	 * Widgets Category in Gutenberg Block.
	 *
	 * @param array $categories
	 * @return array
	 */
	public function widgets_category( $categories ) {
		$categories[] = array(
			'slug'  => self::$plugin_info['classes_prefix'] . '-widgets',
			'title' => esc_html__( 'Sidebars | Widgets Areas', 'gpls-ssig-widgets-in-gutenberg' ),
		);
		return $categories;
	}

	/**
	 * Register Widgets Blocks.
	 *
	 * @return void
	 */
	public function register_widgets_as_blocks() {
		global $pagenow, $wp_registered_sidebars;
		// Abort if widgets page.
		if ( ! empty( $pagenow ) && ( 'widgets.php' === $pagenow ) ) {
			return;
		}
		// Prepare sidebars for blockname and ID.
		$sidebars = array_map(
			function( $sidebar_id ) {
				return array(
					'blockName' => self::$plugin_info['classes_prefix'] . '/' . self::$plugin_info['name'] . '-' . $sidebar_id,
					'id'        => $sidebar_id,
				);
			},
			array_keys( $wp_registered_sidebars )
		);
		wp_register_script( self::$plugin_info['name'] . '-widgets-blocks-js', self::$plugin_info['url'] . 'assets/dist/js/admin/widgets-gutenberg.min.js', array( 'jquery', 'wp-components', 'wp-blocks', 'wp-block-editor' ), self::$plugin_info['version'], true );
		wp_localize_script(
			self::$plugin_info['name'] . '-widgets-blocks-js',
			str_replace( '-', '_', self::$plugin_info['name'] . '_localize_vars' ),
			array(
				'name'           => self::$plugin_info['name'],
				'classes_prefix' => self::$plugin_info['classes_prefix'],
				'sidebars'        => $sidebars,
				'categoryName'   => self::$plugin_info['classes_prefix'] . '-widgets',
			)
		);
		foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar_data_arr ) {
			register_block_type(
				self::$plugin_info['classes_prefix'] . '/' . self::$plugin_info['name'] . '-' . $sidebar_id,
				array(
					'title'           => $sidebar_data_arr['name'],
					'supports'        => array(
						'customClassName' => false,
					),
					'description'     => $sidebar_data_arr['description'],
					'api_version'     => 2,
					'attributes'      => array(
						'id'        => array(
							'type'    => 'string',
							'default' => $sidebar_id,
						),
						'blockName' => array(
							'type'    => 'string',
							'default' => self::$plugin_info['classes_prefix'] . '/' . self::$plugin_info['name'] . '-' . $sidebar_id,
						),
					),
					'editor_script'   => self::$plugin_info['name'] . '-widgets-blocks-js',
					'category'        => self::$plugin_info['classes_prefix'] . '-widgets',
					'render_callback' => array( $this, 'render_widget_sidebars' ),
				)
			);
		}
	}


	/**
	 * Render Widget sidebars Block.
	 *
	 * @param array $attributes Attributes Array.
	 *
	 * @return string
	 */
	public function render_widget_sidebars( $attributes ) {
		ob_start();
		dynamic_sidebar( $attributes['id'] );
		return ob_get_clean();
	}

}
