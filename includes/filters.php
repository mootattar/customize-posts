<?php

add_filter('the_title', 'attar_custom_format_title');
add_filter('the_content', 'attar_custom_format_content_based_on_tags');

function attar_custom_format_title($title)
{
    if (is_admin() || !is_main_query() || !in_the_loop() || is_singular())
        return $title;

    global $post;
    $data = attar_get_schedule_tags_data_callback();

    foreach ($data as $tag => $values) {
        extract($values);
        if (attar_has_current_tag($post->ID, $tag)) {
            return '<span class="attar-post-title-style" data-label="' . esc_attr($titleText) . '" style="--titleColor:' . esc_attr($titleColor) . '; --titleBg:' . esc_attr($titleBg) . ';">' . esc_html__($title, 'customize_posts_label') . '</span>';
        }
    }

    return $title;
}

function attar_custom_format_content_based_on_tags($content)
{
    if (!$content || is_admin() || is_singular() || !in_the_loop() || !is_main_query())
        return $content;

    global $post;
    $post_id = $post->ID;
    $excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(strip_tags($post->post_content), 3, '...');
    $data = attar_get_schedule_tags_data_callback();

    foreach ($data as $tag => $values) {
        extract($values);
        if (attar_has_current_tag($post->ID, $tag)) {
            return '
                <div class="attar-custom-post-box" style="--descBg:' . esc_attr(attar_custom_post_lighten_hex_color($descBg, 0.7)) . '; padding: 5px; --descBorder: ' . esc_attr($descBg) . ';">
                    <p>' . esc_html($excerpt) . '</p>
                </div>
            ';
        }
    }

    return $content;
}
