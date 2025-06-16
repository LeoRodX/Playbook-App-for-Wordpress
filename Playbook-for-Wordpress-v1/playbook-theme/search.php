<?php
/**
 * Шаблон поиска
 *
 * @package Playbook
 */

get_header();
?>

<main class="playbook-main">
    <header class="search-header">
        <h2 class="search-title">
            <?php 
            printf(
                esc_html__('Результаты поиска: %s', 'playbook'),
                '<span>' . get_search_query() . '</span>'
            );
            ?>
        </h2>
    </header>
    <div class="search-results-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/article-card'); ?>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="no-results">
                <p><?php esc_html_e('Ничего не найдено. Попробуйте изменить поисковый запрос.', 'playbook'); ?></p>
                <?php get_search_form(); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php the_posts_pagination(array(
        'prev_text' => '<span class="screen-reader-text">' . __('Предыдущая', 'playbook') . '</span>',
        'next_text' => '<span class="screen-reader-text">' . __('Следующая', 'playbook') . '</span>',
        'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Страница', 'playbook') . '</span>',
    )); ?>
</main>

<?php
get_footer();