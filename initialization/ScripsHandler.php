<?php 

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

class ScripsHandler {

    function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enque_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enque_admin_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'enque_event_short_code_styles']);
    }

    /**
     * Adds the styles for admin area.
     */ 
    function enque_admin_styles($hook_suffix) {
        wp_enqueue_style('general-admin-styles', plugin_dir_url(__FILE__) . '../css/admin-styles.css', array(), '1.0.0', 'all');
        $this->enque_manage_event_styles($hook_suffix);
        $this->enque_manage_attendee_styles($hook_suffix);
        $this->enque_info_styles($hook_suffix);
    }

    private function enque_manage_event_styles($hook_suffix) {
        $manage_event = AdminMenuHandler::get_instance()->manage_event_hook_suffix;
        if ($manage_event === $hook_suffix) {
            wp_enqueue_style('tabs-styles', plugin_dir_url(__FILE__) . '../css/tabs-styles.css', array(), '1.0.0', 'all');
        }
    }

    private function enque_manage_attendee_styles($hook_suffix) {
        $events = AdminMenuHandler::get_instance()->events_hook_suffix;
        if ($events === $hook_suffix) {
            $is_nonce_valid = isset($_GET['manage_attendee_nonce']) ? wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['manage_attendee_nonce'])), 'manage_attendee') : false;
            $action = $is_nonce_valid && isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : null;
            if ($action === 'manage_attendee') {
                wp_enqueue_style('related-attendees-styles', plugin_dir_url(__FILE__) . '../css/related-attendees-styles.css', array(), '1.0.0', 'all');
            }
        }
    }

    private function enque_info_styles($hook_suffix) {
        $info = AdminMenuHandler::get_instance()->info_hook_suffix;
        if ($info === $hook_suffix) {
            wp_enqueue_style('info-styles', plugin_dir_url(__FILE__) . '../css/info-styles.css', array(), '1.0.0', 'all');
        }
    }


    function enque_admin_scripts($hook_suffix) {
        $this->enque_manage_attendee_scripts($hook_suffix);
        $this->enque_event_main_scripts($hook_suffix);
    }

    private function enque_manage_attendee_scripts($hook_suffix) {
        $events = AdminMenuHandler::get_instance()->events_hook_suffix;
        if ($events === $hook_suffix) {
            $is_nonce_valid = isset($_GET['manage_attendee_nonce']) ? wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['manage_attendee_nonce'])), 'manage_attendee') : false;
            $action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : null;
            if ($action === 'manage_attendee') {
                wp_enqueue_script('related-attendees-script', plugin_dir_url(__FILE__) . '../admin/pages/attendee/attendee.js', array(), '1.0.0', true);
            }
        }
    }

    private function enque_event_main_scripts($hook_suffix) {
        $manage_event = AdminMenuHandler::get_instance()->manage_event_hook_suffix;
        if ($manage_event === $hook_suffix) {
            wp_enqueue_script('event-main-tabs-script', plugin_dir_url(__FILE__) . '../admin/pages/event/event_main.js', array(), '1.0.0', true);
        }
    }

    /**
     * Adds the style for the short code.
     */
    function enque_event_short_code_styles() {
        wp_register_style('short-code-styles', plugin_dir_url(__FILE__) . '../css/short-code.css', array(), '1.0.0', 'all');
        
        wp_enqueue_style('short-code-styles');
    }
}

?>