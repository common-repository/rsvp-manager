<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../tables/related_attendees.php';
require_once plugin_dir_path(__FILE__) . '../tables/attendees.php';

class RelatedAttendeesHandler {

    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new RelatedAttendeesHandler();
        }

        return self::$instance;
    }

    private function __construct() {
    }

    function save_related_attendees($main_attendee_id, $related_attendee_ids, $is_mutual_association) {
        // First we remove the already related attendees for the main attendee.
        $this->delete_related_attendees($main_attendee_id);
        
        // After removing we update the related attendees with the new ones.
        if (!empty($related_attendee_ids)) {
            global $wpdb;
            $values = [];
            foreach($related_attendee_ids as $related_attendee_id) {
                $values[] = $wpdb->prepare("(%d, %d)", $main_attendee_id, $related_attendee_id); 
            }
            $table_name = RelatedAttendeesTable::TABLE_NAME;
            $query = "INSERT INTO {$table_name} (main_attendee_id, related_attendee_id) VALUES " . implode(', ', $values);
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The query is already safely prepared. No chaching is used for now.
            $wpdb->query($query);
        }

        if ($is_mutual_association) {
            $this->save_mutual_associations($main_attendee_id, $related_attendee_ids);
        }
    }

    /**
     * For the related attendees of the main attendee, saves the main attendee as related attendee.
     */
    private function save_mutual_associations($main_attendee_id, $related_attendee_ids) {
        global $wpdb;
        $table_name = RelatedAttendeesTable::TABLE_NAME;
        foreach ($related_attendee_ids as $related_attendee_id) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery -- Caching is not used. We have a custom db table here so we have to use a direct query.
            $wpdb->query($wpdb->prepare(
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- The query is already safely prepared.
                "INSERT IGNORE INTO {$table_name} (main_attendee_id, related_attendee_id) VALUES (%d, %d)", 
                $related_attendee_id,
                $main_attendee_id
            ));
        }
    }

    private function delete_related_attendees($main_attendee_id) {
        global $wpdb;
        $table_name = RelatedAttendeesTable::TABLE_NAME;
        $where = array( 'main_attendee_id' => $main_attendee_id );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery -- Caching is not used. We have a custom db table here so we have to use a direct query.
        return $wpdb->delete($table_name, $where, array('%d'));
    }

    function get_related_attendees($attendee_id) {
        global $wpdb;
        $related_attendees_table_name = RelatedAttendeesTable::TABLE_NAME;
        $attendees_table_name = AttendeesTable::TABLE_NAME;
        $query = $wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- The table names are constants and trusted.
            "SELECT * FROM {$attendees_table_name} WHERE id IN (SELECT related_attendee_id FROM {$related_attendees_table_name} WHERE main_attendee_id = %d)",
            $attendee_id
        );
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The query is already safely prepared. No caching is used for now.
        return $wpdb->get_results($query);
    }
}

?>