<?php
namespace GPLSCore\GPLS_PLUGIN_SSIG;

/**
 * Plugin Name:     Sidebars Blocks
 * Description:     Use Full Sidebars and Widgets into Gutenberg as blocks.
 * Author:          GrandPlugins
 * Author URI:      https://grandplugins.com
 * Text Domain:     gpls-ssig-widgets-in-gutenberg
 * Std Name:        gpls-ssig-widgets-in-gutenberg
 * Version:         1.0.2
 *
 * @package         Widgets_In_Gutenberg
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use GPLSCorePro\GPLS_PLUGIN_SSIG\WidgetBlock;

if ( ! class_exists( __NAMESPACE__ . '\GPLS_SSIG_Class' ) ) :


	/**
	 * Main Class.
	 */
	class GPLS_SSIG_Class {

		/**
		 * The class Single Instance.
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Plugin Info
		 *
		 * @var array
		 */
		private static $plugin_info;

		/**
		 * Initialize the class instance.
		 *
		 * @return object
		 */
		public static function init() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Plugin Activated Function
		 *
		 * @return void
		 */
		public static function plugin_activated() {
		}

		/**
		 * Plugin Deactivated Hook.
		 *
		 * @return void
		 */
		public static function plugin_deactivated() {
		}

		/**
		 * Class Constructor.
		 */
		public function __construct() {
			self::setup_plugin_info();
			$this->load_languages();
			$this->includes();
			$this->load();
		}

		/**
		 * Load Classes.
		 *
		 * @return void
		 */
		public function load() {
			WidgetBlock::init( self::$plugin_info );
		}

		/**
		 * Define Constants
		 *
		 * @param string $key
		 * @param string $value
		 * @return void
		 */
		public function define( $key, $value ) {
			if ( ! defined( $key ) ) {
				define( $key, $value );
			}
		}

		/**
		 * Set Plugin Info
		 *
		 * @return array
		 */
		public static function setup_plugin_info() {
			$plugin_data = get_file_data(
				__FILE__,
				array(
					'Version'     => 'Version',
					'Name'        => 'Plugin Name',
					'URI'         => 'Plugin URI',
					'SName'       => 'Std Name',
					'text_domain' => 'Text Domain',
				),
				false
			);

			self::$plugin_info = array(
				'id'              => 14,
				'basename'        => plugin_basename( __FILE__ ),
				'version'         => $plugin_data['Version'],
				'name'            => $plugin_data['SName'],
				'text_domain'     => $plugin_data['text_domain'],
				'file'            => __FILE__,
				'plugin_url'      => $plugin_data['URI'],
				'public_name'     => $plugin_data['Name'],
				'path'            => trailingslashit( plugin_dir_path( __FILE__ ) ),
				'url'             => trailingslashit( plugin_dir_url( __FILE__ ) ),
				'options_page'    => $plugin_data['SName'],
				'localize_var'    => str_replace( '-', '_', $plugin_data['SName'] ) . '_localize_data',
				'type'            => 'free',
				'classes_prefix'  => 'gpls-ssig',
				'related_plugins' => array(),
			);
		}

		/**
		 * Include plugin files
		 *
		 * @return void
		 */
		public function includes() {
			require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'vendor/autoload.php';
		}

		/**
		 * Load languages Folder.
		 *
		 * @return void
		 */
		public function load_languages() {
			load_plugin_textdomain( self::$plugin_info['text_domain'], false, self::$plugin_info['path'] . 'languages/' );
		}

	}

	add_action( 'plugins_loaded', array( __NAMESPACE__ . '\GPLS_SSIG_Class', 'init' ), 1 );
	register_activation_hook( __FILE__, array( __NAMESPACE__ . '\GPLS_SSIG_Class', 'plugin_activated' ) );
	register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\GPLS_SSIG_Class', 'plugin_deactivated' ) );

endif;
