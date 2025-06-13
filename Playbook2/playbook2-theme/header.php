<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <title>Playbook</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><rect width='16' height='16' rx='3' fill='%230073aa'/><text x='50%' y='50%' font-size='12' font-family='Arial, sans-serif' font-weight='bold' fill='%23fff' text-anchor='middle' dominant-baseline='middle'>P2</text></svg>">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <div id="wrapper">
        <header>
            <div class="header-content">
                <div class="header-left">
                    <div class="logo-container">
                        <img class="logo" src="<?php echo esc_url(get_template_directory_uri() . '/logo.png'); ?>"> 
                    </div>  
                    <div class="logo-text-wrapper">
                        <span class="logo-text">Playbook</span>
                        <span class="logo-text1">Сценарии решений</span>
                    </div>
                </div>
                <div class="header-search-container">
                    <?php get_search_form(); ?>
                    <button class="help-button">?</button>
                </div>
            </div>    
            <div id="aboutModal" class="about-modal" aria-hidden="true">
                <div class="about-modal-content">
                    <button class="about-close" aria-label="Закрыть">&times;</button>                       
                    <body>
                        <h3>О приложении Playbook</h3>
                        <hr class="divider">
                        <p><strong>Playbook</strong> – это справочное приложение, которое предоставляет быстрый поиск готовых сценариев решений
                         и нормативных документов, уменьшая время ответов на обращения и повышая эффективность поддержки клиентов.</p>
                        Основные функции:<p>
                        1. Поиск сценариев и документов
                        <ul>
                            <li><strong>Поисковая строка</strong> расположена в верхней части экрана.</li>
                            <li><strong>Подсветка совпадений</strong>: найденные фрагменты выделяются в результатах поиска.</li>
                            <li><strong>Ссылки перехода</strong>: внизу каждого фрагмента для открытия документа в формате подкатегорий.</li>
                        </ul>
                        2. Навигация по двухуровневому меню
                        <ul>
                            <li><strong>Первый уровень</strong> – категории.</li>
                            <li><strong>Второй уровень</strong> – подкатегории.</li>
                            <li>При выборе подкатегории отображается соответствующий сценарий решения и вкладки с релевантными нормативными документами.</li>
                        </ul>
                        3. Администрирование справочника
                        <ul>
                            <li><strong>Конструктор</strong> – размещение документов.</li>
                            <li><strong>Отчет</strong> – проверка структуры размещения документов в справочнике.</li>
                            <li><strong>Статистика</strong> – анализ использования документов.</li>
                        </ul>
                        <p>© MDIS&M Lab, 2025</p>
                </div>
            </div>
        </header>    

