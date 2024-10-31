<?php 

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'event.php';
require_once plugin_dir_path(__FILE__) . 'texts.php';
require_once plugin_dir_path(__FILE__) . '../../../database/handlers/events_handler.php';

function display_event_main_page() {
    $event = EventsHandler::get_instance()->get_event();
    $selected_tab = null;
    if (isset($_GET['tab_event_details_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['tab_event_details_nonce'])), 'tab_event_details')) {
        $selected_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : null;
    } else if (isset($_GET['tab_texts_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['tab_texts_nonce'])), 'tab_texts')) {
        $selected_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : null;
    }
    ?>
    
    <h2><?php echo wp_kses_post($event !== null ? $event->name : ''); ?></h2>
    <ul class="tab-links">
        <?php 
            echo '<li' . ($selected_tab == null || $selected_tab === 'tab_event_details' ? ' class = "active"' : '') . '><a href="#tab_event_details">' . wp_kses_post(__('Event Details', 'rsvp-manager')) . '</a></li>';
            echo '<li' . ($selected_tab != null && $selected_tab === 'tab_texts' ? ' class = "active"' : '') . '><a href="#tab_texts">' . wp_kses_post(__('Texts', 'rsvp-manager')) . '</a></li>';
        ?>
    </ul>

    <div class="tab-content">
        <div id="tab_event_details" class="tab <?php echo $selected_tab == null || $selected_tab === 'tab_event_details' ? 'active' : ''; ?>">
            <?php display_manage_event_page(); ?>
        </div>

        <div id="tab_texts" class="tab <?php echo $selected_tab != null && $selected_tab === 'tab_texts' ? ' active' : ''; ?>">
            <?php
            display_manage_texts_page();
            ?>
        </div>
    </div>
    <?php
}

?>