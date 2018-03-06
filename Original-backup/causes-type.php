<?php
/* ==================================================
  Causes Post Type Functions
  ================================================== */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
add_action('init', 'causes_register');
function causes_register() {
    $args_c = array(
    "label" => __('Causes Categories', "imic-framework-admin"),
    "singular_label" => __('Cause Category', "imic-framework-admin"),
    'public' => true,
    'hierarchical' => true,
    'show_ui' => true,
    'show_in_nav_menus' => true,
    'args' => array('orderby' => 'term_order'),
    'rewrite' => false,
    'query_var' => true,
	'show_admin_column' => true,
);
register_taxonomy('causes-category', 'causes', $args_c);
    $labels = array(
        'name' => __('Causes', 'framework'),
        'singular_name' => __('Cause', 'framework'),
        'add_new' => __('Add New', 'framework'),
        'add_new_item' => __('Add New Cause', 'framework'),
        'edit_item' => __('Edit Cause', 'framework'),
        'new_item' => __('New Cause', 'framework'),
        'view_item' => __('View Cause', 'framework'),
        'search_items' => __('Search Cause', 'framework'),
        'not_found' => __('No causes have been added yet', 'framework'),
        'not_found_in_trash' => __('Nothing found in Trash', 'framework'),
        'parent_item_colon' => '',
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'hierarchical' => false,
        'rewrite' => false,
        'supports' => array('title', 'editor', 'thumbnail', 'comments'),
        'has_archive' => true,
        'taxonomies' => array('causes-category'),
		'menu_icon' => 'dashicons-universal-access',
    );
    register_post_type('causes', $args);
    register_taxonomy_for_object_type('causes-category','causes');
}
?>