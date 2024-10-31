<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

class AdminMenuHandler {

    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new AdminMenuHandler();
        }

        return self::$instance;
    }

    public $events_hook_suffix = "";
    public $manage_event_hook_suffix = "";
    public $info_hook_suffix = "";

    private function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu_options']);
    }

    /**
     * Adds the RSVP option in the menu in Admin.
     */
    function add_admin_menu_options() {
        include_once(plugin_dir_path(__FILE__) . '../pages/events.php');
        include_once(plugin_dir_path(__FILE__) . '../pages/event/event_main.php');
        include_once(plugin_dir_path(__FILE__) . '../pages/attendees.php');
        include_once(plugin_dir_path(__FILE__) . '../pages/attendee/attendee.php');
        include_once(plugin_dir_path(__FILE__) . '../pages/info.php');
        
        $this->add_top_evel_menu_option();
        $this->add_events_option();
        $this->add_manage_event_option();
        $this->add_info_option();

        // By default the main option adds a sub menu with the same name as well. We don't need it so we remove it from the submenu.
        remove_submenu_page('rsvp', 'rsvp');
    }

    private function add_top_evel_menu_option() {
        add_menu_page(
            __('RSVP Manager by Codeverse', 'rsvp-manager'),
            __('RSVP Manager', 'rsvp-manager'),
            'manage_options',
            'rsvp',
            'display_rsvp_main_page',
            plugins_url('../../icons/menu_option_icon.svg', __FILE__),
            99
        );
    }

    private function add_events_option() {
        $this->events_hook_suffix = add_submenu_page(
            'rsvp',
            __('Event', 'rsvp-manager'),
            __('Event', 'rsvp-manager'),
            'manage_options',
            'events',
            __NAMESPACE__ . '\\display_manage_events_page'
        );
    }

    private function add_manage_event_option() {
        $this->manage_event_hook_suffix = add_submenu_page(
            'rsvp',
            __('Update Event', 'rsvp-manager'),
            __('Update Event', 'rsvp-manager'),
            'manage_options',
            'manage-event',
            __NAMESPACE__ . '\\display_event_main_page'
        );
    }

    private function add_info_option() {
        $this->info_hook_suffix = add_submenu_page(
            'rsvp',
            __('Info', 'rsvp-manager'),
            __('Info', 'rsvp-manager'),
            'manage_options',
            'info',
            __NAMESPACE__ . '\\display_info_page'
        );
    }

    private function display_rsvp_main_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html(__('RSVP by Codeverse', 'rsvp-manager')) . '</h1>';
        echo '<p>' . esc_html(__('Plugin to manage private events.', 'rsvp-manager')) . '</p>';
        echo '</div>';
    }
}
?>