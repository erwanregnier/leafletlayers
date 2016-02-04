<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class LeafletLayers_Controller_Admin_Markers_Table extends WP_List_Table
{
	protected $moderated;
	public function prepare_items($moderated=true)
    {
        $this->moderated=$moderated;
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
            'title'       => 'Intitul&eacute;',
			'desc'       => 'Descriptif',
            'cat'    => 'Cat&eacute;gorie'
        );
 
        return $columns;
    }
	
	function column_title($item) {
		if($this->moderated)
		{
			$actions = array(
				'edit'      => sprintf('<a href="?page='.LeafletLayers::PLUGIN_ID.'_edit_marker&mid=%s">Editer</a>',$item['id']),
				'delete'    => sprintf('<a href="admin-post.php?action=leafletlayers_delete_marker&mid=%s&pepito=%s">Supprimer</a>',$item['id'],wp_create_nonce('delete_marker_'.$item['id']))
			);
		}
		else
		{
			$actions = array(
				'validate'      =>sprintf('<a href="admin-post.php?action=leafletlayers_validate_marker&mid=%s&pepito=%s">Valider</a>',$item['id'],wp_create_nonce('validate_marker_'.$item['id'])),
				'delete'    => sprintf('<a href="admin-post.php?action=leafletlayers_delete_marker&mid=%s&pepito=%s">Supprimer</a>',$item['id'],wp_create_nonce('delete_marker_'.$item['id']))
			);
		}

	  return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions) );
	}
	
	public function get_sortable_columns()
    {
        return array('title' => array('title', false),'cat' => array('cat', false));
    }
	private function table_data()
    {
		return LeafletLayers_Model_Admin::get_markers_list($this->moderated);
	}
	public function column_default( $item, $column_name )
    {
		switch( $column_name ) {
            case 'cat':
				return $item['group_title'];
			break;
            default:
                return $item[ $column_name ];
        }
    }
	
	private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'nom';
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