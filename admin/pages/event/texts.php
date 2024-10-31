<?php 

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../../database/handlers/texts_handler.php';

function display_manage_texts_page() {
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

            $texts = TextsHandler::get_instance()->get_texts($event->id) ?: [];

            // Load the already saved values.
            $rsvp_is_not_open_message = empty($texts[TextsHandler::RSVP_IS_NOT_OPEN_MESSAGE]) ? "" : $texts[TextsHandler::RSVP_IS_NOT_OPEN_MESSAGE];
            $rsvp_is_closed_message = empty($texts[TextsHandler::RSVP_IS_CLOSED_MESSAGE]) ? "" : $texts[TextsHandler::RSVP_IS_CLOSED_MESSAGE];
            $search_head_message = empty($texts[TextsHandler::SEARCH_HEAD_MESSAGE]) ? "" : $texts[TextsHandler::SEARCH_HEAD_MESSAGE];
            $first_name_label = empty($texts[TextsHandler::FIRST_NAME_LABEL]) ? "" : $texts[TextsHandler::FIRST_NAME_LABEL];
            $last_name_label = empty($texts[TextsHandler::LAST_NAME_LABEL]) ? "" : $texts[TextsHandler::LAST_NAME_LABEL];
            $search_button_label = empty($texts[TextsHandler::SEARCH_BUTTON_LABEL]) ? "" : $texts[TextsHandler::SEARCH_BUTTON_LABEL];
            $error_empty_first_or_last_name = empty($texts[TextsHandler::ERROR_EMPTY_FIRST_OR_LAST_NAME]) ? "" : $texts[TextsHandler::ERROR_EMPTY_FIRST_OR_LAST_NAME];
            $error_no_attendees_found = empty($texts[TextsHandler::ERROR_NO_ATTENDEES_FOUND]) ? "" : $texts[TextsHandler::ERROR_NO_ATTENDEES_FOUND];
            $rsvp_button_label = empty($texts[TextsHandler::RSVP_BUTTON_LABEL]) ? "" : $texts[TextsHandler::RSVP_BUTTON_LABEL];
            $rsvp_already_answer_label = empty($texts[TextsHandler::RSVP_ALREADY_ANSWER_LABEL]) ? "" : $texts[TextsHandler::RSVP_ALREADY_ANSWER_LABEL];
            $rsvp_already_answer_yes_button_label = empty($texts[TextsHandler::RSVP_ALREADY_ANSWER_YES_BUTTON_LABEL]) ? "" : $texts[TextsHandler::RSVP_ALREADY_ANSWER_YES_BUTTON_LABEL];
            $rsvp_already_answer_no_button_label = empty($texts[TextsHandler::RSVP_ALREADY_ANSWER_NO_BUTTON_LABEL]) ? "" : $texts[TextsHandler::RSVP_ALREADY_ANSWER_NO_BUTTON_LABEL];
            $rsvp_welcome_label = empty($texts[TextsHandler::RSVP_WELCOME_LABEL]) ? "" : $texts[TextsHandler::RSVP_WELCOME_LABEL];
            $rsvp_question = empty($texts[TextsHandler::RSVP_QUESTION]) ? "" : $texts[TextsHandler::RSVP_QUESTION];
            $rsvp_answer_yes = empty($texts[TextsHandler::RSVP_ANSWER_YES]) ? "" : $texts[TextsHandler::RSVP_ANSWER_YES];
            $rsvp_answer_no = empty($texts[TextsHandler::RSVP_ANSWER_NO]) ? "" : $texts[TextsHandler::RSVP_ANSWER_NO];
            $rsvp_message_title = empty($texts[TextsHandler::RSVP_MESSAGE_TITLE]) ? "" : $texts[TextsHandler::RSVP_MESSAGE_TITLE];
            $rsvp_related_attendees_info_message = empty($texts[TextsHandler::RSVP_RELATED_ATTENDEES_INFO_MESSAGE]) ? "" : $texts[TextsHandler::RSVP_RELATED_ATTENDEES_INFO_MESSAGE];
            $rsvp_relates_attendees_rsvp_question = empty($texts[TextsHandler::RSVP_RELATED_ATTENDEES_RSVP_QUESTION]) ? "" : $texts[TextsHandler::RSVP_RELATED_ATTENDEES_RSVP_QUESTION];
            $rsvp_confirm_button_label = empty($texts[TextsHandler::RSVP_CONFIRM_BUTTON_LABEL]) ? "" : $texts[TextsHandler::RSVP_CONFIRM_BUTTON_LABEL];
            $rsvp_yes_message = empty($texts[TextsHandler::RSVP_YES_MESSAGE]) ? "" : $texts[TextsHandler::RSVP_YES_MESSAGE];
            $rsvp_no_message = empty($texts[TextsHandler::RSVP_NO_MESSAGE]) ? "" : $texts[TextsHandler::RSVP_NO_MESSAGE];
        ?>

        <?php echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">' ?>
            <?php wp_nonce_field('save_texts', 'save_texts_nonce'); ?>
            <input type="hidden" name="action" value="save_texts">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th colspan="2" class="form-table-section-title">
                            <?php echo wp_kses_post(__('RSVP Status', 'rsvp-manager')); ?>
                        </tr>
                    </th>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_is_not_open_message"><?php esc_html_e('RSVP not open message:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_is_not_open_message" name="rsvp_is_not_open_message" value="<?php echo wp_kses_post($rsvp_is_not_open_message); ?>">
                            <p class="text_default_value"><?php echo wp_kses_post(htmlspecialchars('Default is "The RSVP will start on <b>%s</b>!", where %s is the date when the RSVP will start.')); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_is_closed_message"><?php esc_html_e('RSVP is closed message:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_is_closed_message" name="rsvp_is_closed_message" value="<?php echo wp_kses_post($rsvp_is_closed_message); ?>">
                            <p class="text_default_value"><?php echo wp_kses_post(htmlspecialchars('Default is "The RSVP ended on <b>%s</b>!", where %s is the date when the RSVP ended.')); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2" class="form-table-section-title">
                            <?php echo wp_kses_post(__('RSVP Search', 'rsvp-manager')); ?>
                        </tr>
                    </th>
                    <tr>
                        <th scope="row">
                            <label for="search_head_message"><?php esc_html_e('Search head message:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="search_head_message" name="search_head_message" value="<?php echo wp_kses_post($search_head_message); ?>">
                            <p class="text_default_value">Default is "To start the confirmation process, please enter your first and last name in the fields below and search.".</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="first_name_label"><?php esc_html_e('"First Name" label:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="first_name_label" name="first_name_label" value="<?php echo wp_kses_post($first_name_label); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="last_name_label"><?php esc_html_e('"Last Name" label:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="last_name_label" name="last_name_label" value="<?php echo wp_kses_post($last_name_label); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="search_button_label"><?php esc_html_e('Search button label:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="search_button_label" name="search_button_label" value="<?php echo wp_kses_post($search_button_label); ?>">
                            <p class="text_default_value">Default is "Find me".</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="error_empty_first_or_last_name"><?php esc_html_e('Error empty first or last name:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="error_empty_first_or_last_name" name="error_empty_first_or_last_name" value="<?php echo wp_kses_post($error_empty_first_or_last_name); ?>">
                            <p class="text_default_value">Default is "Please enter a first name and/or a last name.".</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="error_no_attendees_found"><?php esc_html_e('Error no attendees found:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="error_no_attendees_found" name="error_no_attendees_found" value="<?php echo wp_kses_post($error_no_attendees_found); ?>">
                            <p class="text_default_value">Default is "No attendees found.".</p>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2" class="form-table-section-title">
                            <?php echo wp_kses_post(__('RSVP Search Results', 'rsvp-manager')); ?>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_button_label"><?php esc_html_e('RSVP button label:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_button_label" name="rsvp_button_label" value="<?php echo wp_kses_post($rsvp_button_label); ?>">
                            <p class="text_default_value">Default is "RSVP".</p>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2" class="form-table-section-title">
                            <?php echo wp_kses_post(__('Already RSVP', 'rsvp-manager')); ?>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_already_answer_label"><?php esc_html_e('Message:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_already_answer_label" name="rsvp_already_answer_label" value="<?php echo wp_kses_post($rsvp_already_answer_label); ?>">
                            <p class="text_default_value"><?php echo wp_kses_post(htmlspecialchars('Default is "Hi <b>%s</b>,<br />You have already provided an answer. Do you want to modify it?", where %s is replaced with the full name of the attendee.')); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_already_answer_yes_button_label"><?php esc_html_e('"Yes" button label:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_already_answer_yes_button_label" name="rsvp_already_answer_yes_button_label" value="<?php echo wp_kses_post($rsvp_already_answer_yes_button_label); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_already_answer_no_button_label"><?php esc_html_e('"No" button label:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_already_answer_no_button_label" name="rsvp_already_answer_no_button_label" value="<?php echo wp_kses_post($rsvp_already_answer_no_button_label); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2" class="form-table-section-title">
                            <?php echo wp_kses_post(__('RSVP', 'rsvp-manager')); ?>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_welcome_label"><?php esc_html_e('Welcome label:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_welcome_label" name="rsvp_welcome_label" value="<?php echo wp_kses_post($rsvp_welcome_label); ?>">
                            <p class="text_default_value"><?php echo wp_kses_post(htmlspecialchars('Default is "Welcome <b>%s</b>!", where %s is replaced with the full name of the attendee.')); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_question"><?php esc_html_e('RSVP question:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_question" name="rsvp_question" value="<?php echo wp_kses_post($rsvp_question); ?>">
                            <p class="text_default_value">Default is "Will you attend the event?".</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_answer_yes"><?php esc_html_e('Answer "Yes":', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_answer_yes" name="rsvp_answer_yes" value="<?php echo wp_kses_post($rsvp_answer_yes); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_answer_no"><?php esc_html_e('Answer "No":', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_answer_no" name="rsvp_answer_no" value="<?php echo wp_kses_post($rsvp_answer_no); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_message_title"><?php esc_html_e('Message title:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_message_title" name="rsvp_message_title" value="<?php echo wp_kses_post($rsvp_message_title); ?>">
                            <p class="text_default_value">Default is "Your Message:".</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_related_attendees_info_message"><?php esc_html_e('Related attendees info message:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_related_attendees_info_message" name="rsvp_related_attendees_info_message" value="<?php echo wp_kses_post($rsvp_related_attendees_info_message); ?>">
                            <p class="text_default_value"><?php echo wp_kses_post(htmlspecialchars('Default is "The following people are associated with you.<br />You can also confirm their attendance at the event.".')); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_relates_attendees_rsvp_question"><?php esc_html_e('Related attendees RSVP question:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_relates_attendees_rsvp_question" name="rsvp_relates_attendees_rsvp_question" value="<?php echo wp_kses_post($rsvp_relates_attendees_rsvp_question); ?>">
                            <p class="text_default_value"><?php echo wp_kses_post(htmlspecialchars('Default is "Will <b>%s</b> attend the event?", where %s is the full name of the attendee.')); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_confirm_button_label"><?php esc_html_e('Confirm button label:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_confirm_button_label" name="rsvp_confirm_button_label" value="<?php echo wp_kses_post($rsvp_confirm_button_label); ?>">
                            <p class="text_default_value">Default is "Confirm".</p>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2" class="form-table-section-title">
                            <?php echo wp_kses_post(__('RSVP Answer', 'rsvp-manager')); ?>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_yes_message"><?php esc_html_e('RSVP Yes Message:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_yes_message" name="rsvp_yes_message" value="<?php echo wp_kses_post($rsvp_yes_message); ?>">
                            <p class="text_default_value">Default is "Thank you very much for the confirmation and we look forward to the event!".</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="rsvp_no_message"><?php esc_html_e('RSVP No Message:', 'rsvp-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="rsvp_no_message" name="rsvp_no_message" value="<?php echo wp_kses_post($rsvp_no_message); ?>">
                            <p class="text_default_value">Default is "Thank you very much for your reply and we are sorry that you cannot attend the event!".</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button(wp_kses_post(__('Save', 'rsvp-manager'))); ?>
        <?php echo '</form>' ?>
    </div>
    <?php
}
?>