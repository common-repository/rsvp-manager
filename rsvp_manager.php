<?php
/**
 * @package rsvp-manager
 * @author Codeverse
 * @version 1.1
 * Plugin Name: RSVP Manager
 * Description: Manage the RSVP process for private events.
 * Version: 1.1
 * Author: Codeverse
 * License: GPLv3
 * 
 * INSTALLATION: see the readme.txt file.
 * 
 * USAGE: After installing the plugin you can edit your event, add attendees and modify the texts from admin area.  
 *        To include the rsvp view in your website, include the short code anywhere you want it to be displayed.
 */

 namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'database/setup.php';
require_once plugin_dir_path(__FILE__) . 'admin/actions/manage-event.php';
require_once plugin_dir_path(__FILE__) . 'admin/actions/manage-attendee.php';
require_once plugin_dir_path(__FILE__) . 'admin/actions/delete-attendees.php';
require_once plugin_dir_path(__FILE__) . 'admin/actions/manage-texts.php';
require_once plugin_dir_path(__FILE__) . 'ui/event_short_code.php';

class RSVPManager {

    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new RSVPManager();
        }

        return self::$instance;
    }

    private function __construct() {
        require_once 'initialization/PluginActivationHandler.php';
        require_once 'initialization/ScripsHandler.php';
        require_once 'initialization/PluginDeactivationHandler.php';
        require_once 'admin/menu/Menu.php';

        new PluginActivationHandler(__FILE__);
        AdminMenuHandler::get_instance();
        new ScripsHandler();
        new PluginDeactivationHandler();
    }
}

RSVPManager::get_instance();

?>