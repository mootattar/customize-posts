<?php

add_action('wp_ajax_attar_save_schedula_data', 'attar_save_schedule_data_callback');
add_action('wp_ajax_attar_delete_schedula_data', 'attar_delete_schedula_data');

function attar_save_schedule_data_callback()
{
    if (
        isset($_POST['titleText'], $_POST['tag'], $_POST['titleBg'], $_POST['descBg'], $_POST['titleColor'])
    ) {
        $tag = sanitize_text_field($_POST['tag']);
        $data = [
            'titleText' => sanitize_text_field($_POST['titleText']),
            'titleBg' => $_POST['titleBg'] !== 'transparent' ? sanitize_hex_color($_POST['titleBg']) : $_POST['titleBg'],
            'descBg' => $_POST['descBg'] !== 'transparent' ? sanitize_hex_color($_POST['descBg']) : $_POST['descBg'],
            'titleColor' => sanitize_hex_color($_POST['titleColor']),
        ];
        $existing_data = get_option('attar_custom_posts_based_on_tags', []);
        if (!is_array($existing_data))
            $existing_data = [];
        $existing_data[$tag] = $data;
        update_option('attar_custom_posts_based_on_tags', $existing_data);
        wp_send_json_success(['message' => $data]);
    } else {
        wp_send_json_error(['message' => 'no data']);
    }
    wp_die();
}

function attar_delete_schedula_data()
{
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'attar_nonce_action')) {
        wp_send_json_error(['message' => 'Nonce verification Failed']);
        wp_die();
    }

    $tag = sanitize_text_field($_POST['tag']);
    $data = get_option('attar_custom_posts_based_on_tags', []);

    if (isset($data[$tag])) {
        unset($data[$tag]);
        update_option('attar_custom_posts_based_on_tags', $data);
        wp_send_json_success(['message' => 'deleted successfully', 'updatedData' => $data]);
    } else {
        wp_send_json_error(['message' => 'item is not exist']);
    }
    wp_die();
}
