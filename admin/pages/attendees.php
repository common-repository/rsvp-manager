<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '../../database/handlers/attendees_handler.php';

/**
 * Manage the list of attendees for the given even.
 */
function display_manage_attendees_page($event) {    
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
    
    $attendees = AttendeesHandler::get_instance()->get_attendees($event->id);
    
    ?>
    <div class="wrap">
        <h1><?php echo wp_kses_post(__('Attendees for ', 'rsvp-manager') . '"' . $event->name . '"'); ?></h1>
        <br />
        <!-- Add Attendee Button -->
        <?php
        $manage_attendee_url = wp_nonce_url(
            add_query_arg(
                array(
                    'page'      => 'events',
                    'action'    => 'manage_attendee',
                    'event_id'  => $event->id
                ), admin_url('admin.php')
            ), 
            'manage_attendee', 
            'manage_attendee_nonce'
        );
        ?> 
        <a href="<?php echo esc_url($manage_attendee_url); ?>" class="button button-secondary"><?php esc_html_e('Add Attendee', 'rsvp-manager'); ?></a>
        <br />
        <?php echo '<form id="attendees-filter" method="post" action="' . esc_url(admin_url('admin-post.php')) . '">' ?>
            <?php wp_nonce_field('bulk_delete_attendees', 'bulk_delete_attendees_nonce'); ?>
            <input type="hidden" name="action" value="bulk_delete_attendees">
            <input type="hidden" name="event_id" value="<?php echo intval($event->id) ?>">
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text"><?php esc_html_e('Select bulk action', 'rsvp-manager'); ?></label>
                    <select name="action_selector_header" id="bulk-action-selector-top">
                        <option value="-1"><?php esc_html_e('Bulk Actions', 'rsvp-manager'); ?></option>
                        <option value="delete"><?php esc_html_e('Delete', 'rsvp-manager'); ?></option>
                    </select>
                    <input type="submit" id="action" class="button action" value="<?php esc_html_e('Apply', 'rsvp-manager'); ?>">
                </div>
            </div>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <td id="all_attendees_checkbox_header" class="manage-column column-cb check-column">
                            <input type="checkbox">
                        </td>
                        <th scope="col">
                            <?php esc_html_e('Attendee', 'rsvp-manager'); ?>
                        </th>
                        <th scope="col">
                            <?php esc_html_e('RSVP Status', 'rsvp-manager'); ?>
                        </th>
                        <th scope="col">
                            <?php esc_html_e('RSVP Date', 'rsvp-manager'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($attendees)) : ?>
                        <?php foreach ($attendees as $attendee) { 
                            $rsvp = AnswersHandler::get_instance()->get_answer($event->id, $attendee->id);
                            ?>
                            <tr>
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="attendee_ids[]" value="<?php echo intval($attendee->id); ?>">
                                </th>
                                <td>
                                    <?php
                                    $manage_attendee_url = wp_nonce_url(
                                        add_query_arg(
                                            array(
                                                'page'          => 'events',
                                                'action'        => 'manage_attendee',
                                                'attendee_id'   => $attendee->id
                                            ), admin_url('admin.php')
                                        ), 
                                        'manage_attendee', 
                                        'manage_attendee_nonce'
                                    );
                                    ?>
                                    <b><a href="<?php echo esc_url($manage_attendee_url); ?>"><?php echo wp_kses_post($attendee->first_name . ' ' . $attendee->last_name); ?></a></b>
                                    <div>
                                        <a href="<?php echo esc_url($manage_attendee_url);; ?>"><?php echo wp_kses_post(__('Edit', 'rsvp-manager')); ?></a>
                                        <span class="vertical-separator">|</span>
                                        <?php 
                                        $delete_attendee_url = wp_nonce_url(
                                            add_query_arg(
                                                array(
                                                    'action'        => 'delete-attendee',
                                                    'attendee_id'   => $attendee->id
                                                ), admin_url('admin-post.php')
                                            ), 
                                            'delete_attendee', 
                                            'delete_attendee_nonce'
                                        );
                                        ?>
                                        <a href="<?php echo esc_url($delete_attendee_url); ?>" class="submitdelete submitdelete_attendee" onclick="return confirm('<?php echo wp_kses_post(__('Are you sure you want to delete this attendee?', 'rsvp-manager')); ?>');"><?php echo wp_kses_post(__('Delete', 'rsvp-manager')); ?></a>
                                    </div>
                                </td>
                                <td><?php echo wp_kses_post($rsvp->status); ?></td>
                                <td><?php echo wp_kses_post(empty($rsvp->date) ? '-' : $rsvp->date); ?></td>
                            </tr>
                        <?php } ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4"><?php esc_html_e('No attendees found.', 'rsvp-manager'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td id="all_attendees_checkbox_footer" class="manage-column column-cb check-column">
                            <input type="checkbox">
                        </td>
                        <th scope="col">
                            <?php esc_html_e('Attendee', 'rsvp-manager'); ?>
                        </th>
                        <th scope="col">
                            <?php esc_html_e('RSVP Status', 'rsvp-manager'); ?>
                        </th>
                        <th scope="col">
                            <?php esc_html_e('RSVP Date', 'rsvp-manager'); ?>
                        </th>
                    </tr>
                </tfoot>
            </table>
            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text"><?php esc_html_e('Select bulk action', 'rsvp-manager'); ?></label>
                    <select name="action_selector_footer" id="bulk-action-selector-bottom">
                        <option value="-1"><?php esc_html_e('Bulk Actions', 'rsvp-manager'); ?></option>
                        <option value="delete"><?php esc_html_e('Delete', 'rsvp-manager'); ?></option>
                    </select>
                    <input type="submit" id="action2" class="button action" value="<?php esc_html_e('Apply', 'rsvp-manager'); ?>">
                </div>
            </div>
        <?php echo '</form>' ?>
    </div>
    <?php
}

?>