<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../database/handlers/events_handler.php';
require_once plugin_dir_path(__FILE__) . '../database/handlers/answers_handler.php';
require_once plugin_dir_path(__FILE__) . '../database/handlers/related_attendees_handler.php';
require_once plugin_dir_path(__FILE__) . '../database/handlers/texts_handler.php';

/**
 * Creates the shortcode for an event to display in the UI.
 */ 
function event_rsvp_shortcode($atts) {
    $event = EventsHandler::get_instance()->get_event();
    
    if ($event == null) {
        return '<p>' . __('Invalid event Id.', 'rsvp-manager') . '</p>';
    }

    if (!check_rsvp_status($event)) {
        return;
    }

    ob_start();

    if (isset($_POST['show_rsvp_form_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['show_rsvp_form_nonce'])), 'show_rsvp_form')) {
        $attendee_id = isset($_POST['attendee_id']) ? intval($_POST['attendee_id']) : 0;
        show_rsvp_form($attendee_id, $event->id, false);
    } else if (isset($_POST['modify_rsvp_yes'])) {
        $attendee_id = isset($_POST['attendee_id']) ? intval($_POST['attendee_id']) : 0;
        show_rsvp_form($attendee_id, $event->id, true);
    } else if (isset($_POST['submit_rsvp_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['submit_rsvp_nonce'])), 'submit_rsvp')) {
        submit_rsvp_form();
    } else {
        // Display the search form and search results.
        show_search_form($event);
    }

    return ob_get_clean();
}

function check_rsvp_status($event) {
    $rsvp_open_date = new \DateTime($event->rsvp_open);
    $rsvp_close_date = new \DateTime($event->rsvp_close);
    $now = new \DateTime();

    $can_rsvp = true;
    if ($now < $rsvp_open_date) {
        echo wp_kses_post(sprintf(text(TextsHandler::RSVP_IS_NOT_OPEN_MESSAGE, 'The RSVP will start on <b>%s</b>!'), $event->rsvp_open)); 
        $can_rsvp = false;
    } else if ($rsvp_close_date < $now) {
        echo wp_kses_post(sprintf(text(TextsHandler::RSVP_IS_CLOSED_MESSAGE, 'The RSVP ended on <b>%s</b>!'), $event->rsvp_close)); 
        $can_rsvp = false;
    }

    return $can_rsvp;
}

add_shortcode('event_rsvp', __NAMESPACE__ . '\\event_rsvp_shortcode');

function show_search_form($event) {
    $first_name = '';
    $last_name = '';
    $is_find_me = isset($_POST['find_me_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['find_me_nonce'])), 'find_me'); 
    if ($is_find_me) {
        $first_name = isset($_POST['first_name']) ? esc_attr(sanitize_text_field(wp_unslash($_POST['first_name']))) : '';
        $last_name = isset($_POST['last_name']) ? esc_attr(sanitize_text_field(wp_unslash($_POST['last_name']))) : '';
    }
    ?>
    <p><?php show_text(TextsHandler::SEARCH_HEAD_MESSAGE, 'To start the confirmation process, please enter your first and last name in the fields below and search.'); ?></p>

    <form method="post">
        <?php wp_nonce_field('find_me', 'find_me_nonce'); ?>
        <input type="hidden" name="event_id" value="<?php echo esc_attr($event->id); ?>" />

        <label for="first_name" class="input_label"><?php show_text(TextsHandler::FIRST_NAME_LABEL, 'First Name'); ?>:</label>
        <input type="text" name="first_name" id="first_name" value="<?php echo wp_kses_post($first_name); ?>" />

        <label for="last_name" class="input_label"><?php show_text(TextsHandler::LAST_NAME_LABEL, 'Last Name'); ?>:</label>
        <input type="text" name="last_name" id="last_name" value="<?php echo wp_kses_post($last_name); ?>" />
        
        <input type="submit" class="rsvp_submit" value="<?php show_text(TextsHandler::SEARCH_BUTTON_LABEL, 'Find me'); ?>" />
    </form>
    <?php
    
    if ($is_find_me) {
        search_attendee($first_name, $last_name);
    }
}

function search_attendee($first_name, $last_name) {
    if (empty($first_name) && empty($last_name)) {
        echo '<p class="form_error">' . wp_kses_post(text(TextsHandler::ERROR_EMPTY_FIRST_OR_LAST_NAME, 'Please enter a first name and/or a last name.')) . '</p>';
        return;
    }
    
    $attendees = AttendeesHandler::get_instance()->search($first_name, $last_name);
    
    if (!empty($attendees)) {
        ?>
        <table class="wp-list-table search-results-table">
            <tbody>
                <?php
                foreach ($attendees as $attendee) {
                    ?>
                    <tr class="search_result">
                        <td><b><?php echo wp_kses_post($attendee->first_name . ' ' . $attendee->last_name); ?></b></td>
                        <td class="search_result_confirmation_cell">
                            <form method="post">
                                <?php wp_nonce_field('show_rsvp_form', 'show_rsvp_form_nonce'); ?>
                                <input type="hidden" name="attendee_id" value="<?php echo intval($attendee->id); ?>" />
                                <input type="submit" value="<?php show_text(TextsHandler::RSVP_BUTTON_LABEL, 'RVSP'); ?>" />
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    } else {
        echo '<p>' . wp_kses_post(text(TextsHandler::ERROR_NO_ATTENDEES_FOUND, 'No attendees found.')) . '</p>';
    }
}

function show_rsvp_form($attendee_id, $event_id, $force_show) {
    $attendee = AttendeesHandler::get_instance()->get_attendee($attendee_id);
    $rsvp = AnswersHandler::get_instance()->get_answer($event_id, $attendee_id);
    if (!$force_show && $rsvp !== null && $rsvp->status !== "no_response") {
        show_already_rsvp_form($event_id, $attendee);
        return;
    }
    
    $full_name = $attendee->first_name . ' ' . $attendee->last_name;
    $status = $rsvp != null ? $rsvp->status : null;
    $message = $rsvp != null ? $rsvp->message : '';
    $related_attendees = RelatedAttendeesHandler::get_instance()->get_related_attendees($attendee_id);
    
    ?>
    <p class="welcome_label"><?php echo wp_kses_post(sprintf(text(TextsHandler::RSVP_WELCOME_LABEL, 'Welcome <b>%s</b>!'), $full_name)); ?></p>
    <form method="post">
        <?php wp_nonce_field('submit_rsvp', 'submit_rsvp_nonce'); ?>
        <input type="hidden" name="attendee_id" value="<?php echo esc_attr($attendee_id); ?>" />
        <input type="hidden" name="event_id" value="<?php echo esc_attr($event_id); ?>" />
        
        <p class="rsvp_question"><?php show_text(TextsHandler::RSVP_QUESTION, 'Will you attend the event?'); ?></p>
        <div class="radio-group">
            <?php echo '<input type="radio" id="rsvp_yes" name="rsvp_question" value="yes"' . ($status == 'yes' ? 'checked' : '') . ' required>' ?>
            <label class="rsvp_answer_radio_label" for="rsvp_yes"><?php show_text(TextsHandler::RSVP_ANSWER_YES, 'Yes'); ?></label>
        </div>
        <div class="radio-group">
            <?php echo '<input type="radio" id="rsvp_no" name="rsvp_question" value="no"' . ($status == 'no' ? 'checked>' : '>') ?>
            <label class="rsvp_answer_radio_label" for="rsvp_no"><?php show_text(TextsHandler::RSVP_ANSWER_NO, 'No'); ?></label>
        </div>
        <label class="rsvp-label" for="rsvp_message"><?php show_text(TextsHandler::RSVP_MESSAGE_TITLE, 'Your Message'); ?>:</label><br>
        <textarea name="rsvp_message" id="rsvp_message"><?php echo wp_kses_post($message) ?></textarea><br>

        <?php if (!empty($related_attendees)) { ?>
            <p class="rsvp_question"><b><?php show_text(TextsHandler::RSVP_RELATED_ATTENDEES_INFO_MESSAGE, 'The following people are associated with you.<br />You can also confirm their attendance at the event.'); ?></b></p>        
            <?php
            foreach ($related_attendees as $related_attendee) {
                show_related_attendee_rsvp($event_id, $related_attendee);
            } 
        }
        ?>
        <br />
        <input type="submit" class="rsvp_submit" value="<?php show_text(TextsHandler::RSVP_CONFIRM_BUTTON_LABEL, 'Confirm'); ?>">
    </form>
    <?php
}

function show_related_attendee_rsvp($event_id, $attendee) {
    $attendee_id = $attendee->id;
    $answer = AnswersHandler::get_instance()->get_answer($event_id, $attendee_id);
    $full_name = $attendee->first_name . ' ' . $attendee->last_name;
    $status = $answer != null ? $answer->status : null;

    $input_name = 'rsvp_question_' . $attendee_id; 
    $input_yes_id = 'rsvp_yes_' . $attendee_id; 
    $input_no_id = 'rsvp_no_' . $attendee_id;
    ?>
    <p class="rsvp_question"><?php echo wp_kses_post(sprintf(text(TextsHandler::RSVP_RELATED_ATTENDEES_RSVP_QUESTION, 'Will <b>%s</b> attend the event?'), $full_name)); ?></p>
    <div class="radio-group">
        <?php echo '<input type="radio" id="' . wp_kses_post($input_yes_id) . '" name="' . wp_kses_post($input_name) . '" value="yes"' . ($status == 'yes' ? 'checked' : '') . '>' ?>
        <label class="rsvp_answer_radio_label" for="<?php echo wp_kses_post($input_yes_id); ?>"><?php show_text(TextsHandler::RSVP_ANSWER_YES, 'Yes'); ?></label>
    </div>
    <div class="radio-group">
        <?php echo '<input type="radio" id="' . wp_kses_post($input_no_id) . '" name="' . wp_kses_post($input_name) . '" value="no"' . ($status == 'no' ? 'checked>' : '>') ?>
        <label class="rsvp_answer_radio_label" for="<?php echo wp_kses_post($input_no_id); ?>"><?php show_text(TextsHandler::RSVP_ANSWER_NO, 'No'); ?></label>
    </div>
    <?php
}

function show_already_rsvp_form($event_id, $attendee) {
    $full_name = $attendee->first_name . ' ' . $attendee->last_name;
    ?>
    <p><?php echo wp_kses_post(sprintf(text(TextsHandler::RSVP_ALREADY_ANSWER_LABEL, 'Hi <b>%s</b>,<br />You have already provided an answer. Do you want to modify it?'), $full_name)); ?></p>
    <form method="post">
        <input type="hidden" name="attendee_id" value="<?php echo esc_attr($attendee->id); ?>" />
        <input type="hidden" name="event_id" value="<?php echo esc_attr($event_id); ?>" />
        
        <input type="submit" name="modify_rsvp_yes" value="<?php show_text(TextsHandler::RSVP_ALREADY_ANSWER_YES_BUTTON_LABEL, 'Yes'); ?>">
        <input type="submit" name="modify_rsvp_no" value="<?php show_text(TextsHandler::RSVP_ALREADY_ANSWER_NO_BUTTON_LABEL, 'No'); ?>">
    </form>
    <?php
}

function submit_rsvp_form() {
    if (!isset($_POST['submit_rsvp_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['submit_rsvp_nonce'])), 'submit_rsvp')) {
        return;
    }
    $attendee_id = isset($_POST['attendee_id']) ? intval(sanitize_text_field(wp_unslash($_POST['attendee_id']))) : '';
    $event_id = isset($_POST['event_id']) ? intval(sanitize_text_field(wp_unslash($_POST['event_id']))) : '';

    // Check if the event is valid.
    $event = $event_id > 0 ? EventsHandler::get_instance()->get_event($event_id) : null; 
    if ($event == null) {
        echo '<p>' . wp_kses_post(__('Invalid event.', 'rsvp-manager')) . '</p>';
        return;
    }
    
    // Check if the attendee is valid.
    $attendee = $attendee_id > 0 ? AttendeesHandler::get_instance()->get_attendee($attendee_id) : null;
    if ($attendee == null) {
        echo '<p>' . wp_kses_post(wp_kses_post__('Invalid attendeee.', 'rsvp-manager')) . '</p>';
        return;
    }
    
    // Check if the attendee was added to the given event.
    $attendee_to_event = EventsAttendeesHandler::get_instance()->get_attendee_to_event($attendee_id, $event_id);
    if ($attendee_to_event == null) {
        echo '<p>' . wp_kses_post(__('The attendee is not on the list of attendees for the event.', 'rsvp-manager')) . '</p>';
        return;
    }
    
    $status = isset($_POST['rsvp_question']) ? sanitize_text_field(wp_unslash($_POST['rsvp_question'])) : 'no_response';
    $message = isset($_POST['rsvp_message']) ? sanitize_text_field(wp_unslash($_POST['rsvp_message'])) : '';
    $date = current_time('mysql');
    
    $success = AnswersHandler::get_instance()->save_answer($event_id, $attendee_id, $status, $date, $message);

    // Check the related attendees.
    $related_attendees = RelatedAttendeesHandler::get_instance()->get_related_attendees($attendee_id);
    if (!empty($related_attendees)) {
        foreach ($related_attendees as $related_attendee) {
            $related_attendee_id = $related_attendee->id;
            $rsvp_question_param = 'rsvp_question_' . $related_attendee_id;
            $related_attendee_status = isset($_POST[$rsvp_question_param]) ? sanitize_text_field(wp_unslash($_POST[$rsvp_question_param])) : '';
            $related_attendee_status = empty($related_attendee_status) ? 'no_response' : $related_attendee_status;
            AnswersHandler::get_instance()->save_answer($event_id, $related_attendee_id, $related_attendee_status, $date);
        }
    }

    if ($success) {
        if ($status == 'yes') {
            echo '<p class="rsvp_confirmation_message">' . wp_kses_post(text(TextsHandler::RSVP_YES_MESSAGE, 'Thank you very much for the confirmation and we look forward to the event!')) . '</p>';
        } else {
            echo '<p class="rsvp_confirmation_message">' . wp_kses_post(text(TextsHandler::RSVP_NO_MESSAGE, 'Thank you very much for your reply and we are sorry that you cannot attend the event!')) . '</p>';
        }
    } else {
        echo '<p>' . wp_kses_post(__('Something went wrong. Please try again!', 'rsvp-manager')) . '</p>';
    }
}
?>