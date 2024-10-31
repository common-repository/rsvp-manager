<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../tables/texts.php';

function text($key, $default_value) {
    return TextsHandler::get_instance()->get($key, $default_value);
}

function show_text($key, $default_value) {
    echo wp_kses_post(text($key, $default_value));
}

class TextsHandler {

    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new TextsHandler();
        }

        return self::$instance;
    }

    private function __construct() {
    }

    const EVENT_ID = "event_id";
    const RSVP_IS_NOT_OPEN_MESSAGE = "rsvp_is_not_open_message";
    const RSVP_IS_CLOSED_MESSAGE = "rsvp_is_closed_message";
    const SEARCH_HEAD_MESSAGE = "search_head_message";
    const FIRST_NAME_LABEL = "first_name_label";
    const LAST_NAME_LABEL = "last_name_label";
    const SEARCH_BUTTON_LABEL = "search_button_label";
    const ERROR_EMPTY_FIRST_OR_LAST_NAME = "error_empty_first_or_last_name";
    const ERROR_NO_ATTENDEES_FOUND = "error_no_attendees_found";
    const RSVP_BUTTON_LABEL = "rsvp_button_label";
    const RSVP_ALREADY_ANSWER_LABEL = "rsvp_already_answer_label";
    const RSVP_ALREADY_ANSWER_YES_BUTTON_LABEL = "rsvp_already_answer_yes_button_label";
    const RSVP_ALREADY_ANSWER_NO_BUTTON_LABEL = "rsvp_already_answer_no_button_label";
    const RSVP_WELCOME_LABEL = "rsvp_welcome_label";
    const RSVP_QUESTION = "rsvp_question";
    const RSVP_ANSWER_YES = "rsvp_answer_yes";
    const RSVP_ANSWER_NO = "rsvp_answer_no";
    const RSVP_MESSAGE_TITLE = "rsvp_message_title";
    const RSVP_RELATED_ATTENDEES_INFO_MESSAGE = "rsvp_related_attendees_info_message";
    const RSVP_RELATED_ATTENDEES_RSVP_QUESTION = "rsvp_relates_attendees_rsvp_question";
    const RSVP_CONFIRM_BUTTON_LABEL = "rsvp_confirm_button_label";
    const RSVP_YES_MESSAGE = "rsvp_yes_message";
    const RSVP_NO_MESSAGE = "rsvp_no_message";

    private $texts_data_cache = [];

    function get($key, $default_value) {
        if (!empty($key)) {
            $event_id = EventsHandler::get_instance()->get_event_id();
            $this->invalidate_texts($event_id);
            $event_texts = $this->texts_data_cache[$event_id];
            if ($event_texts == null) {
                $this->invalidate_texts($event_id);
                $event_texts = $this->texts_data_cache[$event_id];
            }
            if ($event_texts != null) {
                $value_from_settings = $event_texts[$key];
                if (!empty($value_from_settings)) {
                    return $value_from_settings;   
                }
            }
        }
        return $default_value;
    }

    function save(
        $event_id,
        $rsvp_is_not_open_message,
        $rsvp_is_closed_message,
        $search_head_message,
        $first_name_label,
        $last_name_label,
        $search_button_label,
        $error_empty_first_or_last_name,
        $error_no_attendees_found,
        $rsvp_button_label,
        $rsvp_already_answer_label,
        $rsvp_already_answer_yes_button_label,
        $rsvp_already_answer_no_button_label,
        $rsvp_welcome_label,
        $rsvp_question,
        $rsvp_answer_yes,
        $rsvp_answer_no,
        $rsvp_message_title,
        $rsvp_related_attendees_info_message,
        $rsvp_relates_attendees_rsvp_question,
        $rsvp_confirm_button_label,
        $rsvp_yes_message,
        $rsvp_no_message
    ) {
        global $wpdb;
        $table_name = TextsTable::TABLE_NAME;
        $texts_data = array(
            self::EVENT_ID                                => $event_id,
            self::RSVP_IS_NOT_OPEN_MESSAGE                => $rsvp_is_not_open_message,
            self::RSVP_IS_CLOSED_MESSAGE                  => $rsvp_is_closed_message,
            self::SEARCH_HEAD_MESSAGE                     => $search_head_message,
            self::FIRST_NAME_LABEL                        => $first_name_label,
            self::LAST_NAME_LABEL                         => $last_name_label,
            self::SEARCH_BUTTON_LABEL                     => $search_button_label,
            self::ERROR_EMPTY_FIRST_OR_LAST_NAME          => $error_empty_first_or_last_name,
            self::ERROR_NO_ATTENDEES_FOUND                => $error_no_attendees_found,
            self::RSVP_BUTTON_LABEL                       => $rsvp_button_label,
            self::RSVP_ALREADY_ANSWER_LABEL               => $rsvp_already_answer_label,
            self::RSVP_ALREADY_ANSWER_YES_BUTTON_LABEL    => $rsvp_already_answer_yes_button_label,
            self::RSVP_ALREADY_ANSWER_NO_BUTTON_LABEL     => $rsvp_already_answer_no_button_label,
            self::RSVP_WELCOME_LABEL                      => $rsvp_welcome_label,
            self::RSVP_QUESTION                           => $rsvp_question,
            self::RSVP_ANSWER_YES                         => $rsvp_answer_yes,
            self::RSVP_ANSWER_NO                          => $rsvp_answer_no,
            self::RSVP_MESSAGE_TITLE                      => $rsvp_message_title,
            self::RSVP_RELATED_ATTENDEES_INFO_MESSAGE     => $rsvp_related_attendees_info_message,
            self::RSVP_RELATED_ATTENDEES_RSVP_QUESTION    => $rsvp_relates_attendees_rsvp_question,
            self::RSVP_CONFIRM_BUTTON_LABEL               => $rsvp_confirm_button_label,
            self::RSVP_YES_MESSAGE                        => $rsvp_yes_message,
            self::RSVP_NO_MESSAGE                         => $rsvp_no_message
        );
        $format = array(
            '%d',
            '%s', 
            '%s',
            '%s',
            '%s',
            '%s', 
            '%s',
            '%s',
            '%s',
            '%s', 
            '%s', 
            '%s',
            '%s',
            '%s', 
            '%s', 
            '%s',
            '%s', 
            '%s',
            '%s', 
            '%s',
            '%s', 
            '%s',
            '%s'
        );

        $already_saved_texts = $this->get_texts($event_id);
        if ($already_saved_texts !== null) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Query is safe here. No caching is used for now.
            $result = $wpdb->update($table_name, $texts_data, array(self::EVENT_ID => $event_id));
        } else {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Query is safe here. No caching is used for now.
            $result = $wpdb->insert($table_name, $texts_data, $format);
        }
        $this->invalidate_texts($event_id);
    }

    function get_texts($event_id) {
        global $wpdb;
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The table name and column name are constants and trusted. No caching is used for now.
        $query = $wpdb->prepare("SELECT * FROM " . TextsTable::TABLE_NAME . " where " . self::EVENT_ID . " = %d", $event_id);
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- The query is already safely prepared. No caching is used for now.
        return $wpdb->get_row($query, ARRAY_A);
    }

    private function invalidate_texts($event_id) {
        $this->texts_data_cache[$event_id] = $this->get_texts($event_id);
    }
}

?>