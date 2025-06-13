<?php
class Scenario_Navigator_Admin {

    private static $instance = null;
    private $table_name;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'scenario_navigator';

        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_save_navigator_structure', array($this, 'save_navigator_structure'));
        add_action('wp_ajax_get_navigator_data', array($this, 'get_navigator_data'));
        add_action('wp_ajax_get_all_pages', array($this, 'get_all_pages'));

        // В конструктор класса Scenario_Navigator_Admin добавьте:
        add_action('wp_ajax_export_playbook_stats', array($this, 'export_playbook_stats'));
    }

public function add_admin_menu() {
    add_menu_page(
        __('Конструктор Playbook', 'scenario-navigator'),
        __('Конструктор Playbook', 'scenario-navigator'),
        'manage_options',
        'scenario-navigator',
        array($this, 'render_admin_page'),
        'dashicons-book-alt',
        30
    );

    // Измененное название пункта меню
    add_submenu_page(
        'scenario-navigator',
        __('Отчет PlayBook', 'scenario-navigator'),
        __('Отчет PlayBook', 'scenario-navigator'),
        'manage_options',
        'playbook-reports',
        array($this, 'render_reports_page')
    );

    add_submenu_page(
        'scenario-navigator',
        __('Статистика PlayBook', 'scenario-navigator'),
        __('Статистика PlayBook', 'scenario-navigator'),
        'manage_options',
        'playbook-statistics',
        array($this, 'render_statistics_page')
    );

}

public function render_statistics_page() {
    global $wpdb;
    $stats_table = $wpdb->prefix . 'scenario_navigator_stats';
    
    // Обработка фильтров
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';
    
    // Формируем запрос с учетом фильтров
    $where = array();
    $query_params = array();
    
    if ($start_date) {
        $where[] = 'view_date >= %s';
        $query_params[] = $start_date . ' 00:00:00';
    }
    
    if ($end_date) {
        $where[] = 'view_date <= %s';
        $query_params[] = $end_date . ' 23:59:59';
    }
    
    $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Получаем статистику
    $stats_query = "SELECT 
        page_id, 
        page_title, 
        COUNT(*) as view_count 
        FROM {$stats_table} 
        {$where_clause}
        GROUP BY page_id 
        ORDER BY view_count DESC";
    
    if ($query_params) {
        $stats = $wpdb->get_results($wpdb->prepare($stats_query, $query_params));
    } else {
        $stats = $wpdb->get_results($stats_query);
    }
    
    ?>
    <div class="wrap">
        <h1><?php _e('Статистика просмотров PlayBook', 'scenario-navigator'); ?></h1>
        
        <div class="report-filters">
            <form method="get" action="<?php echo admin_url('admin.php'); ?>">
                <input type="hidden" name="page" value="playbook-statistics">
                
                <label for="start_date">Дата начала:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr($start_date); ?>">
                
                <label for="end_date">Дата окончания:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
                
                <button type="submit" class="button">Применить фильтр</button>
                <a href="<?php echo admin_url('admin.php?page=playbook-statistics'); ?>" class="button">Сбросить</a>
                
                <?php if (!empty($stats)): ?>
                    <button type="button" id="export-stats-csv" class="button button-primary" style="margin-left: auto;">
                        Экспорт в CSV
                    </button>
                <?php endif; ?>
            </form>
        </div>
        
        <?php if (!empty($stats)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID страницы</th>
                        <th>Название страницы</th>
                        <th>Количество просмотров</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $stat): ?>
                        <tr>
                            <td><?php echo intval($stat->page_id); ?></td>
                            <td>
                                <a href="<?php echo get_permalink($stat->page_id); ?>" target="_blank">
                                    <?php echo esc_html($stat->page_title); ?>
                                </a>
                            </td>
                            <td><?php echo intval($stat->view_count); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Нет данных для отображения</p>
        <?php endif; ?>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#export-stats-csv').on('click', function() {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            
            var url = '<?php echo admin_url('admin-ajax.php'); ?>?' + $.param({
                action: 'export_playbook_stats',
                start_date: start_date,
                end_date: end_date,
                nonce: '<?php echo wp_create_nonce('export_playbook_stats'); ?>'
            });
            
            window.location.href = url;
        });
    });
    </script>
    <?php
}


