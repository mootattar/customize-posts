<?php

add_action('admin_menu', 'moot_attar_custom_posts_show_menu');

function moot_attar_custom_posts_show_menu()
{
    add_menu_page(
        __('label styler', 'post_label_styler'),
        __('label styler', 'post_label_styler'),
        'manage_options',
        'label-styler',
        'attar_admin_page_custom_label',
        'dashicons-admin-generic',
        4
    );
}

function attar_admin_page_custom_label()
{
    if (isset($_GET['page']) && $_GET['page'] === 'label-styler') {
        wp_enqueue_style('attar-admin-style', plugin_dir_url(__FILE__) . '../templates/admin-page-style.css', [],
            filemtime(plugin_dir_path(__FILE__) . '../templates/admin-page-style.css'));
        wp_enqueue_script('attar-admin-script', plugin_dir_url(__FILE__) . '../templates/admin-page-script.js', [],
            filemtime(plugin_dir_path(__FILE__) . '../templates/admin-page-script.js'), true);

        wp_localize_script('attar-admin-script', 'attardata', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'scheduleDataFromPHP' => get_option('attar_custom_posts_based_on_tags', []),
            'preview' => __('preview', 'post_label_styler'),
            'delete' => __('delete', 'post_label_styler'),
            'alert' => __('please fill all Fields!', 'post_label_styler'),
            'nonce' => wp_create_nonce('attar_nonce_action'),
        ]);

        include plugin_dir_path(__FILE__) . '../templates/custom-page-content.php';
    }
}
