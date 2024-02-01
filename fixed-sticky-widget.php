<?php

/*
 * Plugin Name:       Fixed & Sticky Widget
 * Plugin URI:        https://www.seferdemirci.com/fixed-sticky-widget
 * Description:       Enhance your WordPress site with sticky widget functionality. This plugin allows widgets to remain visible and "sticky" on the page as users scroll, improving accessibility and engagement.
 * Version:           1.0.0
 * Requires at least: 4.6
 * Requires PHP:      7.0
 * Author:            Sefer Demirci
 * Author URI:        https://www.seferdemirci.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       fixed-sticky-widget
 * Domain Path:       /languages
 */

add_action('admin_menu', 'fs_widget_add_admin_menu');
function fs_widget_add_admin_menu()
{
    add_menu_page('Fixed & Sticky Widget', 'Sticky Widget', 'manage_options', 'fixed-sticky-widget', 'fs_widget_settings_page', 'dashicons-align-left');
}

function fs_widget_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $updated = false;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fs_widget_nonce_field']) && wp_verify_nonce($_POST['fs_widget_nonce_field'], 'fs_widget_save_settings')) {

        $settings = array(
            'active' => isset($_POST['fs_widget_active']) ? 1 : 0,
            'widget_selector' => sanitize_text_field($_POST['fs_widget_selector']),
            'footer_selector' => sanitize_text_field($_POST['fs_footer_selector']),
            'top_padding' => intval($_POST['fs_top_padding']),
            'gap' => intval($_POST['fs_gap']),
            'enable_mobile' => isset($_POST['fs_enable_mobile']) ? 1 : 0,
        );
        update_option('fs_widget_options', $settings);
        $updated = true;
    }

    if ($updated) {
        echo '<div class="updated notice is-dismissible"><p>' . esc_html__('Settings saved.', 'fixed-sticky-widget') . '</p></div>';
    }

    $default_options = array(
        'active' => 0,
        'widget_selector' => '',
        'footer_selector' => '',
        'top_padding' => 0,
        'gap' => 0,
        'enable_mobile' => 0,
    );
    $options = get_option('fs_widget_options', $default_options);

?>
    <div class="wrap">
        <h2>Fixed & Sticky Widget</h2>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <form method="post" action="">
                        <?php wp_nonce_field('fs_widget_save_settings', 'fs_widget_nonce_field'); ?>
                        <div class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Settings', 'fixed-sticky-widget'); ?></h2>
                            </div>
                            <div class="inside">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Plugin Active', 'fixed-sticky-widget'); ?></th>
                                        <td><input type="checkbox" name="fs_widget_active" <?php checked(1, $options['active'], true); ?> /></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Widget Selector', 'fixed-sticky-widget'); ?></th>
                                        <td><input type="text" name="fs_widget_selector" value="<?php echo esc_attr($options['widget_selector']); ?>" class="regular-text" />
                                            <p class="description"><?php esc_html_e('Enter the HTML element selector for the widget to be fixed (e.g., ".my-widget". Only one element is accepted).', 'fixed-sticky-widget'); ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Stopper Selector', 'fixed-sticky-widget'); ?></th>
                                        <td><input type="text" name="fs_footer_selector" value="<?php echo esc_attr($options['footer_selector']); ?>" class="regular-text" />
                                            <p class="description"><?php esc_html_e('Enter the HTML element selector for the page footer or stopper. The stop elements will push sticky elements up as soon as they reach them while scrolling. (e.g., "#footer". Only one element is accepted).', 'fixed-sticky-widget'); ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Top Padding', 'fixed-sticky-widget'); ?></th>
                                        <td><input type="number" name="fs_top_padding" value="<?php echo esc_attr($options['top_padding']); ?>" class="small-text" />
                                            <p class="description"><?php esc_html_e('Set the top padding in pixels when the widget is fixed. The distance fixed elements will keep from the top of the window.', 'fixed-sticky-widget'); ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Gap', 'fixed-sticky-widget'); ?></th>
                                        <td><input type="number" name="fs_gap" value="<?php echo esc_attr($options['gap']); ?>" class="small-text" />
                                            <p class="description"><?php esc_html_e('Set the gap in pixels between the widget and the footer or stopper.', 'fixed-sticky-widget'); ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php esc_html_e('Enable on Mobile', 'fixed-sticky-widget'); ?></th>
                                        <td><input type="checkbox" name="fs_enable_mobile" <?php checked(1, $options['enable_mobile'], true); ?> />
                                            <p class="description"><?php esc_html_e('Enable the sticky widget functionality on mobile devices.', 'fixed-sticky-widget'); ?></p>
                                        </td>
                                    </tr>
                                </table>
                                <p class="submit">
                                    <input type="submit" class="button-primary" value="<?php esc_html_e('Save Changes', 'fixed-sticky-widget'); ?>" />
                                </p>
                                <p>
                                    <?php esc_html_e('The users who are using cache plugins, please remember to clear the cache after making changes on this setting screen.', 'fixed-sticky-widget'); ?>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
}

add_action('wp_enqueue_scripts', 'fs_widget_enqueue_scripts');
function fs_widget_enqueue_scripts()
{
    $options = get_option('fs_widget_options', array('active' => 0));
    if ($options['active']) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('sticky-widget', plugin_dir_url(__FILE__) . 'js/sticky-widget.js', array('jquery'), '1.0.0', true);

        wp_localize_script('sticky-widget', 'fsWidgetParams', array(
            'widgetSelector' => $options['widget_selector'],
            'footerSelector' => $options['footer_selector'],
            'topPadding'     => $options['top_padding'],
            'gap'            => $options['gap'],
            'enableMobile'   => $options['enable_mobile']
        ));
    }
}

function fs_widget_load_textdomain()
{
    load_plugin_textdomain('fixed-sticky-widget', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('init', 'fs_widget_load_textdomain');


function fs_widget_add_settings_link($links)
{
    $settings_link = '<a href="' . admin_url('admin.php?page=fixed-sticky-widget') . '">' . __('Settings', 'fixed-sticky-widget') . '</a>';
    array_push($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'fs_widget_add_settings_link');


?>