<?php 

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

class PluginActivationHandler {

    function __construct($plugin_file) {
        register_activation_hook($plugin_file, [$this, 'on_activate_plugin']);
    }

    /**
     * Upon plugin activation. 
     */
    function on_activate_plugin() {
        DatabaseHandler::getInstance()->initialize();
    }

}


?>