// Новый метод для экспорта
public function export_playbook_stats() {
    check_ajax_referer('export_playbook_stats', 'nonce');
    
    global $wpdb;
    $stats_table = $wpdb->prefix . 'scenario_navigator_stats';
    
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';
    
    $where = array();
    $query_params = array();
    
    if ($start_date) {
        $where[] = 'view_date >= %s';
        $query_params[] = $start_date . ' 00:00:00';
    }
    
    if ($end_date) {
        $where[] = 'view_date <= %s';
        $query_params[] = $end_date . ' 23:59:59';
    }
    
    $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    
    $query = "SELECT 
        page_id, 
        page_title, 
        COUNT(*) as view_count 
        FROM {$stats_table} 
        {$where_clause}
        GROUP BY page_id 
        ORDER BY view_count DESC";
    
    if ($query_params) {
        $stats = $wpdb->get_results($wpdb->prepare($query, $query_params));
    } else {
        $stats = $wpdb->get_results($query);
    }
    
    // Заголовки для CSV
    $headers = array(
        'ID страницы',
        'Название страницы',
        'Количество просмотров'
    );
    
    // Генерируем CSV
    $output = fopen('php://output', 'w');
    
    // Добавляем BOM для корректного отображения кириллицы в Excel
    echo "\xEF\xBB\xBF";
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=playbook_stats_' . date('Y-m-d') . '.csv');
    
    fputcsv($output, $headers, ';');
    
    foreach ($stats as $stat) {
        fputcsv($output, array(
            $stat->page_id,
            $stat->page_title,
            $stat->view_count
        ), ';');
    }
    
    fclose($output);
    exit;
}


