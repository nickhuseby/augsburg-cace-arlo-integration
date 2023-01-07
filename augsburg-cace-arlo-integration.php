<?php
/**
 * Plugin Name: Augsburg University CACE Arlo Integration
 * Description: Integrations and Web Controls Shortcodes for integrating
 * Continuing Education Data for CACE
 * Author: Nick Huseby
 * Version: 1.0.1
 */

 if (!defined('ABSPATH')) {
    exit;
 }

 if( !function_exists('get_plugin_data') ){
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
$plugin_data = get_plugin_data( __FILE__ );

define('CACEARLO_VERSION', $plugin_data['Version']);

// Arlo Upcoming Events Web Control Shortcode
include plugin_dir_path(__FILE__) . 'web-controls/arlo-catalog/arlo-catalog.php'; 

// Arlo Event Template Web Control Shortcode
include plugin_dir_path(__FILE__) . 'web-controls/arlo-event/arlo-event.php';

// Arlo Event Categories Web Control Shortcode
include plugin_dir_path(__FILE__) . 'web-controls/arlo-categories/arlo-categories.php';

// CPT for Arlo Pages (keeps the pages from showing in the site nav, and doesn't clutter or complicate blog posts)
include plugin_dir_path(__FILE__) . 'cpt/arlo-pages.php';

// Register and enqueue shortcode styles

function cace_arlo_register_styles() {
    
    wp_register_style(
        'arlo-catalog-css', 
        plugin_dir_url(__FILE__) . 'web-controls/arlo-catalog/css/arlo-catalog.css',
        array(),
        CACEARLO_VERSION
    );

    wp_register_style(
        'arlo-event-css', 
        plugin_dir_url(__FILE__) . 'web-controls/arlo-event/css/arlo-event.css',
        array(),
        CACEARLO_VERSION
    );

    wp_register_style(
        'arlo-categories-css',
        plugin_dir_url(__FILE__) . 'web-controls/arlo-categories/css/arlo-categories.css',
        array(),
        CACEARLO_VERSION
    );
}
add_action('wp_loaded', 'cace_arlo_register_styles');

function cace_arlo_include_styles_sc() {
    global $post;

    // Arlo Catalog Styles
    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'cace_arlo_catalog') ) {
        wp_enqueue_style( 'arlo-catalog-css' );
    }

    //Arlo Event Styles
    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'cace_arlo_event') ) {
        wp_enqueue_style( 'arlo-event-css' );
    }

    //Arlo Categories Styles
    if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'cace_arlo_categories') ) {
        wp_enqueue_style( 'arlo-categories-css' );
    }
}
add_action('wp_enqueue_scripts', 'cace_arlo_include_styles_sc');

// Add query vars for filtering in web controls
function cace_arlo_add_query_vars( $qvars ) {
    $qvars[] = 'category-id';
    $qvars[] = 'category-name';
    $qvars[] = 'event';
    $qvars[] = 'eventtemplate';
    return $qvars;
}
add_filter( 'query_vars', 'cace_arlo_add_query_vars' );