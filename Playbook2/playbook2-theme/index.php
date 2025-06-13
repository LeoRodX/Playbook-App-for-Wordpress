<?php
/**
 * The main template file.
 *
 * @package Playbook2
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="main-content">
    

<?php echo do_shortcode('[scenario_navigator]'); ?>


</div>

<?php
get_footer();