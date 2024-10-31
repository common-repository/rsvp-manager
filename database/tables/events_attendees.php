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
 * Handles the databse table to store the link between the events and their attendees.
 */
class EventsAttendeesTable implements ITable {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new EventsAttendeesTable();
        }

        return self::$instance;
    }

    const TABLE_NAME = ITable::PREFIX . "events_attendees";

    private $table_name;
    private $charset_collate;

    private function __construct() {
        global $wpdb;
        $this->table_name = self::TABLE_NAME;
        $this->charset_collate = $wpdb->get_charset_collate();
    }

    public function create() {    
        $sql = "CREATE TABLE {$this->table_name} (
            event_id BIGINT(20) UNSIGNED NOT NULL,
            attendee_id BIGINT(20) UNSIGNED NOT NULL,
            PRIMARY KEY (event_id, attendee_id)
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