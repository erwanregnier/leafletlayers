<?php

/**
 * Loader class that includes and loads dependencies and implements activation and desactivation methods
 *
 * @since      1.0.0
 * @package    LeafletLayers
 * @subpackage LeafletLayers/includes
 *
 */

if ( ! class_exists( 'LeafletLayers_Loader' ) ) {

	class LeafletLayers_Loader {

		/**
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      LeafletLayers_Loader    $instance    Instance of this class.
		 */
		private static $instance;

		/**
		 * Provides access to a single instance of a module using the singleton pattern
		 *
		 * @since    1.0.0
		 * @return object
		 */
		public static function get_instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;

		}

		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		protected function __construct() {

			spl_autoload_register( array( $this, 'load_dependencies' ) );

			$this->set_locale();
			$this->register_hook_callbacks();

		}

		/**
		 * Loads all Plugin dependencies
		 *
		 * @since    1.0.0
		 */
		private function load_dependencies( $class ) {

			if ( false !== strpos( $class, LeafletLayers::CLASS_PREFIX ) ) {

				$classFileName = 'class-' . str_replace( '_', '-', strtolower( $class ) ) . '.php';
				$folder        = '/';

				if ( false !== strpos( $class, '_Admin' ) ) {
					$folder .= 'admin/';
				}

				if ( false !== strpos( $class, LeafletLayers::CLASS_PREFIX . 'Controller' ) ) {
					$path = LeafletLayers::get_plugin_path() . 'controllers' . $folder . $classFileName;
					require_once( $path );
				} elseif ( false !== strpos( $class, LeafletLayers::CLASS_PREFIX . 'Model' ) ) {
					$path = LeafletLayers::get_plugin_path() . 'models' . $folder . $classFileName;
					require_once( $path );
				} else {
					$path = LeafletLayers::get_plugin_path() . 'includes/' . $classFileName;
					require_once( $path );
				}

			}

		}

		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * Uses the LeafletLayers_i18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.0.0.0
		 */
		private function set_locale() {

			$plugin_i18n = new LeafletLayers_i18n();
			$plugin_i18n->set_domain( LeafletLayers::PLUGIN_ID );

			LeafletLayers_Actions_Filters::add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

		}

		/**
		 * Register callbacks for actions and filters
		 *
		 * @since    1.0.0.0
		 */
		public function register_hook_callbacks() {

			register_activation_hook(   LeafletLayers::get_plugin_path() . LeafletLayers::PLUGIN_ID . '.php', array( $this, 'activate' ) );
			register_deactivation_hook( __FILE__, 'deactivate' );

		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {
			// Create DB for markers and markers groups
			global $wpdb;			
			$table_name = $wpdb->prefix . 'leafletlayers_markers';
			$table_name_groups = $wpdb->prefix . 'leafletlayers_markers_groups';
			$charset_collate = $wpdb->get_charset_collate();	
			$sql = "CREATE TABLE $table_name (
			`id` mediumint(9) NOT NULL AUTO_INCREMENT,
			`id_group` tinyint(4) NOT NULL,
			`lat` DECIMAL(18, 15) NOT NULL,
			`lng` DECIMAL(18, 15) NOT NULL,
			`title` VARCHAR( 255 ) NOT NULL,
			`desc` TINYTEXT NOT NULL,
			`address` TINYTEXT NOT NULL,
			`moderated` BOOLEAN NOT NULL,
			UNIQUE KEY `id` (id)
			) $charset_collate;
						
			CREATE TABLE $table_name_groups (
			`id` tinyint(4) NOT NULL AUTO_INCREMENT,
			`title` VARCHAR( 255 ) NOT NULL,
			UNIQUE KEY `id` (id)
			) $charset_collate;
			";			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			
		}


		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {

			LeafletLayers_Model_Admin_Notices::remove_admin_notices();
			global $wpdb;			
			$table_name = $wpdb->prefix . 'leafletlayers_markers';
			$table_name_groups = $wpdb->prefix . 'leafletlayers_markers_groups';	
			$wpdb->query("DROP TABLE IF EXISTS $table_name;");
			$wpdb->query("DROP TABLE IF EXISTS $table_name_groups;");
		}

		/**
		 * Fired when user uninstalls the plugin, called in uninstall.php file
		 *
		 * @since    1.0.0
		 */
		public static function uninstall_plugin() {

			require_once dirname( plugin_dir_path( __FILE__ ) ) . '/includes/class-leafletlayers.php';
			require_once dirname( plugin_dir_path( __FILE__ ) ) . '/models/class-leafletlayers-model.php';
			require_once dirname( plugin_dir_path( __FILE__ ) ) . '/models/admin/class-leafletlayers-model-admin.php';
			require_once dirname( plugin_dir_path( __FILE__ ) ) . '/models/admin/class-leafletlayers-model-admin-settings.php';
			global $wpdb;			
			$table_name = $wpdb->prefix . 'leafletlayers_markers';
			$table_name_groups = $wpdb->prefix . 'leafletlayers_markers_groups';	
			$wpdb->query("DROP TABLE IF EXISTS $table_name; DROP TABLE IF EXISTS $table_name_groups;");
			LeafletLayers_Model_Admin_Settings::delete_settings();

		}

	}

}