// Новый метод для отображения страницы отчетов
public function render_reports_page() {
    global $wpdb;
    
    // Получаем все страницы
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    // Получаем структуру навигатора
    $table_name = $wpdb->prefix . 'scenario_navigator';
    $structure = $wpdb->get_results("SELECT * FROM {$table_name}", ARRAY_A);
    
    // Создаем массив для данных отчета
    $report_data = array();
    
    foreach ($structure as $item) {
        $category = $item['category_name'];
        $subcategory = $item['subcategory_name'];
        
        // Обрабатываем страницу сценария
        if ($item['scenario_page_id']) {
            $page = get_post($item['scenario_page_id']);
            if ($page) {
                $report_data[] = array(
                    'category' => $category,
                    'subcategory' => $subcategory,
                    'page_title' => $page->post_title,
                    'page_id' => $page->ID
                );
            }
        }
        
        // Обрабатываем документы
        $doc_ids = maybe_unserialize($item['document_page_ids']);
        if (is_array($doc_ids)) {
            foreach ($doc_ids as $doc_id) {
                $doc = get_post($doc_id);
                if ($doc) {
                    $report_data[] = array(
                        'category' => $category,
                        'subcategory' => $subcategory,
                        'page_title' => $doc->post_title,
                        'page_id' => $doc->ID
                    );
                }
            }
        }
    }
    
    // Сортируем данные по категории, подкатегории и названию страницы
    usort($report_data, function($a, $b) {
        return strcmp($a['category'] . $a['subcategory'] . $a['page_title'], 
                     $b['category'] . $b['subcategory'] . $b['page_title']);
    });
    
    // Получаем уникальные значения для фильтров
    $categories = array_unique(array_column($report_data, 'category'));
    $subcategories = array_unique(array_column($report_data, 'subcategory'));
    $page_titles = array_unique(array_column($report_data, 'page_title'));
    ?>
    <div class="wrap">
        <h1><?php _e('Отчет PlayBook', 'scenario-navigator'); ?></h1>
        
        <div id="playbook-report">
            <div class="report-filters">
                <select id="category-filter" class="report-filter" data-column="0">
                    <option value="">Все категории</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo esc_attr($cat); ?>"><?php echo esc_html($cat); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select id="subcategory-filter" class="report-filter" data-column="1">
                    <option value="">Все подкатегории</option>
                    <?php foreach ($subcategories as $subcat): ?>
                        <option value="<?php echo esc_attr($subcat); ?>"><?php echo esc_html($subcat); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select id="page-filter" class="report-filter" data-column="2">
                    <option value="">Все страницы</option>
                    <?php foreach ($page_titles as $title): ?>
                        <option value="<?php echo esc_attr($title); ?>"><?php echo esc_html($title); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <button id="reset-filters" class="button">Сбросить фильтры</button>
                <button id="export-csv" class="button button-primary">Экспорт в CSV</button>
            </div>
            
            <table id="report-table" class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Категория</th>
                        <th>Подкатегория</th>
                        <th>Страница</th>
                        <th>ID страницы</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report_data as $row): ?>
                        <tr>
                            <td><?php echo esc_html($row['category']); ?></td>
                            <td><?php echo esc_html($row['subcategory']); ?></td>
                            <td><?php echo esc_html($row['page_title']); ?></td>
                            <td><?php echo intval($row['page_id']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Инициализация фильтров таблицы
        $('.report-filter').on('change', function() {
            var column = $(this).data('column');
            var value = $(this).val();
            
            $('#report-table tbody tr').each(function() {
                var row = $(this);
                var cellText = row.find('td:eq(' + column + ')').text().trim();
                
                if (value === '' || cellText === value) {
                    row.show();
                } else {
                    row.hide();
                }
            });
        });
        
        // Сброс фильтров
        $('#reset-filters').on('click', function() {
            $('.report-filter').val('').trigger('change');
        });
        
           // Экспорт в CSV
$('#export-csv').on('click', function() {
    var csv = [];
    var headers = [];
    
    // Заголовки
    $('#report-table thead th').each(function() {
        headers.push($(this).text());
    });
    csv.push(headers.join(';'));
    
    // Данные
    $('#report-table tbody tr:visible').each(function() {
        var row = [];
        $(this).find('td').each(function() {
            row.push('"' + $(this).text().replace(/"/g, '""') + '"');
        });
        csv.push(row.join(';'));
    });
    
    // Создаем файл с правильной кодировкой
    var csvContent = '\uFEFF' + csv.join('\r\n'); // Добавляем BOM для UTF-8
    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'playbook_report_' + new Date().toISOString().slice(0, 10) + '.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});


    });
    </script>
    <?php
}

    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_scenario-navigator' !== $hook) {
            return;
        }

        wp_enqueue_style('scenario-navigator-admin', SCENARIO_NAVIGATOR_PLUGIN_URL . 'assets/css/admin.css', array(), SCENARIO_NAVIGATOR_VERSION);
        wp_enqueue_script('scenario-navigator-admin', SCENARIO_NAVIGATOR_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'jquery-ui-sortable'), SCENARIO_NAVIGATOR_VERSION, true);

        wp_localize_script('scenario-navigator-admin', 'scenarioNavigator', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('scenario-navigator-nonce'),
            'select_page_text' => __('Выберите страницу', 'scenario-navigator'),
            'add_subcategory_text' => __('Добавить подкатегорию', 'scenario-navigator'),
            'add_document_text' => __('Добавить документ', 'scenario-navigator'),
            'remove_text' => __('Удалить', 'scenario-navigator'),
        ));
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Конструктор навигатора сценариев', 'scenario-navigator'); ?></h1>
            
            <div id="scenario-navigator-builder">
                <div class="categories-container">
                    <h2><?php _e('Категории', 'scenario-navigator'); ?></h2>
                    <div id="categories-list" class="sortable-list">
                        <!-- Категории будут загружены через AJAX -->
                    </div>
                    <button id="add-category" class="button button-primary"><?php _e('Добавить категорию', 'scenario-navigator'); ?></button>
                </div>
                
                <div class="actions">
                    <button id="save-structure" class="button button-primary"><?php _e('Сохранить структуру', 'scenario-navigator'); ?></button>
                    <span id="save-message" class="save-message"></span>
                </div>
            </div>
        </div>
        <?php
    }

    public function get_navigator_data() {
        check_ajax_referer('scenario-navigator-nonce', 'nonce');

        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY category_order, subcategory_order", ARRAY_A);

        $categories = array();
        foreach ($results as $row) {
            $category_name = $row['category_name'];
            $subcategory_name = $row['subcategory_name'];
            
            if (!isset($categories[$category_name])) {
                $categories[$category_name] = array(
                    'order' => $row['category_order'],
                    'subcategories' => array()
                );
            }
            
            $categories[$category_name]['subcategories'][$subcategory_name] = array(
                'order' => $row['subcategory_order'],
                'scenario_page_id' => $row['scenario_page_id'],
                'document_page_ids' => maybe_unserialize($row['document_page_ids'])
            );
        }

        wp_send_json_success($categories);
    }

    public function get_all_pages() {
        check_ajax_referer('scenario-navigator-nonce', 'nonce');

        $pages = get_posts(array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        $formatted_pages = array();
        foreach ($pages as $page) {
            $formatted_pages[] = array(
                'ID' => $page->ID,
                'post_title' => $page->post_title
            );
        }

        wp_send_json_success($formatted_pages);
    }

    public function save_navigator_structure() {
        check_ajax_referer('scenario-navigator-nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('У вас недостаточно прав для выполнения этого действия.', 'scenario-navigator'));
        }

        $structure = isset($_POST['structure']) ? $_POST['structure'] : array();
        
        if (empty($structure)) {
            wp_send_json_error(__('Получены пустые данные.', 'scenario-navigator'));
        }

        global $wpdb;
        
        // Очищаем таблицу перед сохранением новой структуры
        $wpdb->query("TRUNCATE TABLE {$this->table_name}");

        foreach ($structure as $category_index => $category) {
            $category_name = sanitize_text_field($category['name']);
            $category_order = intval($category_index);
            
            foreach ($category['subcategories'] as $subcategory_index => $subcategory) {
                $subcategory_name = sanitize_text_field($subcategory['name']);
                $subcategory_order = intval($subcategory_index);
                $scenario_page_id = intval($subcategory['scenario_page_id']);
                
                $document_page_ids = array();
                if (!empty($subcategory['document_page_ids'])) {
                    foreach ($subcategory['document_page_ids'] as $doc_id) {
                        $document_page_ids[] = intval($doc_id);
                    }
                }
                
                $wpdb->insert(
                    $this->table_name,
                    array(
                        'category_name' => $category_name,
                        'category_order' => $category_order,
                        'subcategory_name' => $subcategory_name,
                        'subcategory_order' => $subcategory_order,
                        'scenario_page_id' => $scenario_page_id,
                        'document_page_ids' => maybe_serialize($document_page_ids)
                    ),
                    array('%s', '%d', '%s', '%d', '%d', '%s')
                );
            }
        }

        wp_send_json_success(__('Структура успешно сохранена.', 'scenario-navigator'));
    }
}