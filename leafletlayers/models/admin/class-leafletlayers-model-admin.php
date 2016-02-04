<?php

/**
 * Defines/implements base methods for admin model classes
 *
 * @since      1.0.0
 * @package    LeafletLayers
 * @subpackage LeafletLayers/models/admin
 *
 */

if ( ! class_exists( 'LeafletLayers_Model_Admin' ) ) {

	class LeafletLayers_Model_Admin extends LeafletLayers_Model {
		
		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		protected function __construct() {}

		/**
		 * Register callbacks for actions and filters
		 *
		 * @since    1.0.0
		 */
		protected function register_hook_callbacks() {}
		
		public function get_markers_list($moderated=true) {
			return self::get_datas($moderated);
		}
		
		public function get_marker_datas($mid) {
				return self::get_my_datas(intval($mid));
		}

		public function delete_marker()
		{
			return self::delete_my_datas();
		}
		
		public function delete_group()
		{
			return self::delete_my_group_datas();
		}
		
		public function validate_marker()
		{
			return self::validate_my_datas();
		}
		
		public function get_groups()
		{
			return self::get_markers_groups();	
		}
		
		public function get_group_datas($id) {
				return self::get_my_group_datas(intval($id));
		}
		
		/**
		* Marker validation and save
		* @since 1.0.0
		*/
		public function check_and_save_marker()
		{
			$datas['title'] = stripslashes(esc_html($_POST['title']));
			$datas['desc'] = stripslashes(esc_html($_POST['desc']));
			$datas['lat'] = floatval($_POST['lat']);
			$datas['lng'] = floatval($_POST['lng']);
			$datas['address'] = stripslashes(esc_html($_POST['addr']));
			$datas['id_group'] = intval($_POST['group_id']);
			$datas['moderated']=true;
			foreach($datas as $v=>$d):
				if(empty($d)) return false;
			endforeach;
			 if(self::set_marker_datas($datas,intval($_POST['mid'])) === false)
			 	return false;
			else
				return true;
		}
		
		/**
		* Marker add
		* @since 1.0.0
		*/
		public function check_and_add_marker($moderated=false)
		{
			$datas['title'] = stripslashes(esc_html($_POST['title']));
			$datas['desc'] = stripslashes(esc_html($_POST['desc']));
			$datas['lat'] = floatval($_POST['lat']);
			$datas['lng'] = floatval($_POST['lng']);
			$datas['address'] = stripslashes(esc_html($_POST['addr']));
			$datas['id_group'] = intval($_POST['group_id']);
			foreach($datas as $v=>$d):
				if(empty($d)) return false;
			endforeach;
			$datas['moderated']=$moderated;
			 if(self::add_marker_datas($datas) === false)
			 	return false;
			else
				return true;
		}
		
		/**
		* Group validation and save
		* @since 1.0.0
		*/
		public function check_and_save_group()
		{
			$datas['title'] = stripslashes(esc_html($_POST['title']));
			
			if(empty($datas['title']) || empty($_POST['id'])) { return false; }
			
			 if(self::set_group_datas($datas,intval($_POST['id'])) === false)
			 	return false;
			else
				return true;
		}
		
		/**
		* Group add
		* @since 1.0.0
		*/
		public function check_and_add_group()
		{
			$datas['title'] = stripslashes(esc_html($_POST['title']));
			if(empty($datas['title'])) { return false; }
			
			 if(self::add_group_datas($datas) === false)
			 	return false;
			else
				return true;
		}
		
		
		/* Protected functions */
		
		protected static function get_datas($moderated=true)
		{
			global $wpdb;
			$moderated=($moderated===false?0:1);
			$sql = 'SELECT M.id,M.lat, M.lng, M.title, M.desc, G.title as group_title, G.id as group_id FROM  '.$wpdb->prefix.'leafletlayers_markers M INNER JOIN  '.$wpdb->prefix.'leafletlayers_markers_groups G ON M.id_group = G.id WHERE moderated='.$moderated;
			$datas = $wpdb->get_results($sql, ARRAY_A);
			return $datas;	
		}
		
		protected static function get_markers_groups()
		{
			global $wpdb;	
			$sql = 'SELECT title as group_title, id as group_id FROM '.$wpdb->prefix.'leafletlayers_markers_groups';
			$datas = $wpdb->get_results($sql, ARRAY_A);
			return $datas;	
		}
		
		protected static function get_my_datas($mid)
		{
			global $wpdb;	
			$sql = 'SELECT M.id,M.lat, M.lng, M.title, M.desc, M.address as addr, M.moderated, G.title as group_title, G.id as group_id FROM  '.$wpdb->prefix.'leafletlayers_markers M INNER JOIN  '.$wpdb->prefix.'leafletlayers_markers_groups G ON M.id_group = G.id WHERE M.id ='.$mid;
			$datas = $wpdb->get_row($sql, ARRAY_A);
			return $datas;	
		}
		
		protected static function set_marker_datas($datas, $mid)
		{
			global $wpdb;
			return $wpdb->update( 
				$wpdb->prefix.'leafletlayers_markers', 
				$datas, 
				array( 'id' => $mid ) 
			);
		}
		
		protected static function add_marker_datas($datas)
		{
			global $wpdb;
			return $wpdb->insert( 
				$wpdb->prefix.'leafletlayers_markers', 
				$datas
			);
		}
		
		protected static function delete_my_datas()
		{
			global $wpdb;
			$id=intval($_GET['mid']);
			return $wpdb->delete( 
				$wpdb->prefix.'leafletlayers_markers', 
				array( 'id' => $id)
			);
		}
		
		protected static function validate_my_datas()
		{
			global $wpdb;
			$id=intval($_GET['mid']);
			return $wpdb->update( 
				$wpdb->prefix.'leafletlayers_markers', 
				array( 'moderated' => 1),
				array('id'=>$id)
			);
		}
		
		protected static function set_group_datas($datas, $gid)
		{
			global $wpdb;
			return $wpdb->update( 
				$wpdb->prefix.'leafletlayers_markers_groups', 
				$datas, 
				array( 'id' => $gid ) 
			);
		}
		
		protected static function add_group_datas($datas)
		{
			global $wpdb;
			return $wpdb->insert( 
				$wpdb->prefix.'leafletlayers_markers_groups', 
				$datas
			);
		}
		
		protected static function delete_my_group_datas()
		{
			global $wpdb;
			$id=intval($_GET['id']);
			return $wpdb->delete( 
				$wpdb->prefix.'leafletlayers_markers_groups', 
				array( 'id' => $id)
			);
		}
		
		/* Get group datas
		*@param $id array
		* @since 1.0.0
		*/
		
		protected static function get_my_group_datas($id)
		{
			global $wpdb;	
			$sql = 'SELECT title as group_title, id as group_id FROM '.$wpdb->prefix.'leafletlayers_markers_groups WHERE id ='.$id;
			$datas = $wpdb->get_row($sql, ARRAY_A);
			return $datas;	
		}

	}

}