<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../database/handlers/attendees_handler.php';
require_once plugin_dir_path(__FILE__) . '../../database/handlers/answers_handler.php';
require_once plugin_dir_path(__FILE__) . '../../database/handlers/events_attendees_handler.php';

/**
 * Handles the bulk deletion of attendees.
 */ 
function handle_bulk_delete_attendees_actions() {
    if (!isset($_POST['bulk_delete_attendees_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bulk_delete_attendees_nonce'])), 'bulk_delete_attendees')) {
        return;
    }
    
    if (isset($_POST['action_selector_header']) && $_POST['action_selector_header'] === 'delete' || isset($_POST['action_selector_footer']) && $_POST['action_selector_footer'] === 'delete') {
        if (isset($_POST['attendee_ids']) && is_array($_POST['attendee_ids'])) {
            $attendees_ids = array_map('intval', $_POST['attendee_ids']);

            // Delete each selected attendee.
            foreach ($attendees_ids as $attendee_id) {
                delete_attendee($attendee_id);
            }
        }
    }
    
    redirect_to_attendees();
}

add_action('admin_post_bulk_delete_attendees', __NAMESPACE__ . '\\handle_bulk_delete_attendees_actions');

/**
 * Handles the delete action of an attendee.
 */
function handle_delete_attendee() {
    if (!isset($_GET['delete_attendee_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['delete_attendee_nonce'])), 'delete_attendee')) {
        return;
    }

    $attendee_id = isset($_GET['attendee_id']) ? intval($_GET['attendee_id']) : 0;
    if ($attendee_id > 0) {
        delete_attendee($attendee_id);
        redirect_to_attendees();
    }
}

function redirect_to_attendees() {
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

/**
 * Delete the attendee and the associated anwers for the given attendee and event.
 */
function delete_attendee($attendee_id) {
    // Delete all related RSVPs for the given attendee and event.
    AnswersHandler::get_instance()->delete_answers_for_attendee($attendee_id);
    // Delete link with events.
    EventsAttendeesHandler::get_instance()->delete_links_for_attendee($attendee_id);
    // Delete the attendee.
    AttendeesHandler::get_instance()->delete_attendee($attendee_id);
}

/**
 * Delete the attendees and its related objects for the given event.
 */
function delete_attendees($event_id) {
    $attendees = AttendeesHandler::get_instance()->get_attendees($event_id);
    if (!empty($attendees)) {
        foreach ($attendees as $attendee) {
            delete_attendee($attendee->id);
        }
    }
}

add_action('admin_post_delete-attendee', __NAMESPACE__ . '\\handle_delete_attendee');
?>