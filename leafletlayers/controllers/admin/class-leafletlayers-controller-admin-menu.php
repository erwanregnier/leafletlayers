<?php

/**
 * Controller class that implements Plugin Admin Menu and templates
 *
 * @since      1.0.0
 * @package    LeafletLayers
 * @subpackage LeafletLayers/controllers/admin
 *
 */

if ( ! class_exists( 'LeafletLayers_Controller_Admin_Menu' ) ) {

	class LeafletLayers_Controller_Admin_Menu extends LeafletLayers_Controller_Admin {

		const REQUIRED_CAPABILITY = 'manage_options';
		private static $hook_suffix_add = '';
		private static $hook_suffix_edit = '';


		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		protected function __construct() {

			$this->register_hook_callbacks();
			$this->model = LeafletLayers_Model_Admin::get_instance();

		}

		/**
		 * Register callbacks for actions and filters
		 *
		 * @since    1.0.0
		 */
		protected function register_hook_callbacks() {

			LeafletLayers_Actions_Filters::add_action( 'admin_menu',$this, 'plugin_menu' );
			LeafletLayers_Actions_Filters::add_action( 'admin_post_leafletlayers_add_marker',$this, 'add_marker' );
			LeafletLayers_Actions_Filters::add_action( 'admin_post_leafletlayers_edit_marker',$this, 'save_edited_marker' );
			LeafletLayers_Actions_Filters::add_action( 'admin_post_leafletlayers_delete_marker',$this, 'delete_marker' );
			LeafletLayers_Actions_Filters::add_action( 'admin_post_leafletlayers_validate_marker',$this, 'validate_marker' );
			LeafletLayers_Actions_Filters::add_action( 'admin_post_leafletlayers_add_group',$this, 'add_group' );
			LeafletLayers_Actions_Filters::add_action( 'admin_post_leafletlayers_edit_group',$this, 'save_edited_group' );
			LeafletLayers_Actions_Filters::add_action( 'admin_post_leafletlayers_delete_group',$this, 'delete_group' );
			LeafletLayers_Actions_Filters::add_action( 'admin_enqueue_scripts', $this, 'enqueue_admin_stuff' );
		}
		
		/**
		* Enqueue admin stuff if necessary
		*
		* @since 1.0.0
		*/
		public function enqueue_admin_stuff($hook) {
			if ( static::$hook_suffix_add != $hook && static::$hook_suffix_edit != $hook ) {
				return;
			}
			wp_enqueue_style(
				'panelcss',
				LeafletLayers::get_plugin_url() . 'views/css/leafletlayers.css',
				array(),
				LeafletLayers::PLUGIN_VERSION,
				'all'
				);
			
			wp_enqueue_script(
					'leafletjs',
					LeafletLayers::get_plugin_url() . 'views/js/leaflet.js',
					array(),
					'0.7.7',
					false
				);
				
			wp_enqueue_script(
					'leafletlayers',
					LeafletLayers::get_plugin_url() . 'views/admin/js/leafletlayers_adder.js',
					array('leafletjs'),
					LeafletLayers::PLUGIN_VERSION,
					true
				);
			wp_localize_script( 'leafletlayers', 'zoom_error_txt', __('Insufficent accuracy : please zoom a little more.',LeafletLayers::PLUGIN_ID) );
		}

		/** 
		 * Create menu for Plugin inside admin menu
		 *
		 * @since    1.0.0
		 */
		public function plugin_menu() {

			add_menu_page(__('Markers Map', LeafletLayers::PLUGIN_ID), self::add_moderation_bubble(__('Markers Map', LeafletLayers::PLUGIN_ID)), 'publish_posts', LeafletLayers::PLUGIN_ID.'_markers', array(&$this,'template_listing'),'dashicons-admin-site',6);
			add_submenu_page(LeafletLayers::PLUGIN_ID.'_markers', __('Markers list', LeafletLayers::PLUGIN_ID), __('Markers list', LeafletLayers::PLUGIN_ID), 'publish_posts', LeafletLayers::PLUGIN_ID.'_markers',array(&$this, 'template_listing' ));
			add_submenu_page(LeafletLayers::PLUGIN_ID.'_markers', __('Markers moderation', LeafletLayers::PLUGIN_ID), self::add_moderation_bubble(__('Markers moderation', LeafletLayers::PLUGIN_ID)), 'publish_posts', LeafletLayers::PLUGIN_ID.'_markers_moderation',array(&$this, 'template_mod_listing' ));
			add_submenu_page(LeafletLayers::PLUGIN_ID.'_markers', __('Markers Groups', LeafletLayers::PLUGIN_ID), __('Markers Groups', LeafletLayers::PLUGIN_ID), 'publish_posts', LeafletLayers::PLUGIN_ID.'_markers_groups',array(&$this, 'template_groups_listing' ));
			add_submenu_page( 'Edit group page', __('Edit group', LeafletLayers::PLUGIN_ID), __('Edit group', LeafletLayers::PLUGIN_ID), 'publish_posts', LeafletLayers::PLUGIN_ID.'_edit_group',array(&$this, 'template_edit_group' ));
			add_submenu_page(LeafletLayers::PLUGIN_ID.'_markers', __('Add group', LeafletLayers::PLUGIN_ID), __('Add a group', LeafletLayers::PLUGIN_ID), 'publish_posts', LeafletLayers::PLUGIN_ID.'_add_group',array(&$this, 'template_add_group' ));
			static::$hook_suffix_add=add_submenu_page(LeafletLayers::PLUGIN_ID.'_markers', __('Add marker', LeafletLayers::PLUGIN_ID), __('Add a marker', LeafletLayers::PLUGIN_ID), 'publish_posts', LeafletLayers::PLUGIN_ID.'_add_marker',array(&$this, 'template_add' ));
			static::$hook_suffix_edit=add_submenu_page( 'Edit marker page', __('Edit marker', LeafletLayers::PLUGIN_ID), __('Edit marker', LeafletLayers::PLUGIN_ID), 'publish_posts', LeafletLayers::PLUGIN_ID.'_edit_marker',array(&$this, 'template_edit' ));
		}
	
	/**
	* Adding the number of waiting for approval markers
	*
	* @param $text title
	* @since 1.0.1
	*/
	private function add_moderation_bubble($title)
	{
		$count= $this->model->get_moderation_count();
		if($count>0)
		$title .=sprintf(' <span class="update-plugins"><span class="update-count">%d</span></span>',$count);
		return $title;
	}
		
	/**
	* Save and delete functions
	*/
		
		/**
		* Add a marker
		*
		* @since 1.0.0
		*/
		public function add_marker()
		{
			if(wp_verify_nonce( $_POST['pepito'], 'add_marker' ))
			{
				if($this->model->check_and_add_marker(true))
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Marker added successfully', LeafletLayers::PLUGIN_ID), 'updated');
				}
				else
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Writing error, please try again', LeafletLayers::PLUGIN_ID));
				}
				
				wp_safe_redirect( wp_get_referer() );
				die();
			}
			else
			{
				die('Security check failed, please try again');	
			}
		}
		
		/**
		* Save edited marker
		*
		* @since 1.0.0
		*/
		public function save_edited_marker()
		{
			if(wp_verify_nonce( $_POST['pepito'], 'edit_marker_'.$_POST['mid'] ))
			{
				if($this->model->check_and_save_marker())
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Update successfull', LeafletLayers::PLUGIN_ID), 'updated');
				}
				else
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Updating error, please try again', LeafletLayers::PLUGIN_ID));
				}
				
				wp_safe_redirect( wp_get_referer() );
				die();
			}
			else
			{
				die('Security check failed, please try again');	
			}
		}
		
		/**
		* Delete a marker
		*
		* @since 1.0.0
		*/
		public function delete_marker()
		{
			if(wp_verify_nonce( $_GET['pepito'], 'delete_marker_'.$_GET['mid'] ))
			{
				if($this->model->delete_marker())
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Marker deleted successfully', LeafletLayers::PLUGIN_ID), 'updated');
				}
				else
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Deleting error, please try again', LeafletLayers::PLUGIN_ID));
				}
				
				wp_safe_redirect( wp_get_referer() );
				die();
			}
			else
			{
				die('Security check failed, please try again');	
			}
		}
		
		/**
		* Validate a marker
		*
		* @since 1.0.0
		*/
		public function validate_marker()
		{
			if(wp_verify_nonce( $_GET['pepito'], 'validate_marker_'.$_GET['mid'] ))
			{
				if($this->model->validate_marker())
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Marker successfully validated', LeafletLayers::PLUGIN_ID), 'updated');
				}
				else
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Validation error, please try again', LeafletLayers::PLUGIN_ID));
				}
				
				wp_safe_redirect( wp_get_referer() );
				die();
			}
			else
			{
				die('Security check failed, please try again');	
			}
		}
		
		
		/**
		* Add a group
		*
		* @since 1.0.0
		*/
		public function add_group()
		{
			if(wp_verify_nonce( $_POST['pepito'], 'add_group' ))
			{
				if($this->model->check_and_add_group())
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Group added successfully', LeafletLayers::PLUGIN_ID), 'updated');
				}
				else
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Writing error, please try again', LeafletLayers::PLUGIN_ID));
				}
				
				wp_safe_redirect( wp_get_referer() );
				die();
			}
			else
			{
				die('Security check failed, please try again');	
			}
		}
		
		/**
		* Save edited group
		*
		* @since 1.0.0
		*/
		public function save_edited_group()
		{
			if(wp_verify_nonce( $_POST['pepito'], 'edit_group_'.$_POST['id'] ))
			{
				if($this->model->check_and_save_group())
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Update successfull', LeafletLayers::PLUGIN_ID), 'updated');
				}
				else
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Updating error, please try again', LeafletLayers::PLUGIN_ID));
				}
				
				wp_safe_redirect( wp_get_referer() );
				die();
			}
			else
			{
				die('Security check failed, please try again');	
			}
		}
		
		/**
		* Delete a group
		*
		* @since 1.0.0
		*/
		public function delete_group()
		{
			if(wp_verify_nonce( $_GET['pepito'], 'delete_group_'.$_GET['id'] ))
			{
				if($this->model->delete_group())
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Group deleted successfully', LeafletLayers::PLUGIN_ID), 'updated');
				}
				else
				{
					LeafletLayers_Controller_Admin_Notices::add_admin_notice(__('Deleting error, please try again', LeafletLayers::PLUGIN_ID));
				}
				
				wp_safe_redirect( wp_get_referer() );
				die();
			}
			else
			{
				die('Security check failed, please try again');	
			}
		}
		
		
		/**
		* Load markers page template
		*
		* @since 1.0.0
		*/
		
		public function template_listing() {
		
			if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {
				
				$markers_table = new LeafletLayers_Controller_Admin_Markers_Table();
				$markers_table->prepare_items();
				echo static::render_template(
					'markers/list.php',
					array(
						'page_title' 	=> __('Markers listing', LeafletLayers::PLUGIN_ID),
						'markers_table'	=> $markers_table,
						'domain' => LeafletLayers::PLUGIN_ID
					)
				);

			} else {

				wp_die( __( 'Access denied.' ) );

			}
			
		}
		
		/**
		* Load markers moderation page template
		*
		* @since 1.0.0
		*/
		
		public function template_mod_listing() {
		
			if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {
				
				$markers_table = new LeafletLayers_Controller_Admin_Markers_Table();
				$markers_table->prepare_items(false);
				echo static::render_template(
					'markers/mod-list.php',
					array(
						'page_title' 	=> __('Markers moderation listing', LeafletLayers::PLUGIN_ID),
						'markers_table'	=> $markers_table,
						'domain' => LeafletLayers::PLUGIN_ID
					)
				);

			} else {

				wp_die( __( 'Access denied.' ) );

			}
			
		}
		
		/**
		* Load edit marker page template
		*
		* @since 1.0.0
		*/	
		public function template_edit() {
		
			if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {
				
				$marker_datas=LeafletLayers_Model_Admin::get_marker_datas($_GET['mid']);
				$groups=LeafletLayers_Model_Admin::get_groups();
				$pepito = wp_create_nonce('edit_marker_'.$_GET['mid']);
				echo static::render_template(
					'markers/edit.php',
					array(
						'page_title' 	=> __('Modify a marker', LeafletLayers::PLUGIN_ID),
						'marker_datas'	=> $marker_datas,
						'pepito'	=> $pepito,
						'markers_groups'	=> $groups,
						'domain' => LeafletLayers::PLUGIN_ID
					)
				);

			} else {

				wp_die( __( 'Access denied.' ) );

			}
			
		}
		
		
		/**
		* Load add marker page template
		*
		* @since 1.0.0
		*/
		
		public function template_add() {
		
			if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {
				
				$groups=LeafletLayers_Model_Admin::get_groups();
				$pepito = wp_create_nonce('add_marker');
				echo static::render_template(
					'markers/add.php',
					array(
						'page_title' 	=> __('Add a marker', LeafletLayers::PLUGIN_ID),
						'pepito'	=> $pepito,
						'markers_groups'	=> $groups,
						'domain' => LeafletLayers::PLUGIN_ID
					)
				);

			} else {

				wp_die( __( 'Access denied.' ) );

			}
			
		}
		
		/**
		* Load groups list template
		*
		* @since 1.0.0
		*/
		
		public function template_groups_listing() {
		
			if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {
				
				$groups_table = new LeafletLayers_Controller_Admin_Groups_Table();
				$groups_table->prepare_items();
				echo static::render_template(
					'groups/list.php',
					array(
						'page_title' 	=> __('Groups listing', LeafletLayers::PLUGIN_ID),
						'groups_table'	=> $groups_table,
						'domain' => LeafletLayers::PLUGIN_ID
					)
				);

			} else {

				wp_die( __( 'Access denied.' ) );

			}
			
		}
		
		/**
		* Load add group page template
		*
		* @since 1.0.0
		*/
		
		public function template_add_group() {
		
			if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {
				
				$pepito = wp_create_nonce('add_group');
				echo static::render_template(
					'groups/add.php',
					array(
						'page_title' 	=> __('Add a group', LeafletLayers::PLUGIN_ID),
						'pepito'	=> $pepito,
						'domain' => LeafletLayers::PLUGIN_ID
					)
				);

			} else {

				wp_die( __( 'Access denied.' ) );

			}
			
		}
		
		/**
		* Load edit group page template
		*
		* @since 1.0.0
		*/	
		public function template_edit_group() {
		
			if ( current_user_can( static::REQUIRED_CAPABILITY ) ) {
				
				$group_datas=LeafletLayers_Model_Admin::get_group_datas($_GET['id']);
				$pepito = wp_create_nonce('edit_group_'.$_GET['id']);
				echo static::render_template(
					'groups/edit.php',
					array(
						'page_title' 	=> __('Modify a group', LeafletLayers::PLUGIN_ID),
						'group_datas'	=> $group_datas,
						'pepito'	=> $pepito,
						'domain' => LeafletLayers::PLUGIN_ID
					)
				);

			} else {

				wp_die( __( 'Access denied.' ) );

			}
			
		}

	}
}