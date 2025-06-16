<?php
/**
 * Шаблон карточки статьи для Плейбука
 *
 * @package Playbook
 */

if (!defined('ABSPATH')) {
    exit; // Запрет прямого доступа
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('article-card'); ?>>
    <div class="article-content">        
        <header class="article-header">            
            <h3 class="article-title">               
                <?php the_title(); ?>               
            </h3>                        
        </header>        
        <div class="article-excerpt">
            <?php the_content(); ?>
        </div>          
    </div>
</article>