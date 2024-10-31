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
 * Handles the databse table to store the link between atendees and their related attendees.
 */
class RelatedAttendeesTable implements ITable {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new RelatedAttendeesTable();
        }

        return self::$instance;
    }

    const TABLE_NAME = ITable::PREFIX . "related_attendees";

    private $table_name;
    private $charset_collate;

    private function __construct() {
        global $wpdb;
        $this->table_name = self::TABLE_NAME;
        $this->charset_collate = $wpdb->get_charset_collate();
    }
    
    public function create() {    
        $sql = "CREATE TABLE {$this->table_name} (
            main_attendee_id BIGINT(20) UNSIGNED NOT NULL,
            related_attendee_id BIGINT(20) UNSIGNED NOT NULL,
            PRIMARY KEY (main_attendee_id, related_attendee_id)
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