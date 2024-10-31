<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../database/handlers/events_handler.php';

/**
 * Handles the create end update actions of an event. 
 */ 
function handle_save_event_submission() {
    if (!isset($_POST['save_event_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['save_event_nonce'])), 'save_event')) {
        return;
    }
    $action = isset($_POST['action']) ? sanitize_text_field(wp_unslash($_POST['action'])) : '';
    $is_action_save_event = $action === 'save_event';
    $event_name = isset($_POST['event_name']) ? sanitize_text_field(wp_unslash($_POST['event_name'])) : '';
    $is_event_name_set = !empty($event_name);
    
    // Check if it is create event action.
    if ($is_action_save_event && $is_event_name_set) {
        $rsvp_open = isset($_POST['rsvp_open_date_time']) ? sanitize_text_field(wp_unslash($_POST['rsvp_open_date_time'])) : 0;
        $rsvp_close = isset($_POST['rsvp_close_date_time']) ? sanitize_text_field(wp_unslash($_POST['rsvp_close_date_time'])) : 0;

        EventsHandler::get_instance()->save_event($event_name, '', $rsvp_open, $rsvp_close, 0, 0, '');
        
        $tab_event_details_url = wp_nonce_url(
            add_query_arg(
                array(
                    'page'  => 'manage-event',
                    'tab'   => 'tab_event_details'
                ), admin_url('admin.php')
            ), 
            'tab_event_details', 
            'tab_event_details_nonce'
        );
        wp_redirect($tab_event_details_url);
    }
}

add_action('admin_post_save_event', __NAMESPACE__ . '\\handle_save_event_submission');
?>