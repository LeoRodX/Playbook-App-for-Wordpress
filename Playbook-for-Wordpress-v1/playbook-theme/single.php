<?php
/**
 * Шаблон отдельной статьи
 *
 * @package Playbook
 */

get_header();
?>

<main class="playbook-main">
    <article id="post-<?php the_ID(); ?>" <?php post_class('single-article'); ?>>
    
       <?php get_template_part('template-parts/article'); ?>
                
    </article>
</main>

<?php
get_footer();
?>
