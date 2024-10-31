<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../tables/events_attendees.php';

class EventsAttendeesHandler {

    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new EventsAttendeesHandler();
        }

        return self::$instance;
    }

    private function __construct() {
    }

    function get_attendee_to_event($attendee_id, $event_id) {
        global $wpdb;
        $table_name = EventsAttendeesTable::TABLE_NAME;
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- The table name is a constant and trusted. The event_id and attendee_id are validated before calling the function.
        $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE event_id = %d AND attendee_id = %d", $event_id, $attendee_id);
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The table name is a constant and trusted variable. The attendee_id and event_id are validated before calling the function. No caching is used for now.
        return $wpdb->get_row($query);
    }

    function save_attendee_to_event($attendee_id, $event_id) {
        global $wpdb;
        $link = $this->get_attendee_to_event($attendee_id, $event_id);
        if ($link === null) {
            $data = array(
                'attendee_id'   => $attendee_id,
                'event_id'      => $event_id
            );
            $table_name = EventsAttendeesTable::TABLE_NAME;
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- The direct query is safe to use here.
            $result = $wpdb->insert($table_name, $data);
            return $result !== false;
        }
        return false;
    }

    function delete_links_for_attendee($attendee_id) {
        global $wpdb;
        $table_name = EventsAttendeesTable::TABLE_NAME;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- No caching is used for now.
        $wpdb->delete($table_name, array('attendee_id' => $attendee_id), '%d');
    }

    function get_all() {
        global $wpdb;
        $table_name = EventsAttendeesTable::TABLE_NAME;
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The table name is a constant and trusted. No caching is used for now.
        return $wpdb->get_results("SELECT * FROM {$table_name}");
    }

}

?>