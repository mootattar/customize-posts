<?php

function attar_get_schedule_tags_data_callback()
{
    $data = get_option('attar_custom_posts_based_on_tags', []);
    return is_array($data) ? $data : [];
}

function attar_has_current_tag($post_id, $current_tag)
{
    $tags = wp_get_post_tags($post_id);
    foreach ($tags as $tag) {
        if (strtolower($tag->name) === $current_tag)
            return true;
    }
    return false;
}

function attar_custom_post_lighten_hex_color($hex, $percent)
{
    if ($hex === 'transparent')
        return 'transparent';
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3)
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $r = round($r + (255 - $r) * $percent);
    $g = round($g + (255 - $g) * $percent);
    $b = round($b + (255 - $b) * $percent);
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}
