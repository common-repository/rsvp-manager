<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../database/handlers/events_handler.php';
require_once plugin_dir_path(__FILE__) . '../../database/handlers/attendees_handler.php';
require_once plugin_dir_path(__FILE__) . '../../database/handlers/answers_handler.php';
require_once plugin_dir_path(__FILE__) . '../../database/handlers/events_attendees_handler.php';
require_once plugin_dir_path(__FILE__) . '../../database/handlers/related_attendees_handler.php';

/**
 * Handles the create end update actions of an attendee. 
 */ 
function handle_save_attendee_submission() {
    if (!isset($_POST['save_attendee_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['save_attendee_nonce'])), 'save_attendee')) {
        return;
    }

    $action = isset($_POST['action']) ? sanitize_text_field(wp_unslash($_POST['action'])) : '';
    $is_action_save_attendee = $action === 'save_attendee';
    $event_id = isset($_POST['event_id']) ? intval(sanitize_text_field(wp_unslash($_POST['event_id']))) : 0;
    $event = $event_id > 0 ? EventsHandler::get_instance()->get_event($event_id) : null;
    $attendee_id = isset($_POST['attendee_id']) ? intval(sanitize_text_field(wp_unslash($_POST['attendee_id']))) : 0;

    // Check if it is create attendee action.
    if ($is_action_save_attendee && $event != null) {
        // Create new post (attendee)
        $first_name = isset($_POST['first_name']) ? sanitize_text_field(wp_unslash($_POST['first_name'])) : '';
        $last_name = isset($_POST['last_name']) ? sanitize_text_field(wp_unslash($_POST['last_name'])) : '';
        $email = isset($_POST['email']) ? sanitize_text_field(wp_unslash($_POST['email'])) : '';
        $is_main_attendee = isset($_POST['is_main_attendee']) ? sanitize_text_field(wp_unslash($_POST['is_main_attendee'])) : 0;

        $attendee_id = AttendeesHandler::get_instance()->save_attendee($attendee_id, $first_name, $last_name, $email, $is_main_attendee);
        EventsAttendeesHandler::get_instance()->save_attendee_to_event($attendee_id, $event_id);

        $status = isset($_POST['rsvp_status']) ? sanitize_text_field(wp_unslash($_POST['rsvp_status'])) : 'no_response';
        $date = isset($_POST['rsvp_date']) ? sanitize_text_field(wp_unslash($_POST['rsvp_date'])) : 0;
        $message = isset($_POST['message']) ? sanitize_text_field(wp_unslash($_POST['message'])) : '';

        AnswersHandler::get_instance()->save_answer($event_id, $attendee_id, $status, $date, $message);

        // update the related attendees if there were changes
        $is_mutual_association = isset($_POST['mutual_association']);
        $related_attendee_ids = isset($_POST['related_attendee_ids']) ? sanitize_text_field(wp_unslash($_POST['related_attendee_ids'])) : null;
        if ($related_attendee_ids !== null) {
            $related_attendees_array = explode(',', $related_attendee_ids);
            if ($related_attendees_array !== null) {
                $ids = array_map('intval', $related_attendees_array);
                if ($ids !== null) {
                    RelatedAttendeesHandler::get_instance()->save_related_attendees($attendee_id, $ids, $is_mutual_association);
                }
            }
        }

        // Redirect the user to the list of attendees.        
        $attendees_url = html_entity_decode(wp_nonce_url(
            add_query_arg(
                array(
                    'page'      => 'events',
                    'action'    => 'attendees',
                ), admin_url('admin.php')
            ), 
            'attendees', 
            'attendees_nonce'
        ));
        wp_redirect($attendees_url);
    }
}

add_action('admin_post_save_attendee', __NAMESPACE__ . '\\handle_save_attendee_submission');

?>