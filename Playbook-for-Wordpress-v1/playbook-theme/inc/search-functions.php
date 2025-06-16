<?php
// Расширенный поиск по статьям
function playbook_enhanced_search($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        $query->set('post_type', array('playbook_article'));
    }
    return $query;
}
add_filter('pre_get_posts', 'playbook_enhanced_search');

// AJAX поиск
function playbook_ajax_search() {
    check_ajax_referer('playbook_search_nonce', 'nonce');
    
    $search_term = sanitize_text_field($_POST['search']);
    $args = array(
        'post_type' => 'playbook_article',
        'posts_per_page' => 10,
        's' => $search_term,
        'post_status' => 'publish',
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/article-card');
        }
    } else {
        echo '<div class="no-results">'.__('Ничего не найдено', 'playbook').'</div>';
    }
    
    wp_die();
}
add_action('wp_ajax_playbook_search', 'playbook_ajax_search');
add_action('wp_ajax_nopriv_playbook_search', 'playbook_ajax_search');