<?php
/*
 * Plugin Name:       Beardog SEO Enhancer
 * Plugin URI:        https://beardog.digital/
 * Description:       Designed to boost Beardog Company's website visibility and search engine rankings, ensuring overall digital marketing success.
 * Version:           1.6.5
 * Requires PHP:      7.2
 * Author:            #beaubhavik
 * Author URI:        https://beardog.digital/
 */


// Beaudog code start now

// Define plugin path constant.
define('BD_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BD_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the update checker.
require_once 'beardog-update-checker/beardog-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/beaushowcase/beardog-seo-enhancer/',
    __FILE__,
    'beardog-seo-enhancer'
);

$myUpdateChecker->setBranch('main');
$myUpdateChecker->getVcsApi()->enableReleaseAssets();

// Enqueue admin styles.
function bd_load_admin_style()
{

    global $pagenow;
    if ($pagenow == 'admin.php' && $_GET['page'] == 'beardog-seo-enhancer') {
        wp_enqueue_script('jquery');

        wp_enqueue_style('bd_style_css', plugins_url('/css/style.css', __FILE__), [], '1.0.0');

        wp_enqueue_style('bd_toastr', plugins_url('/css/toastr.min.css', __FILE__), [], '1.0.0');
        wp_enqueue_script('bd_toastr1', plugins_url('/js/toastr.min.js', __FILE__), [], '1.1.1', true);

        wp_enqueue_script('bd-ajax-script', plugin_dir_url(__FILE__) . 'js/bd_ajax.js', array('jquery'), '1.0', true);

        wp_localize_script(
            'bd-ajax-script',
            'ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php')
            )
        );

        wp_enqueue_script('bd_custom', plugins_url('/js/custom.js', __FILE__), [], '1.0.0', true);
    }
}
add_action('admin_enqueue_scripts', 'bd_load_admin_style');


// Include admin panel files.
require_once BD_PLUGIN_PATH . 'inc/admin_panel.php';

// SEO Data Addition.
add_action('wp_head', 'bd_seo_data_add', 0);
function bd_seo_data_add()
{
    if (get_query_var('paged')) {
        echo '<meta name="robots" content="nofollow, noindex" />';
    }
}

function footer_script_img()
{
    ?>
    <script>
        if (jQuery('img').length > 0) { // Check if there are any img elements
            jQuery('img').each(function () {
                let src = jQuery(this).attr('src');
                if (src) { // Check if src is defined
                    var altValue = jQuery(this).attr('alt');
                    if (altValue && altValue.trim() !== '') {
                        jQuery(this).addClass('alt_have');
                    } else {
                        var words = src.substring(src.lastIndexOf("/") + 1);
                        if (words) {
                            var finaltxt = words.substring(0, words.lastIndexOf("."))
                                .split(/[-_]/)
                                .map(function (word) {
                                    return word.charAt(0).toUpperCase() + word.slice(1);
                                })
                                .join(' ');
                            jQuery(this).attr('alt', finaltxt);
                        }
                    }
                }
            });
        }
    </script>
    <?php
}

function image_bothattr()
{
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('img').each(function () {
                var $img = $(this);
                var src = $img.attr('src');
                var attachmentId = findAttachmentId(src);
                var title = generateTitle(src);
                var alt = generateTitle(src);

                if (attachmentId && attachmentMeta.meta[attachmentId] && attachmentMeta.meta[attachmentId].hasTitleField) {
                    title = attachmentMeta.meta[attachmentId].mytitle || title;
                }

                if (attachmentId && attachmentMeta.meta[attachmentId] && attachmentMeta.meta[attachmentId].hasTitleField) {
                    alt = attachmentMeta.meta[attachmentId].myalt || alt;
                }

                // $img.attr({
                //     'title': title,
                //     'alt': alt
                // });
                $img.attr({
                    'title': title,
                    'alt': alt,
                    'data-original-title': title
                });

                $img.attr('title', '');
            });

            function findAttachmentId(src) {
                for (var id in attachmentMeta.meta) {
                    if (attachmentMeta.meta.hasOwnProperty(id) && attachmentMeta.meta[id].url === src) {
                        return id;
                    }
                }
                return null;
            }

            function generateTitle(src) {
                var words = src.substring(src.lastIndexOf("/") + 1, src.lastIndexOf("."))
                    .split(/[-_]/)
                    .map(function (word) {
                        return word.charAt(0).toUpperCase() + word.slice(1);
                    })
                    .join(' ');
                return words;
            }
        });
    </script>
    <?php
}

