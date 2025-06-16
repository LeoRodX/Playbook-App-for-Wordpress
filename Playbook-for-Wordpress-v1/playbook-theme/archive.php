<?php
/**
 * Шаблон архива для Плейбука
 */
get_header();
?>

<main class="playbook-main">
    <div class="articles-container">
        <div class="articles-grid">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/article-card'); ?>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="no-results">
                    <p><?php esc_html_e('Ничего не найдено.', 'playbook'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php the_posts_pagination(array(
            'prev_text' => '<span class="screen-reader-text">' . __('Предыдущая', 'playbook') . '</span>',
            'next_text' => '<span class="screen-reader-text">' . __('Следующая', 'playbook') . '</span>',
        )); ?>
    </div>
</main>

<?php get_footer();