<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../database/handlers/texts_handler.php';
require_once plugin_dir_path(__FILE__) . '../../database/handlers/events_handler.php';

/**
 * Handles the update action of the texts. 
 */ 
function handle_save_texts_submission() {
    if (!isset($_POST['save_texts_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['save_texts_nonce'])), 'save_texts')) {
        return;
    }
    $action = isset($_POST['action']) ? sanitize_text_field(wp_unslash($_POST['action'])) : '';
    $is_action_save_texts = $action === 'save_texts';
    
    // Check if it is create event action.
    if ($is_action_save_texts) {
        $event = EventsHandler::get_instance()->get_event();
        if ($event != null) {
            $rsvp_is_not_open_message = isset($_POST['rsvp_is_not_open_message']) ? wp_kses_post(wp_unslash($_POST['rsvp_is_not_open_message'])) : '';   
            $rsvp_is_closed_message = isset($_POST['rsvp_is_closed_message']) ? wp_kses_post(wp_unslash($_POST['rsvp_is_closed_message'])) : '';   
            $search_head_message = isset($_POST['search_head_message']) ? wp_kses_post(wp_unslash($_POST['search_head_message'])) : '';   
            $first_name_label = isset($_POST['first_name_label']) ? sanitize_text_field(wp_unslash($_POST['first_name_label'])) : '';   
            $last_name_label = isset($_POST['last_name_label']) ? sanitize_text_field(wp_unslash($_POST['last_name_label'])) : '';
            $search_button_label = isset($_POST['search_button_label']) ? sanitize_text_field(wp_unslash($_POST['search_button_label'])) : '';
            $error_empty_first_or_last_name = isset($_POST['error_empty_first_or_last_name']) ? wp_kses_post(wp_unslash($_POST['error_empty_first_or_last_name'])) : '';
            $error_no_attendees_found = isset($_POST['error_no_attendees_found']) ? wp_kses_post(wp_unslash($_POST['error_no_attendees_found'])) : '';
            $rsvp_button_label = isset($_POST['rsvp_button_label']) ? sanitize_text_field(wp_unslash($_POST['rsvp_button_label'])) : '';
            $rsvp_already_answer_label = isset($_POST['rsvp_already_answer_label']) ? wp_kses_post(wp_unslash($_POST['rsvp_already_answer_label'])) : '';
            $rsvp_already_answer_yes_button_label = isset($_POST['rsvp_already_answer_yes_button_label']) ? sanitize_text_field(wp_unslash($_POST['rsvp_already_answer_yes_button_label'])) : '';
            $rsvp_already_answer_no_button_label = isset($_POST['rsvp_already_answer_no_button_label']) ? sanitize_text_field(wp_unslash($_POST['rsvp_already_answer_no_button_label'])) : '';
            $rsvp_welcome_label = isset($_POST['rsvp_welcome_label']) ? wp_kses_post(wp_unslash($_POST['rsvp_welcome_label'])) : '';
            $rsvp_question = isset($_POST['rsvp_question']) ? wp_kses_post(wp_unslash($_POST['rsvp_question'])) : '';
            $rsvp_answer_yes = isset($_POST['rsvp_answer_yes']) ? sanitize_text_field(wp_unslash($_POST['rsvp_answer_yes'])) : '';
            $rsvp_answer_no = isset($_POST['rsvp_answer_no']) ? sanitize_text_field(wp_unslash($_POST['rsvp_answer_no'])) : '';
            $rsvp_message_title = isset($_POST['rsvp_message_title']) ? wp_kses_post(wp_unslash($_POST['rsvp_message_title'])) : '';
            $rsvp_related_attendees_info_message = isset($_POST['rsvp_related_attendees_info_message']) ? wp_kses_post(wp_unslash($_POST['rsvp_related_attendees_info_message'])) : '';
            $rsvp_relates_attendees_rsvp_question = isset($_POST['rsvp_relates_attendees_rsvp_question']) ? wp_kses_post(wp_unslash($_POST['rsvp_relates_attendees_rsvp_question'])) : '';
            $rsvp_confirm_button_label = isset($_POST['rsvp_confirm_button_label']) ? sanitize_text_field(wp_unslash($_POST['rsvp_confirm_button_label'])) : '';
            $rsvp_yes_message = isset($_POST['rsvp_yes_message']) ? wp_kses_post(wp_unslash($_POST['rsvp_yes_message'])) : '';
            $rsvp_no_message = isset($_POST['rsvp_no_message']) ? wp_kses_post(wp_unslash($_POST['rsvp_no_message'])) : '';

            TextsHandler::get_instance()->save(
                $event->id,
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
            );
            
            // Redirect the user to the texts tab.
            $tab_texts_url = html_entity_decode(wp_nonce_url(
                add_query_arg(
                    array(
                        'page'  => 'manage-event',
                        'tab'   => 'tab_texts'
                    ), admin_url('admin.php')
                ), 
                'tab_texts', 
                'tab_texts_nonce'
            ));
            wp_redirect($tab_texts_url);
        }
    }
}

add_action('admin_post_save_texts', __NAMESPACE__ . '\\handle_save_texts_submission');
?>