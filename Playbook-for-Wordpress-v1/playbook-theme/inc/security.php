<?php
/**
 * Безопасность темы Playbook
 */

defined('ABSPATH') || exit;

// 1. Отключение ненужного
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');

// 2. Защита от перечисления пользователей
if (!is_admin() && isset($_SERVER['REQUEST_URI'])) {
    if (preg_match('/(wp-comments-post|author=)/', $_SERVER['REQUEST_URI'])) {
        status_header(404);
        exit;
    }
}

// 3. Безопасные заголовки
add_action('send_headers', function() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
});

// 4. Блокировка опасных запросов
add_action('init', function() {
    if (is_admin()) return;
    
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $patterns = [
        '/\.\.\//', 
        '/\<script/', 
        '/base64_/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $uri)) {
            status_header(403);
            exit('Forbidden');
        }
    }
}, 1);