<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once 'itable.php';

/**
 * Handles the databse table to store Answers.
 */
class AnswersTable implements ITable {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new AnswersTable();
        }

        return self::$instance;
    }

    const TABLE_NAME = ITable::PREFIX . "answers";
    private $table_name;
    private $charset_collate;

    private function __construct() {
        global $wpdb;
        $this->table_name = self::TABLE_NAME;
        // We need this to support special characters from different languages as well.
        // $this->charset_collate = 'CHARACTER SET utf8mb4 COLLATE $utf8mb4_unicode_ci';
        $this->charset_collate = $wpdb->get_charset_collate();
    }

    public function create() {    
        $sql = "CREATE TABLE {$this->table_name} (
            attendee_id BIGINT(20) UNSIGNED NOT NULL,
            event_id BIGINT(20) UNSIGNED NOT NULL,
            status VARCHAR(50),
            date TIMESTAMP,
            message VARCHAR(10000),
            PRIMARY KEY (attendee_id, event_id)
        ) {$this->charset_collate};";
    
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    public function migrate() {
    }
    
    public function delete() {
    }
    
}

?>