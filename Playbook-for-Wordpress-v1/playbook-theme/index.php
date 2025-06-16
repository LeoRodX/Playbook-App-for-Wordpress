<?php
/**
 * Главный шаблон темы
 *
 * @package Playbook
 */

get_header();
?>

<main class="playbook-main">    
    
    <div class="articles-container">
        <?php 
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            'post_type' => 'playbook_article',
            'posts_per_page' => 10,
            'paged' => $paged,
            'orderby' => 'title',
            'order' => 'ASC',
        );
        
        // Если выбрана категория, добавляем в запрос
        if (is_tax('playbook_category')) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'playbook_category',
                    'field' => 'slug',
                    'terms' => get_queried_object()->slug,
                )
            );
        }
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) : ?>
            <div class="articles-grid">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php get_template_part('template-parts/article-card'); ?>
                <?php endwhile; ?>
            </div>
            
            <div class="pagination">
                <?php
                echo paginate_links(array(
                    'total' => $query->max_num_pages,
                    'current' => $paged,
                    'prev_text' => __('« Назад', 'playbook'),
                    'next_text' => __('Вперед »', 'playbook'),
                ));
                ?>
            </div>
        <?php else : ?>
            <div class="no-results">
                <p><?php esc_html_e('Статьи не найдены.', 'playbook'); ?></p>
            </div>
        <?php endif; ?>
        
        <?php wp_reset_postdata(); ?>
    </div>
</main>

<?php
get_footer();