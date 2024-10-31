<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../tables/answers.php';

class AnswersHandler {

    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new AnswersHandler();
        }

        return self::$instance;
    }

    private function __construct() {
    }

    function get_answer($event_id, $attendee_id) {
        global $wpdb;
        $table_name = AnswersTable::TABLE_NAME;
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- The table name is a constants and trusted. The event id and attendee id are validated before calling the function.
        $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE event_id = %d AND attendee_id = %d", $event_id, $attendee_id);
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The query is already safely prepared. No caching is used for now.
        return $wpdb->get_row($query);
    }

    function save_answer($event_id, $attendee_id, $status, $date, $message = null) {
        global $wpdb;
        $is_edit = $this->get_answer($event_id, $attendee_id) != null;
        $answer_data = array(
            'status'        => $status,
            'date'          => $date,
        );
        if ($message !== null) {
            $answer_data['message'] = $message;
        }
        $table_name = AnswersTable::TABLE_NAME;
        $success = true;
        if ($is_edit) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- No caching is used for now.
            $wpdb->update($table_name, $answer_data, array('event_id' => $event_id, 'attendee_id' => $attendee_id));
        } else {
            $answer_data['event_id'] = $event_id;
            $answer_data['attendee_id'] = $attendee_id;
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- No caching is used for now.
            $result = $wpdb->insert($table_name, $answer_data);
            if ($result === false) {
                $success = false;
            }
        }
        return $success;
    }

    function delete_answers_for_attendee($attendee_id) {
        global $wpdb;
        $table_name = AnswersTable::TABLE_NAME;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- No caching is used for now.
        $wpdb->delete($table_name, array('attendee_id' => $attendee_id), '%d');
    }

    function get_all() {
        global $wpdb;
        $table_name = EventsAttendeesTable::TABLE_NAME;
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The table name is a constants and trusted. No caching is used for now.
        return $wpdb->get_results("SELECT * FROM {$table_name}");
    }
}

?>