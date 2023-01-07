<?php

if (!defined('ABSPATH')) {
    exit;
}

function register_arlo_pages() {
    $args = array(
        'labels'      => array(
            'name'          => __('Arlo Pages', 'augsburg-cace-arlo'),
            'singular_name' => __('Arlo Page', 'augsburg-cace-arlo'),
        ),
        'public'      => true,
        'has_archive' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-format-aside',
        'hierarchical' => true,
        'supports' => array(
            'title',
            'editor',
            'page-attributes'
        )
    );
    register_post_type('arlo-page', $args);
}
add_action('init', 'register_arlo_pages');