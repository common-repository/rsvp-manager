<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once 'tables/events.php';
require_once 'tables/attendees.php';
require_once 'tables/events_attendees.php';
require_once 'tables/answers.php';
require_once 'tables/texts.php';
require_once 'tables/related_attendees.php';

class DatabaseHandler {
    
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DatabaseHandler();
        }

        return self::$instance;
    }
    
    private $tables;
    
    private function __construct() {
        $this->tables = array(
            EventsTable::get_instance(),
            AttendeesTable::get_instance(),
            EventsAttendeesTable::get_instance(),
            AnswersTable::get_instance(),
            TextsTable::get_instance(),
            RelatedAttendeesTable::get_instance()
        );
    }
    
    function initialize() {
        foreach ($this->tables as $table) {
            $table->create();
        }

        // execute migrations if needed;
        foreach ($this->tables as $table) {
            $table->migrate();
        }
    }
}

?>