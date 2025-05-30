<?php

/**
 * Plugin Name: customize title and content posts
 * Description: customize the title and the content based on your tags | customize texts colors
 * Version: 1.0
 * Author: Abdulrahman AL-Attar
 * Text Domain: customize_title_and_content_posts
 */
add_action('admin_menu', 'moot_attar_custom_posts_show_menu');

function moot_attar_custom_posts_show_menu()
{
    add_menu_page(
        __('custom posts', 'customize_title_and_content_posts'),
        __('custom posts', 'customize_title_and_content_posts'),
        'manage_options',
        'edit-title-content',
        'attar_custom_posts_show_content',
        'dashicons-admin-generic',
        40
    );
}

function attar_custom_posts_show_content()
{
    include plugin_dir_path(__FILE__) . 'templates/custom-page-content.php';
}

add_action('admin_enqueue_scripts', 'my_custom_admin_styles');

function my_custom_admin_styles($hook)
{
    if ($hook != 'toplevel_page_edit-title-content')
        return;

    wp_enqueue_style('my-custom-style', plugin_dir_url(__FILE__) . 'style.css');
}

function attar_get_schedule_tags_data_callback()
{
    // $data = get_option('custom_schedule_data', []);
    $data = get_option('attar_custom_posts_based_on_tags', []);

    if (!is_array($data)) {
        $data = [];
    }

    // wp_send_json_success(['data' => $data]);
    return $data;
}

add_filter('the_title', 'attar_custom_format_title');

function attar_custom_format_title($title)
{
    if (is_admin() || !is_main_query() || !in_the_loop() || is_singular())
        return $title;
    $is_rtl = 'left';
    if (is_rtl()) {
        $is_rtl = 'right';
    }
    global $post;
    $data = attar_get_schedule_tags_data_callback();
    foreach ($data as $tag => $values) {
        extract($values);
        if (attar_has_current_tag($post->ID, $tag)) {
            return '<span class="attar-post-title-style-' . $is_rtl . '" data-label="' . esc_attr($titleText) . '" style="--titleColor:' . esc_attr($titleColor) . '; --titleBg:' . esc_attr($titleBg) . ';">' . esc_html__($title, 'customize_title_and_content_posts') . '</span>';
        }
    }
    return $title;
}

function attar_has_current_tag($post_id, $current_tag)
{
    $tags = wp_get_post_tags($post_id);
    foreach ($tags as $tag) {
        if (strtolower($tag->name) === $current_tag) {
            return true;
        }
    }
    return false;
}

add_filter('the_content', 'attar_custom_format_content_based_on_tags');

function attar_custom_format_content_based_on_tags($content)
{
    if (!$content)
        return $content;
    $is_rtl = 'left';
    if (is_rtl()) {
        $is_rtl = 'right';
    }
    global $post;
    $post_id = $post->ID;
    if (is_admin() || is_singular() || !in_the_loop() || !is_main_query())
        return $content;

    $excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(strip_tags($post->post_content), 3, '...');

    $data = attar_get_schedule_tags_data_callback();
    foreach ($data as $tag => $values) {
        extract($values);
        if (attar_has_current_tag($post->ID, $tag)) {
            $custom_output = '
            <div class="attar-custom-post-box-' . $is_rtl . '" style="--descBg:' . esc_attr(attar_custom_post_lighten_hex_color($descBg, 0.7)) . '; padding: 5px; --descBorder: ' . esc_attr($descBg) . ';">
                <p>' . esc_html($excerpt) . '</p>
            </div>
        ';

            return $custom_output;
        }
    }

    return $content;
}

function attar_custom_post_lighten_hex_color($hex, $percent)
{
    if ($hex === 'transparent') {
        return 'transparent';
    }
    $hex = ltrim($hex, '#');

    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $r = round($r + (255 - $r) * $percent);
    $g = round($g + (255 - $g) * $percent);
    $b = round($b + (255 - $b) * $percent);

    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

add_action('wp_enqueue_scripts', 'attar_custom_posts_enqueue_styles');

function attar_custom_posts_enqueue_styles()
{
    wp_enqueue_style('attar-custom-posts-enqueue-styles', plugin_dir_url(__FILE__) . 'style.css');
}

add_action('wp_ajax_attar_save_schedula_data', 'attar_save_schedule_data_callback');

function attar_save_schedule_data_callback()
{
    if (
        isset($_POST['titleText']) &&
        isset($_POST['tag']) &&
        isset($_POST['titleBg']) &&
        isset($_POST['descBg']) &&
        isset($_POST['titleColor'])
    ) {
        $tag = sanitize_text_field($_POST['tag']);
        $data = [
            'titleText' => sanitize_text_field($_POST['titleText']),
            'titleBg' => $_POST['titleBg'] !== 'transparent' ? sanitize_hex_color($_POST['titleBg']) : $_POST['titleBg'],
            'descBg' => $_POST['descBg'] !== 'transparent' ? sanitize_hex_color($_POST['descBg']) : $_POST['descBg'],
            'titleColor' => sanitize_hex_color($_POST['titleColor']),
        ];
        $existing_data = get_option('attar_custom_posts_based_on_tags', []);

        if (!is_array($existing_data)) {
            $existing_data = [];
        }

        $existing_data[$tag] = $data;

        update_option('attar_custom_posts_based_on_tags', $existing_data);

        wp_send_json_success(['message' => $data]);
    } else {
        wp_send_json_error(['message' => 'no data', $data]);
    }

    wp_die();
}

add_action('wp_ajax_attar_delete_schedula_data', 'attar_delete_schedula_data');

function attar_delete_schedula_data()
{
    $tag = sanitize_text_field($_POST['tag']);
    // $data = get_option('custom_schedule_data', []);
    $data = get_option('attar_custom_posts_based_on_tags', []);

    if (isset($data[$tag])) {
        unset($data[$tag]);
        // update_option('custom_schedule_data', $data);
        update_option('attar_custom_posts_based_on_tags', $data);

        wp_send_json_success(['message' => 'تم الحذف بنجاح', 'updatedData' => $data]);
    } else {
        wp_send_json_error(['message' => 'العنصر غير موجود']);
    }
}
