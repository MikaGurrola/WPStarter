<?php
/*
Plugin Name: AC Shortcodes
Plugin URI: https://ayanize.com/product/ac-shortcodes/
Description: Convert Divi Modules into Shortcodes and Use them in another Modules.
Version: 1.2.3
Author: Ayanize Co.
Author URI: http://ayanize.com/
License: GPL
*/
require plugin_dir_path(__FILE__) . '/files/help.php';
require plugin_dir_path(__FILE__) . 'files/updater/ac-update.php';
$MyUpdateChecker = PucFactory::buildUpdateChecker('https://ayanize.com/api/?action=get_metadata&slug=ac-shortcodes', __FILE__, 'ac-shortcodes');
add_action('manage_shortcode_maker_posts_custom_column', 'ac_sc_show_sc_columns');
add_action('init', 'ac_sc_create_sc_maker', 0);
add_action('admin_menu', 'add_menu_ac_sc', 100);
add_filter('enter_title_here', 'ac_sc_title_ph_text');
add_action('edit_form_after_title', 'ac_sc_form_editor_title');
add_action('add_meta_boxes', 'ac_sc_add_metabox_sc');
add_filter('et_builder_post_types', 'ac_sc_post_type');
add_filter('et_fb_post_types', 'ac_sc_post_type');
add_shortcode('ac-sc', 'ac_sc_generate_sc');
function detect_plugin_deactivation()
{
    $theme = wp_get_theme();
    if (!function_exists('et_builder_should_load_framework')) {
        deactivate_plugins(plugin_basename(__FILE__));
    }
    if ('Nellie' == $theme->name) {
        deactivate_plugins(plugin_basename(__FILE__));
    }
}
add_action('admin_init', 'detect_plugin_deactivation', 10);
function ac_sc_create_sc_maker()
{
    $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    if (strpos($url, 'et_fb') !== false) {
        $bool = true;
    } else {
        $bool = false;
    }
    $labels = array(
        'name' => __('AC ShortCodes', 'AC Shortcode Maker', 'ac-sc'),
        'singular_name' => __('AC Shortcode', 'AC Shortcode', 'ac-sc'),
        'menu_name' => __('AC SC Maker', 'ac-sc'),
        'edit_item' => __('AC SC Maker', 'ac-sc'),
        'not_found_in_trash' => __('Not found in Trash', 'ac-sc'),
        'all_items' => __('All Shortcodes', 'ac-sc'),
        'add_new_item' => __('Create New Shortcode', 'ac-sc'),
        'add_new' => __('Create a Shortcode', 'ac-sc'),
        'new_item' => __('New Shortcode', 'ac-sc'),
        'edit_item' => __('Edit Shortcode', 'ac-sc'),
        'update_item' => __('Update Shortcode', 'ac-sc'),
        'view_item' => __('View Shortcode', 'ac-sc'),
        'not_found' => __('Shortcode Not found', 'text_domain'),
        'not_found_in_trash' => __('Shortcode Not found in Trash', 'text_domain'),
        'filter_items_list' => __('Filter Shortcodes list', 'ac-sc'),
        'search_items' => __('Search Shortcodes', 'ac-sc'),
        'remove_featured_image' => __('Remove featured image', 'ac-sc')
    );
    $args   = array(
        'label' => __('Shortcode Maker', 'ac-sc'),
        'description' => __('Shortcode Maker', 'ac-sc'),
        'labels' => $labels,
        'supports' => array(
            'editor',
            'title'
        ),
        'map_meta_cap' => true,
        'hierarchical' => true,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => false,
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => $bool,
        'register_meta_box_cb' => 'ac_sc_add_metabox_sc',
        'capability_type' => 'page'
    );
    register_post_type('shortcode_maker', $args);
}
function add_menu_ac_sc()
{
    if ('Divi' == get_template()) {
        $theme = 'et_divi_options';
    } elseif ('Extra' == get_template()) {
        $theme = 'et_extra_options';
    } else {
        $theme = 'themes.php';
    }
    add_submenu_page($theme, __('AC Shortcodes', 'ac-sc'), __('AC Shortcodes', 'ac-sc'), 'manage_options', 'edit.php?post_type=shortcode_maker', NULL);
}
function ac_sc_title_ph_text($input)
{
    global $post_type;
    if (is_admin() && 'shortcode_maker' == $post_type)
        return __('Enter a Shortcode Name here', 'ac-sc');
    return $input;
}
function ac_sc_form_editor_title($post)
{
    if ('shortcode_maker' === $post->post_type) {
        echo '<span class="sc-name-opt">Shortcode Name is optional and wont\' be visible on the front-end</span>';
    }
}
function ac_sc_add_metabox_sc()
{
    add_meta_box('ac_sc_metabox_cb', 'Shortcode', 'ac_sc_metabox_cb', 'shortcode_maker', 'side', 'default');
}
function ac_sc_metabox_cb()
{
    $id = get_the_ID();
    echo '<p>Place this shortcode into a module text editor</p>';
    echo '<code>[ac-sc id="' . $id . '"]</code>';
}
function ac_sc_post_type($post_types)
{
    $custom_post_types = array(
        'shortcode_maker'
    );
    $new_post_types    = array_merge($post_types, $custom_post_types);
    return $new_post_types;
}
function ac_sc_generate_sc($atts, $content = null)
{
    global $post;
    extract(shortcode_atts(array(
        'id' => null
    ), $atts));
    ob_start();
    $output = apply_filters('the_content', get_post_field('post_content', $id));
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}
function ac_sc_get_theme_ver()
{
    $theme   = wp_get_theme('Divi');
    $ver     = $theme->get('Version');
    $compare = version_compare($ver, '3.0', '>');
    return $compare;
}
add_filter('manage_edit-shortcode_maker_columns', 'ac_sc_user_columns');
function ac_sc_user_columns($columns)
{
    $theme                = wp_get_theme('Divi');
    $ver                  = $theme->get('Version');
    $columns['shortcode'] = __('Shortcode', 'shortcode');
    if (ac_sc_get_theme_ver() && get_template() == 'Divi' || get_template() == 'Extra') {
        $columns['edit_vb'] = __('Edit with Visual Builder', 'shortcode');
    }
    return $columns;
}
function ac_sc_show_sc_columns($column_name)
{
    $id   = get_the_ID();
    $link = get_permalink($id);
    $vb   = esc_attr('?et_fb=1');
    if (ac_sc_get_theme_ver()) {
        switch ($column_name) {
            case 'shortcode':
                echo '[ac-sc id="' . $id . '"]';
                break;
            case 'edit_vb':
                echo '<a href="' . $link . $vb . '">' . _('Edit ShortCode') . '</a>';
                break;
        }
    } else {
        switch ($column_name) {
            case 'shortcode':
                echo '[ac-sc id="' . $id . '"]';
                break;
        }
    }
}
function ac_sc_divi_compatibility()
{
    if (ac_sc_get_theme_ver()) {
       include('files/compatibility.php');
    }
}
if (get_template() == 'Divi' || get_template() == 'Extra') {
    add_action('after_setup_theme', 'ac_sc_divi_compatibility');
}
function ac_sc_remove_divi_layout_filter(){ 
    remove_filter( 'et_pb_show_all_layouts_built_for_post_type', 'et_pb_show_all_layouts_built_for_post_type');
}
add_action( 'admin_init', 'ac_sc_remove_divi_layout_filter', 9999 );

function et_pb_show_all_layouts_built_for_post_type_ac( $post_type ) {
	$similar_post_types = array(
		'post',
		'page',
		'project',
		'shortcode_maker',
	);
	if ( in_array( $post_type, $similar_post_types ) ) {
		return $similar_post_types;
	}
	return $post_type;
}
add_filter( 'et_pb_show_all_layouts_built_for_post_type', 'et_pb_show_all_layouts_built_for_post_type_ac' );








