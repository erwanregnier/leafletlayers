<?php

/**
 * Controller class that implements Plugin public side controller class
 *
 * @since      1.0.0
 * @package    LeafletLayers
 * @subpackage LeafletLayers/controllers
 *
 */

if ( ! class_exists( 'LeafletLayers_Controller_Public' ) ) {

	class LeafletLayers_Controller_Public extends LeafletLayers_Controller {
		
		public static $leafletlayers_markers;
		public static $leafletlayers_groups;
		public static $markers_js;
		protected static $settings;
		protected static $model_sett;
		
		const SETTINGS_NAME = LeafletLayers::PLUGIN_ID;
		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		protected function __construct() {
			self::$model_sett = LeafletLayers_Model_Admin_Settings::get_instance();
			$this->register_hook_callbacks();
			self::$markers_js = LeafletLayers_Model::get_markers_json();
			self::$leafletlayers_groups = LeafletLayers_Model::get_groups(false);
			
		}

		/**
		 * Register callbacks for actions and filters
		 *
		 * @since    1.0.0
		 */
		protected function register_hook_callbacks() {
			LeafletLayers_Actions_Filters::add_shortcode('leafmap','leafmap_html');
			
			if( self::$model_sett->get_settings('leafletlayers_collab_form'))
			{
			LeafletLayers_Actions_Filters::add_shortcode('leafmapform','leafmap_form');
			LeafletLayers_Actions_Filters::add_action('wp_ajax_marker_submission', $this,'leafletlayers_public_add' );
			LeafletLayers_Actions_Filters::add_action('wp_ajax_nopriv_marker_submission', $this, 'leafletlayers_public_add' );
			}
			LeafletLayers_Actions_Filters::add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles' );
			LeafletLayers_Actions_Filters::add_action( 'wp_footer', $this, 'enqueue_scripts' );
			
		}
		

		/**
		 * Register the stylesheets for the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {
				
				wp_enqueue_style(
				'panelcss',
				LeafletLayers::get_plugin_url() . 'views/css/leafletlayers.css',
				array(),
				LeafletLayers::PLUGIN_VERSION,
				'all'
				);
		
		}

		/**
		 * Register the JavaScript for the public-facing side of the site.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {
			$do_load=LeafletLayers::$is_shortcode_used;
			if($do_load===true)
			{
				wp_enqueue_script(
					'leafletjs',
					LeafletLayers::get_plugin_url() . 'views/js/leaflet.js',
					array(),
					'0.7.7',
					false
				);
				
				wp_enqueue_script(
					'leafletjspanel',
					LeafletLayers::get_plugin_url() . 'views/js/leaflet-panel-layers.js',
					array('leafletjs'),
					'0.1',
					false
				);
				
				wp_enqueue_script(
					'leafletlayers',
					LeafletLayers::get_plugin_url() . 'views/js/leafletlayers.js',
					array('leafletjspanel'),
					LeafletLayers::PLUGIN_VERSION,
					true
				);
				
				if( self::$model_sett->get_settings('leafletlayers_collab_form'))
				{
					wp_enqueue_script(
						'leafletlayers_form',
						LeafletLayers::get_plugin_url() . 'views/js/leafletlayers_adder.js',
						array('leafletjs'),
						LeafletLayers::PLUGIN_VERSION,
						true
					);
					
					wp_localize_script( 'leafletlayers', 'zoom_error_txt', __('Insufficent accuracy : please zoom a little more.',LeafletLayers::PLUGIN_ID) );
					wp_localize_script( 'leafletlayers', 'leafletlayers_success_txt', __('Thanks you, the marker will be added after moderation',LeafletLayers::PLUGIN_ID) );
					wp_localize_script( 'leafletlayers', 'leafletlayers_error_txt', __('Oops : an error occured',LeafletLayers::PLUGIN_ID) );
					wp_localize_script( 'leafletlayers', 'leafletlayers_img_path', LeafletLayers::get_plugin_url() . 'views/images/' );
					wp_localize_script( 'leafletlayers', 'ajax_url', admin_url( 'admin-ajax.php' ) );
				}
				
				wp_localize_script( 'leafletlayers', 'markers_json', self::$markers_js );
				
			}

		}
		
		/**
		* Register the shortcodes
		*
		* @since	1.0.1
		* @return	string html
		*/
		public function leafmap_html($atts, $content = null, $tag)
		{
			LeafletLayers::$is_shortcode_used=true;
			$leafletlayers['domain']=LeafletLayers::PLUGIN_ID;
			$content = static::render_template(
					'leafletlayers_map.php',
					$leafletlayers
				);
			return $content;
		}
		
		public function leafmap_form($atts, $content = null, $tag)
		{
			
			LeafletLayers::$is_shortcode_used=true;
			$leafletlayers['domain']= LeafletLayers::PLUGIN_ID;
			$leafletlayers['groups']= self::$leafletlayers_groups;
			$leafletlayers['pepito']= wp_create_nonce('add_public_marker');
			
			$content = static::render_template(
					'leafletlayers_form.php',
					$leafletlayers
				);
			return $content;
			
		}
		
		/**
		* Ajax front-end submission function
		*
		* @since 1.0.0
		*/
		public function leafletlayers_public_add()
		{
			if(check_ajax_referer('add_public_marker', 'pepito', false))
			{
				if(self::$model_sett->check_and_add_marker(false))
				{
					wp_mail(get_option('admin_email'),__('New marker added on your map',LeafletLayers::PLUGIN_ID),__('Hello, a new marker is waiting approval.<br>Please login to the admin panel to validate it.',LeafletLayers::PLUGIN_ID));
					die('added');
				}
				else
				{
					die('err1');
				}	
			}
			else die('err2');
		}
		
		/**
		 * Retrieves all of the settings from the database
		 *
		 * @since    1.0.0
		 * @return array
		 */
		public static function get_settings( $setting_name = false ) {

			if ( ! isset( static::$settings ) ) {
				static::$settings = get_option( static::SETTINGS_NAME, array() );
			}

			if ( $setting_name ) {
				return isset( static::$settings[$setting_name] ) ? static::$settings[$setting_name] : array();
			}

			return static::$settings;
			
		}

	}

}