jQuery(document).ready(function($) {
    // Загружаем данные при открытии страницы
    loadNavigatorData();
    
    // Функция загрузки данных навигатора
    function loadNavigatorData() {
        $('#categories-list').html('<div class="loading">Загрузка данных...</div>');
        
        $.ajax({
            url: scenarioNavigator.ajax_url,
            type: 'POST',
            data: {
                action: 'get_navigator_data',
                nonce: scenarioNavigator.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Загружаем список всех страниц перед отрисовкой
                    loadAllPages().then(function(pages) {
                        renderCategories(response.data, pages);
                    });
                } else {
                    $('#categories-list').html('<div class="error">Ошибка загрузки данных: ' + response.data + '</div>');
                }
            },
            error: function() {
                $('#categories-list').html('<div class="error">Ошибка загрузки данных</div>');
            }
        });
    }
    
    // Загрузка всех страниц
    function loadAllPages() {
        return $.ajax({
            url: scenarioNavigator.ajax_url,
            type: 'POST',
            data: {
                action: 'get_all_pages',
                nonce: scenarioNavigator.nonce
            }
        }).then(function(response) {
            if (response.success) {
                return response.data;
            }
            return [];
        });
    }
    
    // Функция отрисовки категорий
    function renderCategories(data, allPages) {
        if (!data || Object.keys(data).length === 0) {
            $('#categories-list').html('<div class="empty-message">Нет созданных категорий. Добавьте первую категорию.</div>');
            return;
        }
        
        let html = '';
        
        // Сортируем категории по порядку
        const sortedCategories = Object.entries(data).sort((a, b) => a[1].order - b[1].order);
        
        sortedCategories.forEach(([categoryName, categoryData]) => {
            html += `
                <div class="category-item" data-category="${escapeHtml(categoryName)}">
                    <div class="category-header">
                        <input type="text" class="category-name" value="${escapeHtml(categoryName)}" placeholder="Название категории">
                        <button class="button remove-category">${scenarioNavigator.remove_text}</button>
                    </div>
                    <div class="subcategories-container">
                        <div class="subcategories-list sortable-list">
            `;
            
            // Сортируем подкатегории по порядку
            const sortedSubcategories = Object.entries(categoryData.subcategories).sort((a, b) => a[1].order - b[1].order);
            
            sortedSubcategories.forEach(([subcategoryName, subcategoryData]) => {
                html += renderSubcategoryItem(categoryName, subcategoryName, subcategoryData, allPages);
            });
            
            html += `
                        </div>
                        <button class="button add-subcategory" data-category="${escapeHtml(categoryName)}">
                            ${scenarioNavigator.add_subcategory_text}
                        </button>
                    </div>
                </div>
            `;
        });
        
        $('#categories-list').html(html);
        initSortable();
    }
    
    // Функция отрисовки элемента подкатегории
    function renderSubcategoryItem(categoryName, subcategoryName, subcategoryData, allPages) {
        let documentsHtml = '';
        
        if (subcategoryData.document_page_ids && subcategoryData.document_page_ids.length > 0) {
            subcategoryData.document_page_ids.forEach(docId => {
                documentsHtml += `
                    <div class="document-item">
                        ${generatePagesDropdown(`document_page_ids[${categoryName}][${subcategoryName}][]`, docId, allPages)}
                        <button class="button remove-document">${scenarioNavigator.remove_text}</button>
                    </div>
                `;
            });
        }
        
        return `
            <div class="subcategory-item" data-subcategory="${escapeHtml(subcategoryName)}">
                <div class="subcategory-header">
                    <input type="text" class="subcategory-name" value="${escapeHtml(subcategoryName)}" placeholder="Название подкатегории">
                    <button class="button remove-subcategory">${scenarioNavigator.remove_text}</button>
                </div>
                <div class="subcategory-content">
                    <div class="scenario-page-field">
                        <label>Страница сценария:</label>
                        ${generatePagesDropdown(`scenario_page_id[${categoryName}][${subcategoryName}]`, subcategoryData.scenario_page_id, allPages)}
                    </div>
                    <div class="documents-container">
                        <label>Нормативные документы:</label>
                        <div class="documents-list">
                            ${documentsHtml}
                        </div>
                        <button class="button add-document" data-category="${escapeHtml(categoryName)}" data-subcategory="${escapeHtml(subcategoryName)}">
                            ${scenarioNavigator.add_document_text}
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Генерация выпадающего списка страниц
    function generatePagesDropdown(name, selected, pages) {
        let html = `<select name="${name}">`;
        html += `<option value="">${scenarioNavigator.select_page_text}</option>`;
        
        pages.forEach(page => {
            html += `<option value="${page.ID}" ${page.ID == selected ? 'selected' : ''}>${page.post_title}</option>`;
        });
        
        html += '</select>';
        return html;
    }
    
    // Остальные функции остаются без изменений
    function initSortable() {
        $('.sortable-list').sortable({
            handle: '.drag-handle',
            axis: 'y',
            update: function() {
                // Можно добавить логику сохранения порядка при необходимости
            }
        }).disableSelection();
    }
    
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    // Добавление новой категории
    $('#add-category').on('click', function() {
        const categoryName = 'Новая категория ' + (Math.floor(Math.random() * 1000) + 1);
        const categoryHtml = `
            <div class="category-item" data-category="${escapeHtml(categoryName)}">
                <div class="category-header">
                    <input type="text" class="category-name" value="${escapeHtml(categoryName)}" placeholder="Название категории">
                    <button class="button remove-category">${scenarioNavigator.remove_text}</button>
                </div>
                <div class="subcategories-container">
                    <div class="subcategories-list sortable-list">
                    </div>
                    <button class="button add-subcategory" data-category="${escapeHtml(categoryName)}">
                        ${scenarioNavigator.add_subcategory_text}
                    </button>
                </div>
            </div>
        `;
        
        if ($('#categories-list .empty-message').length) {
            $('#categories-list').html(categoryHtml);
        } else {
            $('#categories-list').append(categoryHtml);
        }
        
        initSortable();
    });
    
    // Удаление категории
    $(document).on('click', '.remove-category', function() {
        $(this).closest('.category-item').remove();
        
        if ($('#categories-list .category-item').length === 0) {
            $('#categories-list').html('<div class="empty-message">Нет созданных категорий. Добавьте первую категорию.</div>');
        }
    });
    
    // Добавление подкатегории
    $(document).on('click', '.add-subcategory', function() {
        const categoryName = $(this).data('category');
        const subcategoryName = 'Новая подкатегория ' + (Math.floor(Math.random() * 1000) + 1);
        
        loadAllPages().then(function(pages) {
            const subcategoryData = {
                scenario_page_id: 0,
                document_page_ids: [],
                order: $(this).siblings('.subcategories-list').find('.subcategory-item').length
            };
            
            const subcategoryHtml = renderSubcategoryItem(categoryName, subcategoryName, subcategoryData, pages);
            $(this).siblings('.subcategories-list').append(subcategoryHtml);
        }.bind(this));
    });
    
    // Удаление подкатегории
    $(document).on('click', '.remove-subcategory', function() {
        $(this).closest('.subcategory-item').remove();
    });
    
    // Добавление документа
    $(document).on('click', '.add-document', function() {
        const categoryName = $(this).data('category');
        const subcategoryName = $(this).data('subcategory');
        
        loadAllPages().then(function(pages) {
            const documentHtml = `
                <div class="document-item">
                    ${generatePagesDropdown(`document_page_ids[${categoryName}][${subcategoryName}][]`, '', pages)}
                    <button class="button remove-document">${scenarioNavigator.remove_text}</button>
                </div>
            `;
            
            $(this).siblings('.documents-list').append(documentHtml);
        }.bind(this));
    });
    
    // Удаление документа
    $(document).on('click', '.remove-document', function() {
        $(this).closest('.document-item').remove();
    });
    
    // Сохранение структуры
    $('#save-structure').on('click', function() {
        const saveButton = $(this);
        const saveMessage = $('#save-message');
        
        saveButton.prop('disabled', true);
        saveMessage.text('Сохранение...').removeClass('error success');
        
        const structure = [];
        
        $('#categories-list .category-item').each(function(categoryIndex) {
            const categoryName = $(this).find('.category-name').val();
            const categoryData = {
                name: categoryName,
                subcategories: []
            };
            
            $(this).find('.subcategory-item').each(function(subcategoryIndex) {
                const subcategoryName = $(this).find('.subcategory-name').val();
                const scenarioPageId = $(this).find('.scenario-page-field select').val();
                
                const documentPageIds = [];
                $(this).find('.documents-list select').each(function() {
                    if ($(this).val()) {
                        documentPageIds.push($(this).val());
                    }
                });
                
                categoryData.subcategories.push({
                    name: subcategoryName,
                    scenario_page_id: scenarioPageId,
                    document_page_ids: documentPageIds
                });
            });
            
            structure.push(categoryData);
        });
        
        $.ajax({
            url: scenarioNavigator.ajax_url,
            type: 'POST',
            data: {
                action: 'save_navigator_structure',
                structure: structure,
                nonce: scenarioNavigator.nonce
            },
            success: function(response) {
                if (response.success) {
                    saveMessage.text(response.data).addClass('success');
                } else {
                    saveMessage.text(response.data).addClass('error');
                }
            },
            error: function() {
                saveMessage.text('Ошибка при сохранении').addClass('error');
            },
            complete: function() {
                saveButton.prop('disabled', false);
                setTimeout(function() {
                    saveMessage.text('').removeClass('error success');
                }, 3000);
            }
        });
    });
});