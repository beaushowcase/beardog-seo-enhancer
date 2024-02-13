<?php
// error_reporting(0);
function beardog_add_menu_page()
{
    add_menu_page(
        'Beardog SEO Enhancer',
        'Beardog SEO',
        'manage_options',
        'beardog-seo-enhancer',
        'beaudog_seo_enhancer_callback',
        'dashicons-editor-bold',
        null,
    );
}
add_action('admin_menu', 'beardog_add_menu_page');
function beardog_add_settings_link($links)
{
    $settings_link = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=beardog-seo-enhancer')) . '">' . __('Beardog Settings') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_beardog-seo-enhancer/beardog-seo-enhancer.php', 'beardog_add_settings_link');

function beaudog_seo_enhancer_callback()
{
?>
    <div class="seo-plugin-data-info container">
        <div class="inner-content-data">
            <h2 class="boxtitle ">SEO Enhancer</h2>
            <form id="bd_ajax_form" method="post">
                <?php wp_nonce_field('beaudog_seo_enhancer_action', 'beaudog_seo_enhancer_nonce'); ?>

                <div class="form-group">
                    <label class="switch">
                        <input type="checkbox" class="checkbox-data hoverme" id="phonescript" name="phonescript" value="1" <?php checked(get_option('phonescript'), 1); ?>>
                        <span class="slider"></span>
                    </label>
                    <span class="switch-label">Tel Analytics Tracker</span>
                </div>

                <div class="form-group">
                    <label class="switch">
                        <input type="checkbox" class="checkbox-data" id="seometa" name="seometa" value="1" <?php checked(get_option('seometa'), 1); ?>>
                        <span class="slider"></span>
                    </label>
                    <span class="switch-label">Paged SEO Deactivate</span>
                </div>

                <div class="form-group">
                    <label class="switch">
                        <input type="checkbox" class="checkbox-data" id="linkalt" name="linkalt" value="1" <?php checked(get_option('linkalt'), 1); ?>>
                        <span class="slider"></span>
                    </label>
                    <span class="switch-label">Anchor Tag Tooltip Setter</span>
                </div>

                <div class="form-group">
                    <label class="switch">
                        <input type="checkbox" class="checkbox-data" id="imglinkalt" name="imglinkalt" value="1" <?php checked(get_option('imglinkalt'), 1); ?>>
                        <span class="slider"></span>
                    </label>
                    <span class="switch-label">Image Alt Text Auto-setter</span>
                </div>

                <div class="form-group">
                    <label class="switch">
                        <input type="checkbox" class="checkbox-data" id="set_external_links" name="set_external_links" value="1" <?php checked(get_option('set_external_links'), 1); ?>>
                        <span class="slider"></span>
                    </label>
                    <span class="switch-label">Set External Links</span>
                </div>

                <div class="form-group">
                    <label class="switch">
                        <input type="checkbox" class="checkbox-data" id="set_internal_links" name="set_internal_links" value="1" <?php checked(get_option('set_internal_links'), 1); ?>>
                        <span class="slider"></span>
                    </label>
                    <span class="switch-label">Set Internal Links</span>
                </div>
            </form>
        </div>
    </div>   
<?php
}
?>