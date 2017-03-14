<?php
add_filter('contextual_help', 'ac_sc_help_menu', 5, 3);
add_action('admin_enqueue_scripts', 'ac_sc_css_enq');
add_action('admin_init', 'ac_sc_dismiss_panel_func');
add_action('admin_head', 'ac_sc_rename_published_on_js');
add_action('current_screen', 'ac_sc_get_current_screen_cpt', 9);
add_filter('bulk_post_updated_messages', 'ac_sc_message_bulk', 10, 2);
add_filter('post_updated_messages', 'ac_sc_generate_update_msg');
function ac_sc_help_menu($dft_help, $screen_id, $screen)
{
    if ('edit-shortcode_maker' != $screen_id && 'shortcode_maker' != $screen_id)
        return;
    $screen->remove_help_tabs();
    $screen->add_help_tab(array(
        'id' => 'ac_sc_help_wc',
        'title' => 'Overview',
        'content' => '',
        'callback' => 'ac_sc_welcome_cb'
    ));
    $screen->add_help_tab(array(
        'id' => 'ac_sc_help_pro',
        'title' => 'Documentation',
        'content' => '',
        'callback' => 'ac_sc_doc_cb'
    ));
    $screen->add_help_tab(array(
        'id' => 'ac_sc_help_com',
        'title' => 'Compatibility',
        'content' => '',
        'callback' => 'ac_sc_com_cb'
    ));
    $screen->add_help_tab(array(
        'id' => 'ac_sc_help_tem',
        'title' => 'Template Usage',
        'content' => '',
        'callback' => 'ac_sc_tem_cb'
    ));
    get_current_screen()->set_help_sidebar('<p><strong>' . __('Useful Links') . '</strong></p>' . '<p>' . __('<a href="https://www.youtube.com/watch?v=uI1yPWV82pE" title="AC Shortcode Video" target="_blank">Video Demo</a>') . '</p>' . '<p>' . __('<a href="https://ayanize.com/plugins/convert-any-divi-module-into-a-shortcode-using-ac-shortcodes-plugin/" target="_blank" title="AC Shortcode Release Blog Post">Release Blog Post</a>') . '</p>');
    return $dft_help;
}
function ac_sc_welcome_cb()
{
    echo '
        <p>Welcome to AC Shortcode Maker. It\'s a very simple Plugin which will help you convert any Divi Page Builder Module into a shortcode which you can place into another module. The Shortcodes will render the output in those modules. You can edit the shortcode any time and those will be reflected instantly in the other modules.</p> 

        <p>Please have a look at the video as how it works and why it\' so simple</p>
    ';
}
function ac_sc_doc_cb()
{
    echo '<ul><li>
       <p>Please create a new shortcode. Use any Divi Page Builder Rows or Sections. Add modules to them. Once you\'r done with the editing, publish the shortcode. You will see a shortcode automatically generated in right side meta-box like this <code>[ac-sc id="1234"]</code>. Copy the shortcode and place this into an post editor inside any module. You can also use Divi Visual Builder to Create or modify your shortcode.</p></li>' . '<li><p>To edit the output of any shortcode which is displayed from another module, you need to come to the shortcode you have made and make changes. The changes will take effect immediately in the other modules where the shortcode has been added.</p></li>
	   <li><p>You can also use Edit Shortcode Link to edit your shortcode with visual builder.</p></li></ul>';
}
function ac_sc_com_cb()
{
    echo '<ul><li><p>The Plugin needs Divi Page Builder to work. In other words, it works with Divi or Extra theme and with any theme which has Divi Page Builder Plugin activated</p></li>';
    echo '<li><p>The Plugin will be deactivated automatically if it does not find Divi Page Builder in the site.</p></li>';
	echo '<li><p>Please re-save your permalinks once. The Plugin may not work with default permalinks structure.</p></li></ul>';
}
function ac_sc_tem_cb()
{
    echo '<p>Place the following PHP tag into a template file like <code>header.php</code> or <code>footer.php</code></p>';
?><code>&lt;?php echo do_shortcode('[ac-sc id="1234"]');?> /*..replace 1234 with the shortcode id*../</code>
   <?php
}
function ac_sc_show_panel()
{
    global $current_user;
    $user_id  = $current_user->ID;
    $hide_url = add_query_arg('ac_sc_dismiss', '0');
    $screen   = get_current_screen();
    $img      = plugin_dir_url(__FILE__) . 'logo.png';
    $link     = admin_url('post-new.php?post_type=shortcode_maker');
    if (!get_user_meta($user_id, 'ac_sc_dismiss_arg') && $screen->post_type == 'shortcode_maker') {
        echo '<div class="ac-sc-welcome">';
        printf(__('<a title="This panel will be gone for good" class="remove-panel-sc" href="%1$s">Remove this Panel</a>'), $hide_url);
        echo '<h2><img src="' . $img . '"></h2>';
        printf(__('<p>Welcome to AC Shortcodes. You can convert any Divi Page Builder module into a shortcode and use this into another module editor to render the output. <strong><a href="%1$s">Create a Shortcode Now</a></strong>.</p>'), $link);
        echo '<p>Click the <span class="help">Help</span> menu at the top right corner for documentation</p>';
        echo '</div>';
    }
}
function ac_sc_dismiss_panel_func()
{
    global $current_user;
    $user_id = $current_user->ID;
    if (isset($_GET['ac_sc_dismiss']) && '0' == $_GET['ac_sc_dismiss']) {
        add_user_meta($user_id, 'ac_sc_dismiss_arg', 'true', true);
    }
}
$hasposts = get_posts('post_type=shortcode_maker');
if (empty($hasposts)) {
    add_action('admin_notices', 'ac_sc_show_panel');
}
function ac_sc_css_enq()
{
    global $post_type;
    if ('shortcode_maker' == $post_type) {
        wp_register_style('ac-sc', plugin_dir_url(__FILE__) . 'panel.css', false, '1.0.0');
        wp_enqueue_style('ac-sc');
    }
}
function ac_sc_generate_update_msg($messages)
{
    $post                        = get_post();
    $post_type                   = get_post_type($post);
    $post_type_object            = get_post_type_object($post_type);
    $messages['shortcode_maker'] = array(
        0 => '',
        1 => __('Shortcode updated.', 'ac-sc'),
        2 => '',
        3 => __('Shortcode moved to trash.', 'ac-sc'),
        4 => __('Shortcode updated.', 'ac-sc'),
        5 => '',
        6 => __('Shortcode Created.', 'ac-sc')
    );
    return $messages;
}
function ac_sc_get_current_screen_cpt($current_screen)
{
    if ('shortcode_maker' == $current_screen->post_type && 'post' == $current_screen->base) {
        add_filter("gettext", "ac_sc_create_button", 10, 2);
    }
}
function ac_sc_create_button($translation, $text)
{
    switch ($text) {
        case "Publish":
            return "Create";
        case "Publish <b>immediately</b>":
            return "Create <b>immediately</b>";
        case "Publish on: ":
            return "Create on: ";
        case "Privately Published":
            return "Privately Created";
        case "Published":
            return "Created";
        case "Save & Publish":
            return "Save & Create";
        default:
            return $translation;
    }
}
function ac_sc_rename_published_on_js()
{
    $screen = get_current_screen();
    if ($screen->post_type == 'shortcode_maker') {
        echo '<script type="text/javascript">
(function($) {
$(document).ready(function() {
$(".curtime #timestamp").text(function () {
    return $(this).text().replace("Published", "Created"); 
});
});
})(jQuery);
</script>';
    }
}
function ac_sc_message_bulk($bulk_messages, $bulk_counts)
{
    $bulk_messages['shortcode_maker'] = array(
        'updated' => _n('%s Shortcode updated.', '%s Shortcodes updated.', $bulk_counts['updated']),
        'locked' => _n('%s Shortcode not updated, somebody is editing it.', '%s Shortcodes not updated, somebody is editing them.', $bulk_counts['locked']),
        'deleted' => _n('%s Shortcode permanently deleted.', '%s Shortcodes permanently deleted.', $bulk_counts['deleted']),
        'trashed' => _n('%s Shortcode moved to the Trash.', '%s Shortcodes moved to the Trash.', $bulk_counts['trashed']),
        'untrashed' => _n('%s Shortcode restored from the Trash.', '%s Shortcodes restored from the Trash.', $bulk_counts['untrashed'])
    );
    return $bulk_messages;
}

