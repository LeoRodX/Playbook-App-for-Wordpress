<?php
/*
Plugin Name: Меню и конструктор Playbook 
Description: Плагин основного функционала приложения Playbook [scenario_navigator]
Version: 2.0
Author: Terre
Text Domain: scenario-navigator
Domain Path: /languages
*/

defined('ABSPATH') or die('Прямой доступ запрещен!');

// Константы плагина
define('SCENARIO_NAVIGATOR_VERSION', '1.0');
define('SCENARIO_NAVIGATOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SCENARIO_NAVIGATOR_PLUGIN_URL', plugin_dir_url(__FILE__));

// Подключение классов
require_once SCENARIO_NAVIGATOR_PLUGIN_DIR . 'includes/class-scenario-navigator-admin.php';
require_once SCENARIO_NAVIGATOR_PLUGIN_DIR . 'includes/class-scenario-navigator-frontend.php';
require_once SCENARIO_NAVIGATOR_PLUGIN_DIR . 'includes/class-scenario-navigator-shortcode.php';

class Scenario_Navigator {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Инициализация плагина
        add_action('plugins_loaded', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
    }

    public function init() {
        // Локализация
        load_plugin_textdomain('scenario-navigator', false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Инициализация компонентов
        Scenario_Navigator_Admin::get_instance();
        Scenario_Navigator_Frontend::get_instance();
        Scenario_Navigator_Shortcode::get_instance();
    }

    public function activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'scenario_navigator';
    $stats_table = $wpdb->prefix . 'scenario_navigator_stats';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        category_name varchar(255) NOT NULL,
        category_order int(11) NOT NULL DEFAULT 0,
        subcategory_name varchar(255) NOT NULL,
        subcategory_order int(11) NOT NULL DEFAULT 0,
        scenario_page_id int(11) NOT NULL,
        document_page_ids text NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;

    CREATE TABLE $stats_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        page_id int(11) NOT NULL,
        page_title varchar(255) NOT NULL,
        view_date datetime NOT NULL,
        PRIMARY KEY  (id),
        KEY page_id (page_id),
        KEY view_date (view_date)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
}

// Запуск плагина
Scenario_Navigator::get_instance();

