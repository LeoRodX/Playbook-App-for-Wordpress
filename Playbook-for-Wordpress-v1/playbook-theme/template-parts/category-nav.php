<div class="category-navigation">
    <div class="category-container">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="category-item active">
            <?php esc_html_e('Все категории', 'playbook'); ?>
        </a>
        
        <?php 
        $categories = get_terms(array(
            'taxonomy' => 'playbook_category',
            'orderby' => 'name',
            'hide_empty' => true,
        ));
        
        foreach ($categories as $category) : ?>
            <a href="<?php echo esc_url(get_term_link($category)); ?>" class="category-item">
                <?php echo esc_html($category->name); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>