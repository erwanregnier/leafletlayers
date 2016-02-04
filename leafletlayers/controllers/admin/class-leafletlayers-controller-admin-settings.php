<?php

/**
 * Controller class that implements Plugin Admin Settings configurations
 *
 * @since      1.0.0
 * @package    LeafletLayers
 * @subpackage LeafletLayers/controllers/admin
 *
 */

if ( ! class_exists( 'LeafletLayers_Controller_Admin_Settings' ) ) {

	class LeafletLayers_Controller_Admin_Settings extends LeafletLayers_Controller_Admin {

		private static $hook_suffix = '';

		const SETTINGS_PAGE_URL = LeafletLayers::PLUGIN_ID;
		const REQUIRED_CAPABILITY = 'manage_options';


		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		protected function __construct() {

			static::$hook_suffix = 'settings_page_' . LeafletLayers::PLUGIN_ID;

			$this->register_hook_callbacks();
			$this->model = LeafletLayers_Model_Admin_Settings::get_instance();

		}

		/**
		 * Register callbacks for actions and filters
		 *
		 * @since    1.0.0
		 */
		protected function register_hook_callbacks() {

			LeafletLayers_Actions_Filters::add_action( 'admin_menu',                                  $this, 'plugin_menu' );
			LeafletLayers_Actions_Filters::add_action( 'load-' . static::$hook_suffix,                $this, 'register_fields' );

			LeafletLayers_Actions_Filters::add_filter(
				'plugin_action_links_' . LeafletLayers::PLUGIN_ID . '/' . LeafletLayers::PLUGIN_ID . '.php',
				$this,
				'add_plugin_action_links'
			);

		}

		/** 
		 * Create menu for Plugin inside Settings menu
		 *
		 * @since    1.0.0
		 */
		public function plugin_menu() {

			static::$hook_suffix = add_options_page(
				__( LeafletLayers::PLUGIN_NAME ),        // Page Title
				__( LeafletLayers::PLUGIN_NAME ),        // Menu Title
				static::REQUIRED_CAPABILITY,           // Capability
				static::SETTINGS_PAGE_URL,             // Menu URL
				array( $this, 'markup_settings_page' ) // Callback
			);

		}

		/**
		 * Creates the markup for the Settings page
		 *
		 * @since    1.0.0
		 */
		public function markup_settings_page() {

			if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {

				echo static::render_template(
					'page-settings/page-settings.php',
					array(
						'page_title' 	=> LeafletLayers::PLUGIN_NAME,
						'settings_name' => LeafletLayers_Model_Admin_Settings::SETTINGS_NAME,
						'domain' => LeafletLayers::PLUGIN_ID
					)
				);

			} else {

				wp_die( __( 'Access denied.' ) );

			}

		}

		/**
		 * Registers settings sections and fields
		 *
		 * @since    1.0.0
		 */
		public function register_fields() {

			// Add Settings Page Section
			add_settings_section(
				'leafletlayers_section',                    // Section ID
				__( 'Plugin\'s configuration',LeafletLayers::PLUGIN_ID),                         // Section Title
				array( $this, 'markup_section_headers' ), // Section Callback
				static::SETTINGS_PAGE_URL                 // Page URL
			);

			add_settings_field(
				'leafletlayers_collab_form',                        // Field ID
				__( 'Activate front-end add form:',LeafletLayers::PLUGIN_ID ),                 // Field Title 
				array( $this, 'markup_fields_checkbox' ),            // Field Callback
				static::SETTINGS_PAGE_URL,                  // Page
				'leafletlayers_section',                      // Section ID
				array(                                      // Field args
					'id'        => 'leafletlayers_collab_form',
					'label_for' => 'leafletlayers_collab_form'				) 
			);

		}

		/**
		 * Adds the section introduction text to the Settings page
		 *
		 * @param array $section
		 *
		 * @since    1.0.0
		 */
		public function markup_section_headers( $section ) {

			echo static::render_template(
				'page-settings/page-settings-section-headers.php',
				array(
					'section'      => $section,
					'information_text' => __( 'Here you can set the permission of [leafmapform] usage.',LeafletLayers::PLUGIN_ID )
				)
			);
		
		}

		/**
		 * Delivers the markup for settings fields
		 *
		 * @param array $args
		 *
		 * @since    1.0.0
		 */
		public function markup_fields( $field_args ) {

			$field_id = $field_args['id'];
			$settings_value = static::get_model()->get_settings( $field_id );

			echo static::render_template(
				'page-settings/page-settings-fields.php',
				array(
					'field_id'       => esc_attr( $field_id ),
					'settings_name'  => LeafletLayers_Model_Admin_Settings::SETTINGS_NAME,
					'settings_value' => ! empty( $settings_value ) ? esc_attr( $settings_value ) : ''
				),
				'always'
			);
		
		}
		
		/**
		 * Delivers the markup for settings fields type checkbox
		 *
		 * @param array $args
		 *
		 * @since    1.0.0
		 */
		public function markup_fields_checkbox( $field_args ) {

			$field_id = $field_args['id'];
			$control_value = static::get_model()->get_settings( $field_id );
			echo static::render_template(
				'page-settings/page-settings-fields-checkbox.php',
				array(
					'field_id'       => esc_attr( $field_id ),
					'settings_name'  => LeafletLayers_Model_Admin_Settings::SETTINGS_NAME,
					'control_value' => $control_value
				),
				'always'
			);
		
		}

		/**
		 * Adds links to the plugin's action link section on the Plugins page
		 *
		 * @param array $links The links currently mapped to the plugin
		 * @return array
		 *
		 * @since    1.0.0
		 */
		public function add_plugin_action_links( $links ) {

			$settings_link = '<a href="options-general.php?page=' . static::SETTINGS_PAGE_URL . '">' . __( 'Settings',LeafletLayers::PLUGIN_ID ) . '</a>';
			array_unshift( $links, $settings_link );
			//Disallow plugins editor
			if ( array_key_exists( 'edit', $links ) ) unset( $links['edit'] );
			return $links;

		}
		
		/**
		 * Uninstall function
		 *
		 * @since    1.0.1
		 */
		public function delete_settings()
		{
			delete_option( LeafletLayers_Model_Admin_Settings::SETTINGS_NAME );
		}
	}

}