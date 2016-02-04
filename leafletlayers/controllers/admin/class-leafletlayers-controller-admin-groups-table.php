<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class LeafletLayers_Controller_Admin_Groups_Table extends WP_List_Table
{
	public function prepare_items()
    {
		$columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
 
        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );
 
        $perPage = 100;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
 
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
 
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
 
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }
	public function get_columns()
    {
        $columns = array(
            'group_title'       => 'Intitul&eacute;'
        );
 
        return $columns;
    }
	
	function column_group_title($item) {
		$actions = array(
				'edit'      => sprintf('<a href="?page='.LeafletLayers::PLUGIN_ID.'_edit_group&id=%s">Editer</a>',$item['group_id']),
				'delete'    => sprintf('<a href="admin-post.php?action=leafletlayers_delete_group&id=%s&pepito=%s">Supprimer</a>',$item['group_id'],wp_create_nonce('delete_group_'.$item['group_id']))
			);
	  return sprintf('%1$s %2$s', $item['group_title'], $this->row_actions($actions) );
	}
	
	public function get_sortable_columns()
    {
        return array('group_title' => array('group_title', false));
    }
	private function table_data()
    {
		return LeafletLayers_Model_Admin::get_groups();
	}
	public function column_default( $item, $column_name )
    {
		return $item[ $column_name ];
 
    }
	
	private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'group_title';
        $order = 'asc';
 
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
 
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
 
 
        $result = strcmp( $a[$orderby], $b[$orderby] );
 
        if($order === 'asc')
        {
            return $result;
        }
 
        return -$result;
    }
	
	public function get_hidden_columns()
    {
        return array();
    }
}