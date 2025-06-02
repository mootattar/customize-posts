<?php

/**
 * Plugin Name: customize posts label
 * Description: you can add & customize label to you posts depend on the post tag | customize text & colors
 * Version: 1.0
 * Author: Abdulrahman AL-Attar
 * Text Domain: customize_posts_label
 * License: GPL2
 */
foreach (['menu', 'filters', 'ajax', 'helpers'] as $file) {
    require_once plugin_dir_path(__FILE__) . 'includes/' . $file . '.php';
}

add_action('wp_enqueue_scripts', 'attar_custom_posts_enqueue_styles');

function attar_custom_posts_enqueue_styles()
{
    if (
        (is_home() || is_archive() || is_category() || is_tag() || is_search()) &&
        !is_singular()
    ) {
        wp_enqueue_style('attar-custom-posts-enqueue-styles', plugin_dir_url(__FILE__) . 'style.css');
    }
}
