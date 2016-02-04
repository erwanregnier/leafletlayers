<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function uninstall_leafletlayers_plugin() {
	require_once ( plugin_dir_path( __FILE__ ) . 'includes/class-leafletlayers-loader.php' );
	LeafletLayers_Loader::uninstall_plugin();
}

uninstall_leafletlayers_plugin();