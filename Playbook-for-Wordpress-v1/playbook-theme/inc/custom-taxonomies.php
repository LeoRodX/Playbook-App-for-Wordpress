<?php
/**
 * Регистрация таксономий для Плейбука
 */

if (!defined('ABSPATH')) {
    exit;
}

function playbook_register_custom_taxonomies() {
    // Основная таксономия для категорий
    $labels = array(
        'name'              => __('Категории Плейбука', 'playbook'),
        'singular_name'     => __('Категория Плейбука', 'playbook'),
        'menu_name'         => __('Категории', 'playbook'),
        'search_items'      => __('Искать категории', 'playbook'),
        'all_items'         => __('Все категории', 'playbook'),
        'parent_item'       => __('Родительская категория', 'playbook'),
        'parent_item_colon' => __('Родительская категория:', 'playbook'),
        'edit_item'         => __('Редактировать категорию', 'playbook'),
        'update_item'       => __('Обновить категорию', 'playbook'),
        'add_new_item'      => __('Добавить новую категорию', 'playbook'),
        'new_item_name'     => __('Название новой категории', 'playbook'),
    );

    $args = array(
        'hierarchical'      => true, // Иерархические как рубрики
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'playbook-category'),
        'show_in_rest'      => true, // Для поддержки Gutenberg
    );

    register_taxonomy('playbook_category', array('playbook_article'), $args);
}
add_action('init', 'playbook_register_custom_taxonomies', 0);


// Добавляем метабокс для выбора категории в стиле Google Sheets
function playbook_category_metabox_styles() {
    echo '<style>
        /* Стили для метабокса категорий */
        #playbook_categorydiv .hndle {
            background: #f8f9fa;
            border-bottom: 1px solid #dadce0;
            padding: 10px 12px;
        }
        
        #playbook_categorydiv .inside {
            padding: 10px;
        }
        
        #playbook_category-checklist input[type="checkbox"] {
            margin-right: 8px;
        }
        
        #playbook_category-checklist li {
            margin: 5px 0;
        }
        
        .category-add {
            padding: 10px;
            border-top: 1px solid #dadce0;
            margin-top: 10px;
        }
    </style>';
}
add_action('admin_head', 'playbook_category_metabox_styles');