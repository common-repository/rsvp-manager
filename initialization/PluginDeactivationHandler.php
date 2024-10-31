<?php 

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

class PluginDeactivationHandler {

    function __construct() {
        register_deactivation_hook(__FILE__, [$this, 'on_deactivate_plugin']);
    }

    /**
     * Upon plugin deactivation. 
     */
    function on_deactivate_plugin() {
        
    }

}


?>