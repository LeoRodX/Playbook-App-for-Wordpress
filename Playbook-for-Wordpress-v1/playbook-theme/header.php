<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?> <?php bloginfo('name'); ?></title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23C71585'><path d='M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 4h2v5l-1-.75L9 9V4zm9 16H6V4h1v9l3-2.25L13 13V4h5v16z'/></svg>">
    <?php wp_head(); ?>
</head>
<body <?php body_class('playbook-body'); ?>>
<header class="playbook-header">
    <div class="header-container">
        <div class="header-top">
            <!-- Логотип -->
            <div class="header-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/logo.png'); ?>" 
                             alt="<?php bloginfo('name'); ?>" 
                             class="logo-image">
                    <?php endif; ?>
                </a>                
            </div>
            <div class="logo-text-wrapper">
                <span class="logo-text">Playbook</span>
            </div>
            
            <!-- Поиск -->
            <div class="header-search">
                <form role="search" method="get" class="playbook-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" 
                           class="search-field" 
                           placeholder="<?php esc_attr_e('Поиск...', 'playbook'); ?>" 
                           value="<?php echo get_search_query(); ?>" 
                           name="s"
                           aria-label="<?php esc_attr_e('Поиск по справочнику', 'playbook'); ?>">
                    <button type="submit" class="search-button" aria-label="<?php esc_attr_e('Найти', 'playbook'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" 
                                  stroke="currentColor" 
                                  stroke-width="2"/>
                            <path d="M21 21L16.65 16.65" 
                                  stroke="currentColor" 
                                  stroke-width="2" 
                                  stroke-linecap="round"/>
                        </svg>
                    </button>
                </form>
            </div>
            
            <!-- Помощь -->
            <div class="header-help">
                <div class="help-container">
                   <button class="help-button" aria-expanded="false" aria-controls="help-tooltip">
    <svg viewBox="0 0 24 24" width="32" height="32">
        <path  fill="#109CD8" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
    </svg>
    <span class="screen-reader-text">Справка</span>
</button>
                    <div id="help-tooltip" class="help-tooltip">
                        <div class="tooltip-content">
                            <h2 class="tooltip-title"><?php esc_html_e('О приложении Playbook', 'playbook'); ?></h2>
                            <p class="tooltip-list1"></p>
                            <p><span class="tooltip-list">Основные функции:</span></p>
                            <ul class="tooltip-list">
                                <li><?php esc_html_e('Предварительный просмотр', 'playbook'); ?></li>
                                <li><?php esc_html_e('Поиск по статьям', 'playbook'); ?></li>
                                <li><?php esc_html_e('Фильтрация по категориям', 'playbook'); ?></li>                                
                            </ul>
                            <div class="tooltip-contacts">
                                         <a href="mailto:support@example.com" class="contact-link">
                                    <?php esc_html_e('support@example.com', 'playbook'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Навигация по категориям -->
        <nav class="header-categories" aria-label="<?php esc_attr_e('Категории статей', 'playbook'); ?>">
            <div>
                <a href="<?php echo esc_url(home_url('/')); ?>" 
                   class="category-tag <?php echo is_front_page() ? 'active' : ''; ?>">
                    <?php esc_html_e('Все', 'playbook'); ?>
                </a>
                
                <?php 
                $categories = get_terms([
                    'taxonomy' => 'playbook_category',
                    'orderby' => 'name',
                    'hide_empty' => true,
                    'exclude' => [15, 32] // Пример исключения конкретных категорий
                ]);
                
                if (!empty($categories) && !is_wp_error($categories)) :
                    foreach ($categories as $category) : 
                        $is_active = (isset($_GET['playbook_category']) && $_GET['playbook_category'] === $category->slug);
                        ?>
                        <a href="<?php echo esc_url(add_query_arg('playbook_category', $category->slug, home_url('/'))); ?>" 
                           class="category-tag <?php echo $is_active ? 'active' : ''; ?>"
                           data-category="<?php echo esc_attr($category->slug); ?>">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    <?php endforeach;
                endif; ?>
            </div>
        </nav>
    </div>
</header>

<main id="main-content" class="playbook-main">