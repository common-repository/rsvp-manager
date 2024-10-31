<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../../database/handlers/attendees_handler.php';
require_once plugin_dir_path(__FILE__) . '../../../database/handlers/answers_handler.php';
require_once plugin_dir_path(__FILE__) . '../../../database/handlers/related_attendees_handler.php';

/**
 * Display the attendee page. It handles both, the creation of a new attendee and edition.
 * If an attendee id is provided, then it will fill in the fields with the values of the already saved attendee and updates the ui for edition.
 * Otherwise, a new attendee is created.
 */
function display_manage_attendee_page($event) {    
    ?>
    <div class="wrap">
        <?php
            if (!isset($_GET['manage_attendee_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['manage_attendee_nonce'])), 'manage_attendee')) {
                ?>
                <div class="wrap">
                    <h1>
                        <?php 
                        echo wp_kses_post(__('Access denied', 'rsvp-manager'));
                        return;
                        ?>
                    </h1>
                </div>';
                <?php
            }

            // Check if there is an attendee id sent as param so we popupate the fields.
            $is_edit = isset($_GET['attendee_id']) && is_numeric($_GET['attendee_id']);
            $attendee_id = $is_edit ? intval($_GET['attendee_id']) : 0;
            $is_edit = $attendee_id > 0;
            $attendee = $is_edit ? AttendeesHandler::get_instance()->get_attendee($attendee_id) : null;
            
            // If there is an id provided but there is no attendee for it, show a message an return.
            if ($is_edit && !$attendee) {
            ?>
                <div class="wrap">
                    <h1>
                        <?php 
                        echo wp_kses_post(__('Invalid Attendee', 'rsvp-manager'));
                        return;
                        ?>
                    </h1>
                </div>';
            <?php
            }
            
            $first_name = $is_edit ? $attendee->first_name : '';
            $last_name = $is_edit ? $attendee->last_name : '';
            $email = $is_edit ? $attendee->email : '';
            $is_main_attendee = $is_edit ? $attendee->is_main_attendee : '';

            $answer = AnswersHandler::get_instance()->get_answer($event->id, $attendee_id);

            $rsvp_status = $is_edit && $answer != null ? $answer->status : '';
            $rsvp_date = $is_edit && $answer != null ? $answer->date : '';
            $message = $is_edit && $answer != null ? $answer->message : '';
            
            $associated_attendees = $is_edit ? get_post_meta($attendee_id, '_associated_attendees', true) : '';
        ?>
        
        <h1>
            <?php
                if($attendee != null) {
                    echo wp_kses_post(__('Update Attendee', 'rsvp-manager')); 
                } else {
                    echo wp_kses_post(__('Add Attendee', 'rsvp-manager')); 
                }
            ?>
        </h1>
        
        <?php echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">' ?>
            <?php wp_nonce_field('save_attendee', 'save_attendee_nonce'); ?>
            <input type="hidden" name="action" value="save_attendee">
            <input type="hidden" name="event_id" value="<?php echo intval($event->id); ?>">
            <input type="hidden" name="attendee_id" value="<?php echo intval($attendee_id); ?>">
            <input type="hidden" name="related_attendee_ids" id="related_attendee_ids" value="">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="first_name"><?php esc_html_e('First Name:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="first_name" name="first_name" value="<?php echo wp_kses_post($first_name); ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="last_name"><?php esc_html_e('Last Name:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="last_name" name="last_name" value="<?php echo wp_kses_post($last_name); ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_status"><?php esc_html_e('RSVP Status:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <select id="rsvp_status" name="rsvp_status">
                                <?php echo '<option value="no_response"' . (strcmp($rsvp_status, "no_response") === 0 ? 'selected' : '') . '>' . wp_kses_post(__('No Response', 'rsvp-manager')) . '</option>' ?>
                                <?php echo '<option value="yes"' . (strcmp($rsvp_status, "yes") === 0 ? 'selected' : '') . '>' . wp_kses_post(__('Yes', 'rsvp-manager')) . '</option>' ?>
                                <?php echo '<option value="no"' . (strcmp($rsvp_status, "no") === 0 ? 'selected' : '') . '>' . wp_kses_post(__('No', 'rsvp-manager')) . '</option>' ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_date"><?php esc_html_e('RSVP Date:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="datetime-local" id="rsvp_date" name="rsvp_date" value="<?php echo wp_kses_post($rsvp_date); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="message"><?php esc_html_e('Message:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <textarea rows="10" id="message" name="message"><?php echo wp_kses_post($message) ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e('Associated Attendees:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <?php display_related_attendees($event->id, $attendee_id, $is_edit); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <?php submit_button($is_edit ? __('Update Attendee', 'rsvp-manager') : __('Create Attendee', 'rsvp-manager')); ?>
        <?php echo '</form>' ?>
    </div>
    <?php
}

function display_related_attendees($event_id, $attendee_id, $is_edit) {
    $related_attendees = RelatedAttendeesHandler::get_instance()->get_related_attendees($attendee_id);
    $except_attendee_ids = array_map(function($attendee) {
        return $attendee->id;
    }, $related_attendees);
    $except_attendee_ids[] = $attendee_id; 
    $attendees = AttendeesHandler::get_instance()->get_attendees($event_id, $except_attendee_ids);
    ?>
    <div class="attendees-container">
        <div class="attendees-list">
            <p><?php esc_html_e('All Attendees', 'rsvp-manager'); ?></p>
            <ul id="all-attendees">
                <?php
                foreach ($attendees as $attendee) {
                    echo '<li data-id="' . esc_attr($attendee->id) . '">' . esc_html($attendee->first_name . ' ' . $attendee->last_name) . '</li>';
                }
                ?>
            </ul>
        </div>

        <div class="attendees-buttons">
            <button id="add-related-attendee" class="btn">&gt;</button>
            <button id="remove-related-attendee" class="btn">&lt;</button>
        </div>

        <div class="related-attendees-list">
            <p><?php esc_html_e('Related Attendees', 'rsvp-manager'); ?></p>
            <ul id="related-attendees">
                <?php
                foreach ($related_attendees as $attendee) {
                    echo '<li data-id="' . esc_attr($attendee->id) . '">' . esc_html($attendee->first_name . ' ' . $attendee->last_name) . '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="mutual_association_container">
        <input type="checkbox" id="mutual_association" name="mutual_association"<?php if(!$is_edit) echo 'checked'; ?>>
        <label for="mutual_association"><b><?php esc_html_e('Automatically create a mutual association between attendees. The related attendees set here will have this attendee as related attendee.', 'rsvp-manager'); ?><b></label>
    </div>
    <?php
}

?>