function enqueue_image_bothattr_script()
{
    if (get_option("image_bothattr") == 1) {
        $attachments = get_posts(
            array(
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_status' => 'inherit'
            )
        );
        $attachment_meta = array();
        foreach ($attachments as $attachment) {
            $attachment_id = $attachment->ID;
            $image_title = get_post_field('post_title', $attachment_id);
            $image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
            $attachment_url = wp_get_attachment_url($attachment_id);
            $attachment_meta[$attachment_id] = array(
                'hasTitleField' => !empty($image_title),
                'url' => $attachment_url,
                'mytitle' => $image_title,
                'myalt' => $image_alt
            );
        }
        wp_localize_script(
            'jquery',
            'attachmentMeta',
            array(
                'meta' => $attachment_meta
            )
        );
        add_action('wp_footer', 'image_bothattr');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_image_bothattr_script');


function footer_script_a_tag()
{
    ?>
    <script>
        if (jQuery('a')) {
            jQuery('a').each(function () {
                if (jQuery(this).attr('href') && jQuery(this).attr('href').trim() !== '') {
                    if (!jQuery(this).attr('title')) {
                        var src = jQuery(this).text();
                        if (src) {
                            const div = document.createElement('div');
                            div.innerHTML = src;
                            var sanitizedTitle = div.textContent.trim();
                            jQuery(this).attr('title', sanitizedTitle);
                        }
                    }
                }
            });
        }
    </script>
    <?php
}
function seo_phonescript()
{
    ?>
    <script>
        if (jQuery('a[href^="tel:"]')) {
            jQuery('a[href^="tel:"]').each(function () {
                var df = jQuery(this).attr('href').slice(4);
                var dd = jQuery.trim(df);
                jQuery(this).attr('data-other', '1');
                jQuery(this).attr('title', dd);
                jQuery(this).attr('onclick', "gtag('event', 'Clicked to Call " + dd + "', { 'event_category' : 'Phone Number (" + dd + ")' });");

            });
        }
    </script>
    <?php
}

function seo_emailscript()
{
    ?>
    <script>
        if (jQuery('a[href^="mailto:"]')) {
            jQuery('a[href^="mailto:"]').each(function () {
                var df = jQuery(this).attr('href').slice(7); // Changed to slice(7) to remove "mailto:"
                var dd = jQuery.trim(df);
                jQuery(this).attr('data-other', '1');
                jQuery(this).attr('title', dd);
                jQuery(this).attr('onclick', "gtag('event', 'Clicked to Email " + dd + "', { 'event_category' : 'Email (" + dd + ")' });");
            });
        }
    </script>
    <?php
}

function set_external_links()
{
    ?>
    <script>
        // Function to check if a link is internal or external
        function isInternal(link) {
            var currentDomain = window.location.hostname;
            var dummyLink = document.createElement('a');
            dummyLink.href = link;
            return dummyLink.hostname === currentDomain;
        }
        jQuery('a').each(function () {
            var link = jQuery(this).attr('href');
            if (!isInternal(link) && !link.startsWith('tel:') && !link.startsWith('mailto:') && !jQuery(this).closest('#sbi_load').length) {
                // jQuery(this).addClass('bd-external-link');
                var src = jQuery(this).text();
                if (src) {
                    const div = document.createElement("div");
                    div.innerHTML = src;
                    var currentDomain = window.location.hostname;
                    var linkDomain = jQuery(this).prop('hostname');
                    if (currentDomain != linkDomain) {
                        jQuery(this).attr({
                            'title': div.textContent.replace(/\s\s+/g, ' '),
                            'target': '_blank',
                            'rel': 'nofollow noopener noreferrer'
                        });
                    }
                }
            }
        });
    </script>
    <?php
}

function set_internal_links()
{
    ?>
    <script>
        // Function to check if a link is internal or external
        function isInternal(link) {
            var currentDomain = window.location.hostname;
            var dummyLink = document.createElement('a');
            dummyLink.href = link;
            return dummyLink.hostname === currentDomain;
        }
        jQuery('a').each(function () {
            var link = jQuery(this).attr('href');
            if (isInternal(link)) {
                // jQuery(this).addClass('bd-internal-link');
                var src = jQuery(this).text();
                if (src) {
                    const div = document.createElement("div");
                    div.innerHTML = src;
                    var currentDomain = window.location.hostname;
                    var linkDomain = jQuery(this).prop('hostname');
                    if (currentDomain === linkDomain) {
                        if (!jQuery(this).hasClass('beardog_exclude_link')) {
                            jQuery(this).removeAttr('target');
                        }
                        jQuery(this).removeAttr('rel');
                        jQuery(this).attr({
                            'title': div.textContent.replace(/\s\s+/g, ' '),
                        });
                    }

                }
            }
        });
    </script>
    <?php
}

if (get_option("seometa") == 1) {
    add_action('wp_head', 'bd_seo_data_add', 0);
}
if (get_option("linkalt") == 1) {
    add_action('wp_footer', 'footer_script_a_tag');
}
if (get_option("phonescript") == 1) {
    add_action('wp_footer', 'seo_phonescript', 0);
}
if (get_option("emailscript") == 1) {
    add_action('wp_footer', 'seo_emailscript', 0);
}
if (get_option("set_external_links") == 1) {
    add_action('wp_footer', 'set_external_links', 0);
}
if (get_option("set_internal_links") == 1) {
    add_action('wp_footer', 'set_internal_links', 0);
}
//  END plugin admin panel option data - Condtion for enable-disable

// Delete plugin data on uninstall.
function bd_delete_plugin_database_data()
{
    delete_option('phonescript');
    delete_option('emailscript');
    delete_option("seometa");
    delete_option("linkalt");
    delete_option("image_bothattr");
    delete_option("set_external_links");
    delete_option("set_internal_links");
}
register_uninstall_hook(__FILE__, 'bd_delete_plugin_database_data');

function bd_ajax_function()
{
    $response = [];
    $response['success'][] = 0;
    $response['data'][] = '';
    $response['msg'][] = '';
    $response['bool'] = 0;

    $phonescript = $_POST['phonescript'];
    $emailscript = $_POST['emailscript'];
    $seometa = $_POST['seometa'];
    $linkalt = $_POST['linkalt'];
    $image_bothattr = $_POST['image_bothattr'];
    $set_external_links = $_POST['set_external_links'];
    $set_internal_links = $_POST['set_internal_links'];
    $bool = $_POST['bool'];

    if (isset($phonescript) || isset($emailscript) || isset($seometa) || isset($linkalt) || isset($image_bothattr) || isset($titleset) || isset($set_external_links) || isset($set_internal_links)) {
        $update_phonescript = update_option('phonescript', $phonescript);
        $update_emailscript = update_option('emailscript', $emailscript);
        $update_seometa = update_option('seometa', $seometa);
        $update_linkalt = update_option('linkalt', $linkalt);
        $update_imglinkalt = update_option('image_bothattr', $image_bothattr);
        $update_set_external_links = update_option('set_external_links', $set_external_links);
        $update_set_internal_links = update_option('set_internal_links', $set_internal_links);

        $response['data']['phonescript'] = $update_phonescript;
        $response['data']['emailscript'] = $update_emailscript;
        $response['data']['seometa'] = $update_seometa;
        $response['data']['linkalt'] = $update_linkalt;
        $response['data']['image_bothattr'] = $image_bothattr;
        $response['data']['set_external_links'] = $update_set_external_links;
        $response['data']['set_internal_links'] = $update_set_internal_links;

        $response['msg'] = 'Applied in website...';
        $response['success'] = 1;
        if ($bool === 'true') {
            $response['bool'] = 1;
        }
    } else {
        $response['msg'] = 'Something went wrong !';
    }
    wp_send_json($response);
    wp_die();
}

add_action('wp_ajax_bd_ajax_action', 'bd_ajax_function');
add_action('wp_ajax_nopriv_bd_ajax_action', 'bd_ajax_function');


function add_custom_image_to_admin_menu()
{
    ?>
    <style>
        #adminmenu {
            margin-bottom: 50px;
        }
        .admin-custom-logo a{
            text-decoration: none;
            color:aqua;
        }

        .admin-custom-logo {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 140px;
            padding: 10px;
            background: #f0f0f1;
            text-align: center;
            z-index: 9999;
            font-weight: 700;
        }
        .admin-custom-logo a:focus{
            box-shadow: none;
        }

        .admin-custom-logo span {
            color: #2271b1;
        }
    </style>
    <div class="admin-custom-logo">
        <a href="https://beardog.digital/" target="_blank"><span>BEARDOG DIGITAL</span></a>
    </div>
    <?php
}
add_action('in_admin_header', 'add_custom_image_to_admin_menu');

function change_admin_footer_text() {
    echo '';
}
add_filter('admin_footer_text', 'change_admin_footer_text');