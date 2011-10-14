<?php 
	
	
	add_action( 'init', 'register_cpt_skill' );
		function register_cpt_skill() {
			$labels = array(
			'name' => _x( 'Skills', 'skill' ),
			'singular_name' => _x( 'Skill', 'skill' ),
			'add_new' => _x( 'Add New', 'skill' ),
			'add_new_item' => _x( 'Add New Skill', 'skill' ),
			'edit_item' => _x( 'Edit Skill', 'skill' ),
			'new_item' => _x( 'New Skill', 'skill' ),
			'view_item' => _x( 'View Skill', 'skill' ),
			'search_items' => _x( 'Search Skills', 'skill' ),
			'not_found' => _x( 'No skills found', 'skill' ),
			'not_found_in_trash' => _x( 'No skills found in Trash', 'skill' ),
			'parent_item_colon' => _x( 'Parent Skill:', 'skill' ),
			'menu_name' => _x( 'Skills', 'skill' ),
		);
		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'supports' => array( 'title', 'editor', 'thumbnail' ),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_icon' => 'skill.png',
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post'
		);
    register_post_type( 'skill', $args );
    } 
	
    add_action( 'init', 'register_taxonomy_type' );
    function register_taxonomy_type() {
		$labels = array(
			'name' => _x( 'Type', 'type' ),
			'singular_name' => _x( 'Type', 'type' ),
			'search_items' => _x( 'Search Type', 'type' ),
			'popular_items' => _x( 'Popular Type', 'type' ),
			'all_items' => _x( 'All Type', 'type' ),
			'parent_item' => _x( 'Parent Type', 'type' ),
			'parent_item_colon' => _x( 'Parent Type:', 'type' ),
			'edit_item' => _x( 'Edit Type', 'type' ),
			'update_item' => _x( 'Update Type', 'type' ),
			'add_new_item' => _x( 'Add New Type', 'type' ),
			'new_item_name' => _x( 'New Type Name', 'type' ),
			'separate_items_with_commas' => _x( 'Separate type with commas', 'type' ),
			'add_or_remove_items' => _x( 'Add or remove type', 'type' ),
			'choose_from_most_used' => _x( 'Choose from the most used type', 'type' ),
			'menu_name' => _x( 'Type', 'type' ),
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => true,
			'rewrite' => true,
			'query_var' => true
		);
    register_taxonomy( 'type', array('skill'), $args );
    } 
	?>