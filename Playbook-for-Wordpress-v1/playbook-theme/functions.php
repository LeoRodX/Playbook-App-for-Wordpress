<?php
/**
 * Основные функции темы Плейбук
 * 
 * @package Playbook
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Настройки темы
 */
function playbook_setup() {
    load_theme_textdomain('playbook', get_template_directory() . '/languages');
    
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('align-wide');
    
    register_nav_menus(array(
        'primary' => __('Основное меню', 'playbook'),
        'footer'  => __('Меню в подвале', 'playbook'),
    ));
    
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
}
add_action('after_setup_theme', 'playbook_setup');

/**
 * Подключение скриптов и стилей
 */
function playbook_scripts() {
    // Основной CSS
    wp_enqueue_style('playbook-style', get_stylesheet_uri());
    wp_enqueue_style('playbook-google-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
    
    // Основной JS
    wp_enqueue_script('playbook-main', get_template_directory_uri() . '/main.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'playbook_scripts');

/**
 * Стили для админ-панели
 */
function playbook_admin_styles() {
    wp_enqueue_style('playbook-admin', get_template_directory_uri() . '/admin.css');
}
add_action('admin_enqueue_scripts', 'playbook_admin_styles');

/**
 * Подключение дополнительных файлов
 */
function playbook_include_files() {
    $inc_files = array(
        'custom-post-types',
        'security',
        'search-functions'
    );

    foreach ($inc_files as $file) {
        $file_path = get_template_directory() . '/inc/' . $file . '.php';
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
}
add_action('after_setup_theme', 'playbook_include_files');

/**
 * Кастомные классы для меню
 */
function playbook_menu_classes($classes, $item, $args) {
    if ($args->theme_location == 'primary') {
        $classes[] = 'nav-item';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'playbook_menu_classes', 1, 3);

/**
 * Оптимизация WordPress
 */
function playbook_optimize() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'feed_links_extra', 3);
    
    // Отключение эмодзи
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action('init', 'playbook_optimize');

/**
 * Регистрация сайдбара
 */
function playbook_widgets_init() {
    register_sidebar(array(
        'name'          => __('Боковая панель', 'playbook'),
        'id'            => 'sidebar-1',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'playbook_widgets_init');