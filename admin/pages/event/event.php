<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../../database/handlers/events_handler.php';
require_once plugin_dir_path(__FILE__) . '../../../database/tables/texts.php';
require_once plugin_dir_path(__FILE__) . '../../../database/handlers/events_handler.php';

function display_manage_event_page() {
    ?>
    <div class="wrap">
        <?php
            $event = EventsHandler::get_instance()->get_event();
            
            // If there is an id provided but there is no event for it, show a message an return.
            if (!$event) {
            ?>
                <div class="wrap">
                    <h1>
                        <?php 
                        echo wp_kses_post(__('Invalid Event', 'rsvp-manager'));
                        return;
                        ?>
                    </h1>
                </div>';
            <?php
            }

            $event_name = $event->name;
            $rsvp_open_date_time = $event->rsvp_open;
            $rsvp_close_date_time = $event->rsvp_close;
            $event_start_date_time = $event->start;
            $event_end_date_time = $event->end;
            $event_location = $event->location;
            $event_description = $event->description;
        ?>
        
        <?php echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">' ?>
            <?php wp_nonce_field('save_event', 'save_event_nonce'); ?>
            <input type="hidden" name="action" value="save_event">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="event_name"><?php esc_html_e('Event Name:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="event_name" name="event_name" value="<?php echo wp_kses_post($event_name); ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_open_date_time"><?php esc_html_e('RSVP Open Date:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="datetime-local" id="rsvp_open_date_time" name="rsvp_open_date_time" value="<?php echo wp_kses_post($rsvp_open_date_time); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_close_date_time"><?php esc_html_e('RSVP Close Date:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="datetime-local" id="rsvp_close_date_time" name="rsvp_close_date_time" value="<?php echo wp_kses_post($rsvp_close_date_time); ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button(wp_kses_post(__('Update Event', 'rsvp-manager'))); ?>
        <?php echo '</form>' ?>
    </div>
    <?php
}

?>