<?php
    
	add_action( 'init', 'register_cpt_language' );
    
	function register_cpt_language() {
		$labels = array(
			'name' => _x( 'Languages', 'language' ),
			'singular_name' => _x( 'Language', 'language' ),
			'add_new' => _x( 'Add New', 'language' ),
			'add_new_item' => _x( 'Add New Language', 'language' ),
			'edit_item' => _x( 'Edit Language', 'language' ),
			'new_item' => _x( 'New Language', 'language' ),
			'view_item' => _x( 'View Language', 'language' ),
			'search_items' => _x( 'Search Languages', 'language' ),
			'not_found' => _x( 'No languages found', 'language' ),
			'not_found_in_trash' => _x( 'No languages found in Trash', 'language' ),
			'parent_item_colon' => _x( 'Parent Language:', 'language' ),
			'menu_name' => _x( 'Languages', 'language' ),
		);
		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'supports' => array( 'title', 'editor', 'thumbnail' ),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_icon' => 'lang_16.png',
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post'
		);

    register_post_type( 'language', $args );
}  
    
?>