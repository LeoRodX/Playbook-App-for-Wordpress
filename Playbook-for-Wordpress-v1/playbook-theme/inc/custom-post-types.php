<?php
/**
 * Регистрация типа записи "Статья Плейбука"
 */

if (!defined('ABSPATH')) {
    exit;
}

function playbook_register_post_types() {
    $labels = array(
        'name'               => __('Статьи Плейбука', 'playbook'),
        'singular_name'      => __('Статья Плейбука', 'playbook'),
        'menu_name'          => __('Плейбук', 'playbook'),
        'add_new'            => __('Добавить статью', 'playbook'),
        'add_new_item'       => __('Добавить новую статью', 'playbook'),
        'edit_item'          => __('Редактировать статью', 'playbook'),
        'new_item'           => __('Новая статья', 'playbook'),
        'view_item'          => __('Просмотреть статью', 'playbook'),
        'search_items'       => __('Искать статьи', 'playbook'),
        'not_found'          => __('Статьи не найдены', 'playbook'),
        'not_found_in_trash' => __('Статьи не найдены в корзине', 'playbook'),
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-book-alt',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'            => array('slug' => 'playbook'),
        'show_in_rest'       => true,
        'taxonomies'         => array('playbook_category'), // Важно: привязываем таксономию
    );
    
    register_post_type('playbook_article', $args);
}
add_action('init', 'playbook_register_post_types');

// Регистрация таксономии "Категории Плейбука"
function playbook_register_taxonomies() {
    $labels = array(
        'name' => __('Категории Плейбука', 'playbook'),
        'singular_name' => __('Категория Плейбука', 'playbook'),
        'search_items' => __('Искать категории', 'playbook'),
        'all_items' => __('Все категории', 'playbook'),
        'parent_item' => __('Родительская категория', 'playbook'),
        'parent_item_colon' => __('Родительская категория:', 'playbook'),
        'edit_item' => __('Редактировать категорию', 'playbook'),
        'update_item' => __('Обновить категорию', 'playbook'),
        'add_new_item' => __('Добавить новую категорию', 'playbook'),
        'new_item_name' => __('Новое название категории', 'playbook'),
        'menu_name' => __('Категории', 'playbook'),
    );
    
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'playbook-category'),
        'show_in_rest' => true,
    );
    
    register_taxonomy('playbook_category', 'playbook_article', $args);
}
add_action('init', 'playbook_register_taxonomies');