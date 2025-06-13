<?php
class Scenario_Navigator_Shortcode {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('scenario_navigator', array($this, 'render_shortcode'));
        add_action('wp_ajax_load_subcategory_content', array($this, 'load_subcategory_content'));
        add_action('wp_ajax_nopriv_load_subcategory_content', array($this, 'load_subcategory_content'));
        add_action('wp_ajax_load_document_content', array($this, 'load_document_content'));
        add_action('wp_ajax_nopriv_load_document_content', array($this, 'load_document_content'));
    }

    public function render_shortcode($atts) {
        $frontend = Scenario_Navigator_Frontend::get_instance();
        return $frontend->render_navigator();
    }

    public function load_subcategory_content() {
        check_ajax_referer('scenario-navigator-frontend-nonce', 'nonce');

        $subcategory_name = isset($_POST['subcategory_name']) ? sanitize_text_field($_POST['subcategory_name']) : '';
        
        if (empty($subcategory_name)) {
            wp_send_json_error(__('Не указана подкатегория.', 'scenario-navigator'));
        }

        $frontend = Scenario_Navigator_Frontend::get_instance();
        $subcategory_data = $frontend->get_subcategory_data($subcategory_name);
        
        if (!$subcategory_data) {
            wp_send_json_error(__('Данные подкатегории не найдены.', 'scenario-navigator'));
        }

        // Генерируем HTML для кнопок
        $buttons_html = '<div class="navigator-buttons-container">';
        
        // Кнопка сценария
        $scenario_page = get_post($subcategory_data['scenario_page_id']);
        if ($scenario_page) {
            $buttons_html .= sprintf(
                '<button class="navigator-button scenario-button active" data-page-id="%d">%s</button>',
                $subcategory_data['scenario_page_id'],
                __('Сценарий решения', 'scenario-navigator')
            );
        }
        
        // Кнопки документов
        if (!empty($subcategory_data['document_page_ids'])) {
            foreach ($subcategory_data['document_page_ids'] as $doc_id) {
                $doc_page = get_post($doc_id);
                if ($doc_page) {
                    $buttons_html .= sprintf(
                        '<button class="navigator-button document-button" data-page-id="%d">%s</button>',
                        $doc_id,
                        esc_html($doc_page->post_title)
                    );
                }
            }
        }
        
        $buttons_html .= '</div>';
        
        // Контент сценария по умолчанию
        $content = '';
        if ($scenario_page) {
            $content = apply_filters('the_content', $scenario_page->post_content);
        }
        
        wp_send_json_success(array(
            'buttons_html' => $buttons_html,
            'content' => $content
        ));
    }

    public function load_document_content() {
        check_ajax_referer('scenario-navigator-frontend-nonce', 'nonce');

        $page_id = isset($_POST['page_id']) ? intval($_POST['page_id']) : 0;
        
        if (!$page_id) {
            wp_send_json_error(__('Не указан ID страницы.', 'scenario-navigator'));
        }

        $page = get_post($page_id);
        
        if (!$page) {
            wp_send_json_error(__('Страница не найдена.', 'scenario-navigator'));
        }

        $content = apply_filters('the_content', $page->post_content);
        
        wp_send_json_success(array(
            'content' => $content
        ));
    }
}