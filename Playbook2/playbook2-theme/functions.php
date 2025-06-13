<?php
/**
 * Playbook2 Theme functions and definitions
 *
 * @package Playbook2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Setup theme defaults and register supported features
 */
function playbook2_setup() {
    // Make theme available for translation
    load_theme_textdomain('playbook2', get_template_directory() . '/languages');
  
    // Add default theme support
    add_theme_support('title-tag');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Add post thumbnails support
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'playbook2_setup');

/**
 * Enqueue theme scripts and styles
 */
function playbook2_scripts() {

     // Main stylesheet
    wp_enqueue_style(
        'playbook2-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
    
    // Main JavaScript file
    wp_enqueue_script(
        'playbook2-script',
        get_template_directory_uri() . '/about.js',
        array('jquery'),
        '1.0.0',
        true
    );
   
    // Localize script with translations
    wp_localize_script('playbook2-script', 'playbook2_vars', array(
        'expand'   => __('Expand', 'playbook2'),
        'collapse' => __('Collapse', 'playbook2'),
        'ajaxurl'  => admin_url('admin-ajax.php'),
    ));
       
}
add_action('wp_enqueue_scripts', 'playbook2_scripts');

/**
 * Add custom classes to menu items
 *
 * @param array    $classes Current CSS classes
 * @param WP_Post  $item    Menu item
 * @param stdClass $args    Menu arguments
 * @return array Modified CSS classes
 */

/**
 * Add SVG support
 */
function playbook2_add_svg_support($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'playbook2_add_svg_support');

/**
 * Custom excerpt length
 */
function playbook2_custom_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'playbook2_custom_excerpt_length');

/**
 * Security headers
 */
function playbook2_security_headers() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
}

add_action('send_headers', 'playbook2_security_headers');