<?php

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    LeafletLayers
 * @subpackage LeafletLayers/includes
 *
 */

if ( ! class_exists( 'LeafletLayers' ) ) {

	class LeafletLayers {

		/**
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      LeafletLayers    $instance    Instance of this class.
		 */
		private static $instance;

		/**
		 * The modules variable holds all modules of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      object    $modules    Maintains all modules of the plugin.
		 */
		private static $modules = array();

		/**
		 * Main plugin path /wp-content/plugins/<plugin-folder>/.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_path    Main path.
		 */
		private static $plugin_path;

		/**
		 * Absolute plugin url <wordpress-root-folder>/wp-content/plugins/<plugin-folder>/.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_url    Main path.
		 */
		private static $plugin_url;
		
		/**
		 * Add JS and styles only if shortcode is used
		 *
		 * @since	1.0.0
		 * @access	private
		 * @var	boolean		
		*/
		public static $is_shortcode_used = false;

		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 */
		const PLUGIN_ID 		= 'leafletlayers';

		/**
		 * The name identifier of this plugin.
		 *
		 * @since    1.0.0
		 */
		const PLUGIN_NAME 		= 'LeafletLayers';


		/**
		 * The current version of the plugin.
		 *
		 * @since    1.0.0
		 */
		const PLUGIN_VERSION 	= '1.0';

		/**
		 * The plugin prefix to referenciate classes inside the plugin
		 *
		 * @since    1.0.0
		 */
		const CLASS_PREFIX 		= 'LeafletLayers_';

		/**
		 * The plugin prefix to referenciate files and prefixes inside the plugin
		 *
		 * @since    1.0.0
		 */
		const PLUGIN_PREFIX 	= 'leafletlayers-';
		
		/**
		 * Provides access to a single instance of a module using the singleton pattern
		 *
		 * @return object
		 *
		 * @since    1.0.0
		 */
		public static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;

		}

		/**
		 * Define the core functionality of the plugin.
		 *
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

			self::$plugin_path = plugin_dir_path( dirname( __FILE__ ) );
			self::$plugin_url  = plugin_dir_url( dirname( __FILE__ ) );

			require_once( self::$plugin_path . 'includes/class-' . self::PLUGIN_PREFIX . 'loader.php' );

			self::$modules['LeafletLayers_Loader']                    = LeafletLayers_Loader::get_instance();
			self::$modules['LeafletLayers_Controller_Public']         = LeafletLayers_Controller_Public::get_instance();
			self::$modules['LeafletLayers_Controller_Admin_Settings'] = LeafletLayers_Controller_Admin_Settings::get_instance();
			self::$modules['LeafletLayers_Controller_Admin_Notices']  = LeafletLayers_Controller_Admin_Notices::get_instance();
			self::$modules['LeafletLayers_Controller_Admin_Menu'] = LeafletLayers_Controller_Admin_Menu::get_instance();
			
			LeafletLayers_Actions_Filters::init_actions_filters();

		}

		/**
		 * Get plugin's absolute path.
		 *
		 * @since    1.0.0
		 */
		public static function get_plugin_path() {

			return isset( self::$plugin_path ) ? self::$plugin_path : plugin_dir_path( dirname( __FILE__ ) );

		}

		/**
		 * Get plugin's absolute url.
		 *
		 * @since    1.0.0
		 */
		public static function get_plugin_url() {

			return isset( self::$plugin_url ) ? self::$plugin_url : plugin_dir_url( dirname( __FILE__ ) );

		}

	}

}