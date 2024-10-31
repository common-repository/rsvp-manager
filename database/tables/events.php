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
 * Handles the databse table to store Events.
 */
class EventsTable implements ITable {

    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new EventsTable();
        }

        return self::$instance;
    }

    const TABLE_NAME = ITable::PREFIX . "events";

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
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            rsvp_open DATETIME NOT NULL,
            rsvp_close DATETIME NOT NULL,
            start DATETIME NOT NULL,
            end DATETIME NOT NULL,
            location TEXT NOT NULL,
            PRIMARY KEY (id)
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