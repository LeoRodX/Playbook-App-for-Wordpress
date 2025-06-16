<?php
/**
 * Шаблон карточки статьи для Плейбука
 *
 * @package Playbook
 */

if (!defined('ABSPATH')) {
    exit; // Запрет прямого доступа
}

global $post;
$categories = get_the_terms($post->ID, 'playbook_category');
$excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('article-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="article-thumbnail">
            <a href="<?php the_permalink(); ?>" class="article-link">
                <?php the_post_thumbnail('medium', array('class' => 'lazy', 'data-src' => get_the_post_thumbnail_url(), 'alt' => get_the_title())); ?>
            </a>
        </div>
    <?php endif; ?>
    
    <div class="article-content">
        <header class="article-header">
            <?php if (!empty($categories)) : ?>
                <div class="article-categories">
                    <?php foreach ($categories as $category) : ?>
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-tag">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <h3 class="article-title">
                <a href="<?php the_permalink(); ?>" class="article-link">
                    <?php the_title(); ?>
                </a>
            </h3>
            
            <div class="article-meta">
                <span class="meta-date">
                    <?php echo esc_html(get_the_date('d.m.Y')); ?>
                </span>
                <?php if ($post->post_author) : ?>
                    <span class="meta-author">
                        <?php echo esc_html(get_the_author_meta('display_name', $post->post_author)); ?>
                    </span>
                <?php endif; ?>
            </div>
        </header>
        
        <div class="article-excerpt">
            <?php echo esc_html($excerpt); ?>
        </div>
        
        <footer class="article-footer">
            <a href="<?php the_permalink(); ?>" class="read-more">
                <?php esc_html_e('Читать далее', 'playbook'); ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </footer>
    </div>
</article>