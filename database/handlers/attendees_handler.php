<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../tables/attendees.php';
require_once plugin_dir_path(__FILE__) . '../tables/events_attendees.php';

class AttendeesHandler {

    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new AttendeesHandler();
        }

        return self::$instance;
    }

    private function __construct() {
    }

    function get_attendees($event_id, $except_attendee_ids = null) {
        global $wpdb;
        $attendees_table_name = AttendeesTable::TABLE_NAME;
        $events_attendees_table_name = EventsAttendeesTable::TABLE_NAME;
        $except_query = "";
        // The except attendees ids are taken directly from the dabase, so are trusted.
        if (!empty($except_attendee_ids)) {
            $except_query = " AND id NOT IN (" . implode(', ', $except_attendee_ids) . ")";
        }
        $query = $wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- The table names are constants and trusted.
            "SELECT * FROM {$attendees_table_name} WHERE id IN (SELECT attendee_id FROM $events_attendees_table_name WHERE event_id = %d)" . $except_query,
            $event_id
        );
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The query is already safely prepared. No caching is used for now.
        return $wpdb->get_results($query);
    }

    function get_attendee($attendee_id) {
        global $wpdb;
        $table_name = AttendeesTable::TABLE_NAME;
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- The table name is a constant and trusted. The attendee_id is validated before calling the function.
        $query = $wpdb->prepare("SELECT * FROM {$table_name} where id = %d", $attendee_id);
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The query is already safely prepared. No caching is used for now.
        return $wpdb->get_row($query);
    }

    function save_attendee($id, $first_name, $last_name, $email, $is_main_attendee) {
        global $wpdb;
        $is_edit = $id != null && $id > 0;
        $attendee_data = array(
            'first_name'        => $first_name,
            'last_name'         => $last_name,
            'email'             => $email,
            'is_main_attendee'  => $is_main_attendee
        );

        $table_name = AttendeesTable::TABLE_NAME;
        if ($is_edit) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- No caching is used for now.
            $wpdb->update($table_name, $attendee_data, array('id' => $id));
        } else {
             // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Query is safe here.
            $result = $wpdb->insert($table_name, $attendee_data);
            if ($result !== false) {
                $id = $wpdb->insert_id;
            }
        }
        return $id;
    }

    function delete_attendee($attendee_id) {
        global $wpdb;
         // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Query is safe here. No caching is used for now.
        $wpdb->delete(AttendeesTable::TABLE_NAME, array('id' => $attendee_id), '%d');
    }

    function search($first_name, $last_name) {
        global $wpdb;
        $table_name = AttendeesTable::TABLE_NAME;
        $first_name_like = "%" . $wpdb->esc_like($first_name) . "%";
        $last_name_like = "%" . $wpdb->esc_like($last_name) . "%";
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- The table name is a constant and trusted. The first name and last name are validated before calling this method.
        $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE first_name LIKE %s AND last_name LIKE %s", $first_name_like, $last_name_like);
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The query is already safely prepared, and the first name and last name are validated before calling this method. No caching is used for now.
        $result = $wpdb->get_results($query);
        return $result;
    }
}

?>