jQuery(document).ready(function($) {
    // Переменные для хранения текущего состояния
    let currentCategory = null;
    let currentSubcategory = null;
    let navigatorData = null;

    // Инициализация навигатора
    function initNavigator() {
        loadNavigatorData().then(function() {
            renderTopMenu();
            loadDefaultContent();
            initSearch();
        });
    }

    // Загрузка данных навигатора
    function loadNavigatorData() {
        return $.ajax({
            url: scenarioNavigatorFrontend.ajax_url,
            type: 'POST',
            data: {
                action: 'get_navigator_data',
                nonce: scenarioNavigatorFrontend.nonce
            },
            success: function(response) {
                if (response.success) {
                    navigatorData = response.data;
                }
            }
        });
    }
    
    
    // Рендер верхнего меню
        // Рендер верхнего меню
    function renderTopMenu() {
        if (!navigatorData) return;

        const categories = Object.keys(navigatorData);
        const defaultCategory = categories.length > 0 ? categories[0] : null;
        
        // Если категория не выбрана, выбираем первую
        if (!currentCategory && defaultCategory) {
            currentCategory = defaultCategory;
            
            const subcategories = Object.keys(navigatorData[defaultCategory].subcategories);
            if (subcategories.length > 0) {
                currentSubcategory = subcategories[0];
            }
        }

        // HTML для категорий (без изменений)
        let categoriesHtml = `
            <div class="top-menu-category">
                <button class="top-menu-main-btn">${currentCategory || 'Категории'}
                    <span class="dropdown-arrow">▼</span>
                </button>
                <div class="dropdown-content">
        `;

        categories.forEach(category => {
            categoriesHtml += `
                <button class="dropdown-item" data-category="${category}">
                    ${category}
                </button>
            `;
        });

        categoriesHtml += `</div></div>`;

        // HTML для подкатегорий (без изменений)
        let subcategoriesHtml = `
            <div class="top-menu-subcategory">
                <button class="top-menu-main-btn">${currentSubcategory || 'Подкатегории'}
                    <span class="dropdown-arrow">▼</span>
                </button>
                <div class="dropdown-content">
        `;

        if (currentCategory) {
            const subcategories = Object.keys(navigatorData[currentCategory].subcategories);
            subcategories.forEach(subcategory => {
                subcategoriesHtml += `
                    <button class="dropdown-item" data-subcategory="${subcategory}">
                        ${subcategory}
                    </button>
                `;
            });
        }

        subcategoriesHtml += `</div></div>`;


        // 1111111111111111111111111111111

        // HTML для вкладок (измененная часть)
// В функции renderTopMenu измените структуру tabsHtml:
let tabsHtml = '<div class="tab-container">';

if (currentCategory && currentSubcategory) {
    const subcategoryData = navigatorData[currentCategory].subcategories[currentSubcategory];
    
    // Вкладка сценария
    tabsHtml += `
        <button class="tab-btn scenario-tab active" data-page-id="${subcategoryData.scenario_page_id}">
            Сценарий решения
        </button>
    `;

    // Вкладки документов
    if (subcategoryData.document_page_ids && subcategoryData.document_page_ids.length > 0) {
        const docPromises = subcategoryData.document_page_ids.map(docId => {
            return $.ajax({
                url: scenarioNavigatorFrontend.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_page_title',
                    page_id: docId,
                    nonce: scenarioNavigatorFrontend.nonce
                }
            });
        });

        $.when.apply($, docPromises).done(function() {
            const responses = Array.from(arguments).map(arg => arg[0]);
            
            responses.forEach((response, i) => {
                const docId = subcategoryData.document_page_ids[i];
                const title = response.success ? response.data.title : `Док. ${docId}`;
                const shortTitle = title.length > 15 ? title.substring(0, 15) + '..' : title;
                
                tabsHtml += `
                    <button class="tab-btn document-tab" data-page-id="${docId}" title="${title}">
                        ${shortTitle}
                    </button>
                `;
            });
            
            tabsHtml += '</div>';
            $('.tab-container').replaceWith(tabsHtml);
            bindTabButtons();
            
            if (subcategoryData.scenario_page_id) {
                loadPageContent(subcategoryData.scenario_page_id);
            }
        }).fail(function() {
            subcategoryData.document_page_ids.forEach(docId => {
                tabsHtml += `
                    <button class="tab-btn document-tab" data-page-id="${docId}">
                        Док. ${docId}
                    </button>
                `;
            });
            
            tabsHtml += '</div>';
            $('.tab-container').replaceWith(tabsHtml);
            bindTabButtons();
            
            if (subcategoryData.scenario_page_id) {
                loadPageContent(subcategoryData.scenario_page_id);
            }
        });
    } else {
        tabsHtml += '</div>';
    }
}

        // 11111111111111111111111111111111111

        // Собираем все вместе
        const topMenuHtml = `
            <div class="top-menu-container">
                ${categoriesHtml}
                ${subcategoriesHtml}
                ${tabsHtml}
            </div>
        `;

        $('#navigator-buttons').html(topMenuHtml);
        initDropdowns();
    }

    // Новая функция для привязки обработчиков вкладок
    function bindTabButtons() {
        $('.tab-btn').off('click').on('click', function() {
            // Удаляем активный класс у всех вкладок
            $('.tab-btn').removeClass('active');
            
            // Добавляем активный класс текущей вкладке
            $(this).addClass('active');
            
            // Загружаем контент
            const pageId = $(this).data('page-id');
            loadPageContent(pageId);
        });
    }
    
    // Инициализация выпадающих меню
    function initDropdowns() {
        // Обработка клика по категории
        $('.top-menu-category .top-menu-main-btn').on('click', function() {
            $(this).siblings('.dropdown-content').toggle();
            $('.top-menu-subcategory .dropdown-content').hide();
        });

        // Обработка клика по подкатегории
        $('.top-menu-subcategory .top-menu-main-btn').on('click', function() {
            $(this).siblings('.dropdown-content').toggle();
            $('.top-menu-category .dropdown-content').hide();
        });

        // Выбор категории
        $('.top-menu-category .dropdown-item').on('click', function() {
            currentCategory = $(this).data('category');
            
            // Выбираем первую подкатегорию в новой категории
            const subcategories = Object.keys(navigatorData[currentCategory].subcategories);
            currentSubcategory = subcategories.length > 0 ? subcategories[0] : null;
            
            renderTopMenu();
            loadSubcategoryContent(currentSubcategory);
        });

        // Выбор подкатегории
        $('.top-menu-subcategory .dropdown-item').on('click', function() {
            currentSubcategory = $(this).data('subcategory');
            renderTopMenu();
            loadSubcategoryContent(currentSubcategory);
        });

        // Клик по сценарию
        $('.scenario-btn').on('click', function() {
            const pageId = $(this).data('page-id');
            loadPageContent(pageId);
        });

        // Клик по документам
        $('.document-btn').on('click', function() {
            const pageId = $(this).data('page-id');
            loadPageContent(pageId);
        });
    }

    // Инициализация поиска
    function initSearch() {
        $('.search-form').on('submit', function(e) {
            e.preventDefault();
            const searchTerm = $(this).find('.search-field').val().trim();
            
            if (!searchTerm) return;
            
            $('#navigator-content').html('<div class="loading">Поиск...</div>');
            
            $.ajax({
                url: scenarioNavigatorFrontend.ajax_url,
                type: 'POST',
                data: {
                    action: 'scenario_search',
                    search_term: searchTerm,
                    nonce: scenarioNavigatorFrontend.nonce
                },
                success: function(response) {
                    if (response.success) {
                        renderSearchResults(response.data, searchTerm);
                    } else {
                        $('#navigator-content').html('<div class="error">' + response.data + '</div>');
                    }
                },
                error: function() {
                    $('#navigator-content').html('<div class="error">Ошибка поиска</div>');
                }
            });
        });
    }

    // Функция для отображения результатов поиска
    function renderSearchResults(results, searchTerm) {
        let html = '<div class="search-results">';
        html += '<div class="search-header"><h3>Результаты поиска: "' + searchTerm + '"</h3>';
        html += '<button class="back-to-results" style="display:none;">← Вернуться к результатам</button></div>';
        html += '<div class="results-list">';
        
        if (results.length === 0) {
            html += '<div class="no-results">Ничего не найдено</div>';
        } else {
            results.forEach(function(result) {
                // Группируем подкатегории по категориям
                const categoriesMap = {};
                
                if (result.subcategories && result.subcategories.length > 0) {
                    result.subcategories.forEach(sub => {
                        if (!categoriesMap[sub.category_name]) {
                            categoriesMap[sub.category_name] = [];
                        }
                        if (!categoriesMap[sub.category_name].includes(sub.subcategory_name)) {
                            categoriesMap[sub.category_name].push({
                                name: sub.subcategory_name,
                                full_name: `${sub.category_name} → ${sub.subcategory_name}`
                            });
                        }
                    });
                }
                
                // Формируем HTML для подкатегорий
                let subcategoriesHtml = '';
                const categoryEntries = Object.entries(categoriesMap);
                
                if (categoryEntries.length > 0) {
                    subcategoriesHtml = '<div class="result-subcategories">';
                    subcategoriesHtml += '<strong>Ссылки перехода:</strong> ';
                    
                    const categoryLinks = [];
                    
                    categoryEntries.forEach(([category, subcategories]) => {
                        subcategories.forEach(sub => {
                            categoryLinks.push(
                                `<button class="subcategory-link" 
                                    data-category="${encodeURIComponent(category)}"
                                    data-subcategory="${encodeURIComponent(sub.name)}"
                                    title="${sub.full_name}">
                                    ${sub.full_name}
                                </button>`
                            );
                        });
                    });
                    
                    subcategoriesHtml += categoryLinks.join(', ');
                    subcategoriesHtml += '</div>';
                }
                
                html += `
                    <div class="search-result-item" data-page-id="${result.id}">
                        <h4>${result.title}</h4>
                        <div class="result-excerpt">${result.content}</div>
                        ${subcategoriesHtml}
                    </div>
                `;
            });
        }
        
        html += '</div></div>';
        
        $('#navigator-content').html(html);
    }

    // Обработка клика по подкатегории в результатах поиска
    $(document).on('click', '.subcategory-link', function(e) {
        e.preventDefault();
        const category = decodeURIComponent($(this).data('category'));
        const subcategory = decodeURIComponent($(this).data('subcategory'));
        
        // Устанавливаем текущие категорию и подкатегорию
        currentCategory = category;
        currentSubcategory = subcategory;
        
        // Обновляем верхнее меню
        renderTopMenu();
        
        // Загружаем контент подкатегории
        loadSubcategoryContent(subcategory);
    });

    // Загрузка контента подкатегории
    function loadSubcategoryContent(subcategoryName) {
        if (!subcategoryName) return;
        
        $('#navigator-content').html('<div class="loading">Загрузка...</div>');
        
        $.ajax({
            url: scenarioNavigatorFrontend.ajax_url,
            type: 'POST',
            data: {
                action: 'load_subcategory_content',
                subcategory_name: subcategoryName,
                nonce: scenarioNavigatorFrontend.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#navigator-content').html(response.data.content);
                }
            }
        });
    }

    // Загрузка контента страницы
    // Обновленная функция загрузки контента страницы
    function loadPageContent(pageId) {
        if (!pageId) return;
        
        $('#navigator-content').html('<div class="loading">Загрузка...</div>');
        
        $.ajax({
            url: scenarioNavigatorFrontend.ajax_url,
            type: 'POST',
            data: {
                action: 'load_document_content',
                page_id: pageId,
                nonce: scenarioNavigatorFrontend.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#navigator-content').html(`
                        <div class="tab-content active">
                            ${response.data.content}
                        </div>
                    `);
                }
            }
        });
    }

    // Функция для привязки обработчиков кликов к кнопкам документов
function bindDocumentButtons() {
    $('.document-btn').off('click').on('click', function() {
        const pageId = $(this).data('page-id');
        loadPageContent(pageId);
    });
}

    // Загрузка контента по умолчанию
    function loadDefaultContent() {
        if (currentSubcategory) {
            loadSubcategoryContent(currentSubcategory);
        } else {
            $('#navigator-content').html('<div class="default-content">Выберите категорию и подкатегорию для просмотра</div>');
        }
    }

    // Закрытие выпадающих меню при клике вне их
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.top-menu-category').length) {
            $('.top-menu-category .dropdown-content').hide();
        }
        if (!$(e.target).closest('.top-menu-subcategory').length) {
            $('.top-menu-subcategory .dropdown-content').hide();
        }
    });

    // Инициализация
    initNavigator();
});