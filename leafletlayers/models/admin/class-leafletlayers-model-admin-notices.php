<?php

/**
 * Model class that implements Plugin Admin Notices messages
 *
 * @since      1.0.0
 * @package    LeafletLayers
 * @subpackage LeafletLayers/models/admin
 *
 */

if ( ! class_exists( 'LeafletLayers_Model_Admin_Notices' ) ) {

	class LeafletLayers_Model_Admin_Notices extends LeafletLayers_Model_Admin_Settings {

		const ADMIN_NOTICES_SETTINGS_NAME = 'admin_notices';


		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		protected function __construct() { }

		/** 
		 * Show admin notices if plugin activation had any error
		 *
		 * @since    1.0.0
		 */
		public static function show_admin_notices() {

			$admin_notices = static::get_admin_notices();

			if ( ! empty( $admin_notices ) ) {

				foreach ( $admin_notices as $admin_notice ) {
					echo $admin_notice;
				}
				static::remove_admin_notices();

			}

		}

		/** 
		 * Helper to add Plugin Admin Notices
		 *
		 * @since    1.0.0
		 */
		public static function add_admin_notice( $notice ) {

			$admin_notices = static::get_admin_notices();

			if ( empty( $admin_notices ) || ! is_array( $admin_notices ) ) {
				$admin_notices = array();
			}

			$admin_notices[] = $notice;

			$settings = static::get_settings();
			$settings[static::ADMIN_NOTICES_SETTINGS_NAME] = $admin_notices;

			return static::update_settings( $settings );

		}

		/** 
		 * Helper to remove all Plugin Admin Notices
		 *
		 * @since    1.0.0
		 */
		public static function remove_admin_notices() {

			$admin_notices = static::get_admin_notices();
			if ( ! empty( $admin_notices )  ) {

				return static::delete_settings( static::ADMIN_NOTICES_SETTINGS_NAME );

			}

			return true;

		}

		/** 
		 * Helper to get Plugin Admin Notices
		 *
		 * @since    1.0.0
		 * @return   boleean
		 */
		private static function get_admin_notices() {

			$admin_notices = static::get_settings( static::ADMIN_NOTICES_SETTINGS_NAME );
			if ( ! empty( $admin_notices ) ) {
				return $admin_notices;
			}

			return false;

		}

	}

}