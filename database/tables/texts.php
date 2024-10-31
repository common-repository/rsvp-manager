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
 * Handles the databse table to store the texts used in UI.
 */
class TextsTable implements ITable {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new TextsTable();
        }

        return self::$instance;
    }

    const TABLE_NAME = ITable::PREFIX . "texts";

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
        global $wpdb;
        $sql = "CREATE TABLE {$this->table_name} (
            event_id BIGINT(20) UNSIGNED NOT NULL,
            search_head_message TEXT,
            rsvp_is_not_open_message TEXT,
            rsvp_is_closed_message TEXT,
            first_name_label VARCHAR(100),
            last_name_label VARCHAR(100),
            search_button_label VARCHAR(50),
            error_empty_first_or_last_name VARCHAR(500),
            error_no_attendees_found VARCHAR(500),
            rsvp_button_label VARCHAR(50),
            rsvp_already_answer_label VARCHAR(1000),
            rsvp_already_answer_yes_button_label VARCHAR(50),
            rsvp_already_answer_no_button_label VARCHAR(50),
            rsvp_welcome_label VARCHAR(500),
            rsvp_question VARCHAR(1000),
            rsvp_answer_yes VARCHAR(50),
            rsvp_answer_no VARCHAR(50),
            rsvp_message_title VARCHAR(500),
            rsvp_related_attendees_info_message TEXT,
            rsvp_relates_attendees_rsvp_question TEXT,
            rsvp_confirm_button_label VARCHAR(50),
            rsvp_yes_message VARCHAR(1000),
            rsvp_no_message VARCHAR(1000),
            PRIMARY KEY (event_id)
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