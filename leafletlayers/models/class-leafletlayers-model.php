<?php

/**
 * Abstract class to define/implement base methods for model classes
 *
 * @since      1.0.0
 * @package    LeafletLayers
 * @subpackage LeafletLayers/models
 *
 */

if ( ! class_exists( 'LeafletLayers_Model' ) ) {

	abstract class LeafletLayers_Model {

		private static $instances = array();
		protected static $settings;
		
		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		abstract protected function __construct();

		/**
		 * Register callbacks for actions and filters
		 *
		 * @since    1.0.0
		 */
		abstract protected function register_hook_callbacks();
		
		/**
		 * Provides access to a single instance of a module using the singleton pattern
		 *
		 * @since    1.0.0
		 * @return object
		 */
		public static function get_instance() {

			$classname = get_called_class();

			if ( ! isset( self::$instances[ $classname ] ) ) {
				self::$instances[ $classname ] = new $classname();
			}
			return self::$instances[ $classname ];

		}

		/**
		* Returns groups datas
		*
		* @param bool $json return json or array
		* @return json object or array
		* @since 1.0.0
		*/
		public function get_groups($json=true) {
			return self::get_datas('groups',$json);
		}
		
		/**
		* Returns all markers
		*
		* @return array
		* @since 1.0.0
		*/
		public function get_markers() {
			return self::get_datas('markers');
		}
		
		/**
		* Return all markers as json object
		*
		* @return json object
		* @since 1.0.0
		*/
		public function get_markers_json() {
			return self::get_datas_json();
		}
		
		/**
		* Get datas function
		*
		* @param string $type group or markers
		* @param bool $json json or array
		* @param bool $moderated show moderated or all
		* @access protected
		* @return json object or array
		* @since 1.0.0
		*/
		protected static function get_datas($type,$json=true, $moderated=true)
		{
			global $wpdb;
			$moderated=($moderated===false?0:1);		
			$table_name .= ($type=='markers') ? $wpdb->prefix . 'leafletlayers_markers' : $wpdb->prefix . 'leafletlayers_markers_groups';
			$sql = ($type=='markers') ? "SELECT id_group,lat,lng,title,desc,address,moderated FROM $table_name WHERE moderated=$moderated":"SELECT id,title FROM $table_name";
			if($json===true)
			{
				$datas = $wpdb->get_results($sql);
				return json_encode($datas);
			}
			else
			{
				$datas = $wpdb->get_results($sql, ARRAY_A);
				return $datas;
			}
		}
		
		
		/**
		* Get datas json function
		*
		* @param bool $moderated show moderated or all
		* @access protected
		* @return json object
		* @since 1.0.0
		*/
		protected static function get_datas_json($moderated=true)
		{
			global $wpdb;
			$markers=array();
			$moderated=($moderated===false?0:1);		
			$sql = 'SELECT M.id,M.lat, M.lng, M.address, M.moderated, M.title, M.desc, G.title as group_title, G.id as group_id FROM  '.$wpdb->prefix.'leafletlayers_markers M INNER JOIN  '.$wpdb->prefix.'leafletlayers_markers_groups G ON M.id_group = G.id WHERE M.moderated='.$moderated;
			$datas = $wpdb->get_results($sql);
			foreach($datas as $m)
			{
				if(!is_array($markers[$m->group_id])) $markers[$m->group_id]=array('title'=>$m->group_title,'markers'=>array());
				array_push($markers[$m->group_id]['markers'], array('lat'=>$m->lat,'lng'=>$m->lng,'title'=>$m->title,'desc'=>$m->desc,'id'=>$m->id,'addr'=>$m->address));
			}
			return json_encode($markers);	
		}

	}

}