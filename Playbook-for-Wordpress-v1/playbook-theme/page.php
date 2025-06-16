<?php
/**
 * Шаблон страницы
 *
 * @package Playbook
 */

get_header();
?>

<main class="playbook-main">
    <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
        <header class="page-header">
            <?php the_title('<h1 class="page-title">', '</h1>'); ?>
        </header>

        <div class="page-content">
            <?php the_content(); ?>
        </div>
    </article>
</main>

<?php
get_footer();