<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../database/handlers/events_handler.php';
require_once plugin_dir_path(__FILE__) . 'attendees.php';

/**
 * Displays the events page based on the given action.
 */
function display_manage_events_page() {
    // Check if the list of attendees should be shown.
    if ((isset($_GET['attendees_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['attendees_nonce'])), 'attendees'))) {
        $event = EventsHandler::get_instance()->get_event();
        if ($event != null) {
            display_manage_attendees_page($event);
            return;
        }
    } else if (isset($_GET['manage_attendee_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['manage_attendee_nonce'])), 'manage_attendee')) {
        $event = EventsHandler::get_instance()->get_event();
        if ($event != null) {
            display_manage_attendee_page($event);
            return;
        }
    }
    
    display_events_page();
}

/**
 * Displays the list of events.
 */
function display_events_page() {
    ?>
    <div class="wrap">
        <h1><?php echo wp_kses_post(__('Event', 'rsvp-manager')); ?></h1>
        <br />
        
        <!-- Add Event Button -->
        <a href="<?php echo esc_url(admin_url('admin.php?page=manage-event')); ?>" class="button button-secondary"><?php esc_html_e('Update Event', 'rsvp-manager'); ?></a>
        
        <?php 
        $event = EventsHandler::get_instance()->get_event();
        ?>
        <br /><br />
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col">
                        <?php esc_html_e('Event Name', 'rsvp-manager'); ?>
                    </th>
                    <th scope="col">
                        <?php esc_html_e('Shortcode', 'rsvp-manager'); ?>
                    </th>
                    <th scope="col">
                        <?php esc_html_e('Attendees', 'rsvp-manager'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?php
                        $manage_event_url = wp_nonce_url(
                            add_query_arg(
                                array(
                                    'page'      => 'manage-event'
                                ), admin_url('admin.php')
                            ), 
                            'manage-event', 
                            'manage-event_nonce'
                        );
                        ?>
                        <b><a href="<?php echo esc_url($manage_event_url); ?>"><?php echo wp_kses_post($event->name); ?></a></b>
                        <div>
                            <a href="<?php echo esc_url($manage_event_url); ?>"><?php echo wp_kses_post(__('Edit', 'rsvp-manager')); ?></a>
                        </div>
                    </td>
                    <td><?php echo '[event_rsvp]' ?></td>
                    <td>
                        <?php
                        $attendees_url = wp_nonce_url(
                            add_query_arg(
                                array(
                                    'page'      => 'events',
                                    'action'    => 'attendees'
                                ), admin_url('admin.php')
                            ), 
                            'attendees', 
                            'attendees_nonce'
                        );
                        ?>
                        <a href="<?php echo esc_url($attendees_url); ?>"><?php esc_html_e('Manage Attendees', 'rsvp-manager'); ?></a>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="col">
                        <?php esc_html_e('Event Name', 'rsvp-manager'); ?>
                    </th>
                    <th scope="col">
                        <?php esc_html_e('Shortcode', 'rsvp-manager'); ?>
                    </th>
                    <th scope="col">
                        <?php esc_html_e('Attendees', 'rsvp-manager'); ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php
}

?>