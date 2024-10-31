<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../tables/events.php';

class EventsHandler {

    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new EventsHandler();
        }

        return self::$instance;
    }

    private $event_id = 1;

    private function __construct() {
    }

    function get_event_id() {
        return $this->event_id;
    }

    function get_event() {
        global $wpdb;
        $table_name = EventsTable::TABLE_NAME;
        $query = "SELECT * FROM {$table_name} ORDER BY id limit 1";
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The table name is a constant and trusted variable. No caching is used for now.
        $event = $wpdb->get_row($query);
        if ($event == null) {
            $this->add_default_event();
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The table name is a constant and trusted variable. No caching is used for now.
            $event = $wpdb->get_row($query);
        }
        $this->event_id = $event->id;
        return $event;        
    }

    private function add_default_event() {
        $date = new \DateTime();
        $rsvp_open = $date->format('Y-m-d H:i:s');
        $date->modify('+1 week');
        $rsvp_close = $date->format('Y-m-d H:i:s');
        $date->modify('+2 weeks');
        $start = $date->format('Y-m-d H:i:s');
        $date->modify('+1 week +1 day');
        $end = $date->format('Y-m-d H:i:s');
        $this->event_id = 1;
        global $wpdb;
        $event_data = array(
            'id'            => $this->event_id,
            'name'          => 'My Private Event Name',
            'description'   => '',
            'rsvp_open'     => $rsvp_open,
            'rsvp_close'    => $rsvp_close,
            'start'         => $start,
            'end'           => $end,
            'location'      => ''
        );
        $table_name = EventsTable::TABLE_NAME;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- No caching is used for now.
        $result = $wpdb->insert($table_name, $event_data);
        if ($result !== false) {
            $this->event_id = $wpdb->insert_id;
        }
    }

    function save_event($name, $description, $rsvp_open, $rsvp_close, $start, $end, $location) {
        global $wpdb;
        $event_data = array(
            'name'          => $name,
            'description'   => $description,
            'rsvp_open'     => $rsvp_open,
            'rsvp_close'    => $rsvp_close,
            'start'         => $start,
            'end'           => $end,
            'location'      => $location
        );
        
        $table_name = EventsTable::TABLE_NAME;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- No caching is used for now.
        $wpdb->update($table_name, $event_data, array('id' => $this->event_id));
        return $this->event_id;
    }
}

?>