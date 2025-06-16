<?php
/**
 * Шаблон 404 ошибки
 *
 * @package Playbook
 */

get_header();
?>

<main class="playbook-main">
    <section class="error-404">
        <div class="error-content">
            <h1><?php esc_html_e('404', 'playbook'); ?></h1>
            <h2><?php esc_html_e('Страница не найдена', 'playbook'); ?></h2>
            <p><?php esc_html_e('Извините, запрашиваемая страница не существует или была перемещена.', 'playbook'); ?></p>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="button">
                <?php esc_html_e('Вернуться на главную', 'playbook'); ?>
            </a>
        </div>
    </section>
</main>

<?php
get_footer();