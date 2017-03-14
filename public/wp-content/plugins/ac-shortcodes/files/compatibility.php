<?php
add_action('wp_enqueue_scripts', 'ac_sc_enq_css_fb_vb_enabled');
add_filter('single_template', 'ac_sc_cpt_template');
add_action('admin_notices', 'ac_sc_get_divi_latest');
add_action('admin_init', 'ac_sc_permalink_notice_done');
add_action('admin_bar_menu', 'customize_my_wp_admin_bar', 9999);
function ac_sc_enq_css_fb_vb_enabled()
{
    $classes = get_body_class();
    if (in_array('et-fb', $classes)) {
        wp_register_style('ac-sc-front', plugin_dir_url(__FILE__) . 'style.css', false, '1.0.0');
        wp_enqueue_style('ac-sc-front');
    }
}
function ac_sc_cpt_template($single_template)
{
    global $post;
    if ($post->post_type == 'shortcode_maker') {
        $single_template = plugin_dir_path(__FILE__) . 'custom.php';
    }
    return $single_template;
}
function ac_sc_get_divi_latest($version)
{
    global $current_user;
    $user_id   = $current_user->ID;
    $permalink = admin_url('options-permalink.php');
    $hide_url  = add_query_arg('ac_sc_permalink_notice_done', '0');
    if (!get_option('permalink_structure')) {
        printf(_('<div class="error notice"><p>Your site permalinks are set default. Please update the permalinks structure from default to anything else. <a href="%1$s">Update Permalinks</a></p></div>'), $permalink);
    }
    if (get_option('permalink_structure') && !get_user_meta($user_id, 'permalink_update_done')) {
        printf(_('<div class="notice notice-info"><p>Please re-save your permalinks for AC Shortcodes Plugin to work properly. Only hitting Update button once will do. <a href="%1$s">Update Permalinks</a> | <a style="color: #fff;background: #268be6;padding: 1px 8px;text-decoration:none;text-transform: uppercase;border-radius: 3px;" href="%2$s">Done</a></p></div>'), $permalink, $hide_url);
    }
}
function ac_sc_permalink_notice_done()
{
    global $current_user;
    $user_id = $current_user->ID;
    if (isset($_GET['ac_sc_permalink_notice_done']) && '0' == $_GET['ac_sc_permalink_notice_done']) {
        add_user_meta($user_id, 'permalink_update_done', 'true', true);
    }
}
function customize_my_wp_admin_bar($wp_admin_bar)
{
    if (is_singular('shortcode_maker')) {
        $wp_admin_bar->remove_node('et-disable-visual-builder');
    }
}