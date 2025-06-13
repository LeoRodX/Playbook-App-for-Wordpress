<?php
class Scenario_Navigator_Frontend {

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

        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_scenario_search', array($this, 'handle_search'));
        add_action('wp_ajax_nopriv_scenario_search', array($this, 'handle_search'));
        add_action('wp_ajax_load_document_content', array($this, 'load_document_content'));
        add_action('wp_ajax_nopriv_load_document_content', array($this, 'load_document_content'));
        add_action('wp_ajax_load_subcategory_content', array($this, 'load_subcategory_content'));
        add_action('wp_ajax_nopriv_load_subcategory_content', array($this, 'load_subcategory_content'));
        add_action('wp_ajax_get_navigator_data', array($this, 'get_navigator_data'));
        add_action('wp_ajax_nopriv_get_navigator_data', array($this, 'get_navigator_data'));

        add_action('wp_ajax_get_page_title', array($this, 'get_page_title'));
        add_action('wp_ajax_nopriv_get_page_title', array($this, 'get_page_title'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('scenario-navigator-frontend', SCENARIO_NAVIGATOR_PLUGIN_URL . 'assets/css/frontend.css', array(), SCENARIO_NAVIGATOR_VERSION);
        wp_enqueue_script('scenario-navigator-frontend', SCENARIO_NAVIGATOR_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), SCENARIO_NAVIGATOR_VERSION, true);

        wp_localize_script('scenario-navigator-frontend', 'scenarioNavigatorFrontend', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('scenario-navigator-frontend-nonce'),
            'default_content' => __('Выберите категорию и подкатегорию для просмотра', 'scenario-navigator')
        ));
    }

    public function render_navigator() {
        ob_start();
        ?>
        <div class="scenario-navigator-container">
            <div class="navigator-top-panel" id="navigator-buttons">
                <!-- Верхнее меню будет сгенерировано JavaScript -->
                <div class="loading-menu">Загрузка меню...</div>
            </div>
            <div class="navigator-bottom-panel" id="navigator-content">
                <?php echo esc_html($this->get_default_content()); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function get_default_content() {
        return '<div class="default-content">Выберите категорию и подкатегорию для просмотра содержимого</div>';
    }

    public function get_navigator_data() {
        check_ajax_referer('scenario-navigator-frontend-nonce', 'nonce');

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

    public function load_subcategory_content() {
        check_ajax_referer('scenario-navigator-frontend-nonce', 'nonce');
        
        $subcategory_name = isset($_POST['subcategory_name']) ? sanitize_text_field($_POST['subcategory_name']) : '';
        
        if (empty($subcategory_name)) {
            wp_send_json_error('Не указана подкатегория');
        }
        
        global $wpdb;
        
        $data = $wpdb->get_row($wpdb->prepare("
            SELECT scenario_page_id, document_page_ids 
            FROM {$this->table_name} 
            WHERE subcategory_name = %s 
            LIMIT 1
        ", $subcategory_name), ARRAY_A);
        
        if (!$data) {
            wp_send_json_error('Подкатегория не найдена');
        }
        
        $scenario_page_id = $data['scenario_page_id'];
        $document_page_ids = maybe_unserialize($data['document_page_ids']);
        
        $buttons_html = '';
        $content_html = '';
        
        // Загружаем контент страницы сценария по умолчанию
        if ($scenario_page_id) {
            $page = get_post($scenario_page_id);
            if ($page) {
                $content_html = '<div class="document-content">';
                $content_html .= '<h2>' . esc_html($page->post_title) . '</h2>';
                $content_html .= apply_filters('the_content', $page->post_content);
                $content_html .= '</div>';
            }
        }
        
        wp_send_json_success(array(
            'buttons_html' => $buttons_html,
            'content' => $content_html ?: '<div class="no-content">Контент не найден</div>'
        ));
    }

    
    public function load_document_content() {
    check_ajax_referer('scenario-navigator-frontend-nonce', 'nonce');
    
    $page_id = intval($_POST['page_id']);
    $page = get_post($page_id);
    
    if (!$page) {
        wp_send_json_error('Страница не найдена');
    }
    
    // Записываем статистику просмотра
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'scenario_navigator_stats',
        array(
            'page_id' => $page_id,
            'page_title' => $page->post_title,
            'view_date' => current_time('mysql')
        ),
        array('%d', '%s', '%s')
    );
    
    $content = apply_filters('the_content', $page->post_content);
    
    $html = '<div class="document-content">';
    $html .= '<h2>' . esc_html($page->post_title) . '</h2>';
    $html .= $content;
    $html .= '</div>';
    
    wp_send_json_success(array('content' => $html));
    }


    public function get_page_title() {
    check_ajax_referer('scenario-navigator-frontend-nonce', 'nonce');
    
    $page_id = intval($_POST['page_id']);
    $page = get_post($page_id);
    
    if (!$page) {
        wp_send_json_error('Страница не найдена');
    }
    
    wp_send_json_success(array(
        'title' => $page->post_title
    ));
}

    public function handle_search() {
    check_ajax_referer('scenario-navigator-frontend-nonce', 'nonce');
    
    $search_term = sanitize_text_field($_POST['search_term']);
    
    if (empty($search_term)) {
        wp_send_json_error('Введите поисковый запрос');
    }
    
    global $wpdb;
    
    // Ищем страницы, содержащие искомую фразу
    $results = $wpdb->get_results($wpdb->prepare("
        SELECT ID, post_title, post_content 
        FROM {$wpdb->posts} 
        WHERE post_type = 'page' 
        AND post_status = 'publish'
        AND (post_title LIKE %s OR post_content LIKE %s)
    ", '%' . $wpdb->esc_like($search_term) . '%', '%' . $wpdb->esc_like($search_term) . '%'), ARRAY_A);
    
    if (empty($results)) {
        wp_send_json_error('Ничего не найдено');
    }
    
    $formatted_results = array();
    foreach ($results as $result) {
        // Формируем отрывок с подсветкой
        $content = $this->get_highlighted_excerpt($result['post_content'], $search_term);
        $title = $this->highlight_search_term($result['post_title'], $search_term);
        
        // Получаем подкатегории для этой страницы
        $subcategories = $wpdb->get_results($wpdb->prepare("
            SELECT category_name, subcategory_name 
            FROM {$this->table_name} 
            WHERE scenario_page_id = %d 
            OR document_page_ids LIKE %s
            OR document_page_ids LIKE %s
            OR document_page_ids LIKE %s
            OR document_page_ids = %s
        ", 
        $result['ID'],
        '%i:' . $result['ID'] . ';%',
        '%i:' . $result['ID'] . '}%',
        '%"' . $result['ID'] . '"%',
        $result['ID']), ARRAY_A);
        
        $formatted_results[] = array(
            'id' => $result['ID'],
            'title' => $title,
            'content' => $content,
            'full_content' => $result['post_content'],
            'subcategories' => $subcategories
        );
    }
    
    wp_send_json_success($formatted_results);
    }

private function get_highlighted_excerpt($content, $search_term, $length = 300) {
    // Удаляем HTML-теги
    $text = strip_tags($content);
    $text = preg_replace('/\s+/', ' ', $text);
    
    // Находим позицию первого вхождения
    $pos = stripos($text, $search_term);
    
    if ($pos === false) {
        return wp_trim_words($text, 30, '...');
    }
    
    // Вычисляем начальную позицию отрывка
    $start = max(0, $pos - $length/2);
    $end = min(strlen($text), $pos + strlen($search_term) + $length/2);
    
    // Формируем отрывок
    $excerpt = substr($text, $start, $end - $start);
    
    // Добавляем многоточия, если не начало/конец
    if ($start > 0) $excerpt = '...' . $excerpt;
    if ($end < strlen($text)) $excerpt = $excerpt . '...';
    
    // Подсвечиваем искомую фразу
    $excerpt = $this->highlight_search_term($excerpt, $search_term);
    
    return $excerpt;
    }

    private function highlight_search_term($text, $term) {
        return preg_replace("/(" . preg_quote($term, '/') . ")/i", '<span class="search-highlight">$1</span>', $text);
    }

    public function get_subcategory_data($subcategory_name) {
        global $wpdb;
        
        $data = $wpdb->get_row($wpdb->prepare("
            SELECT scenario_page_id, document_page_ids 
            FROM {$this->table_name} 
            WHERE subcategory_name = %s 
            LIMIT 1
        ", $subcategory_name), ARRAY_A);
        
        if (!$data) {
            return false;
        }
        
        $result = array(
            'scenario_page_id' => $data['scenario_page_id'],
            'document_page_ids' => maybe_unserialize($data['document_page_ids'])
        );
        
        return $result;
    }
}