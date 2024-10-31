<?php

namespace CodeverseRSVPManager;

/**
 *  Prevents direct access
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Interface for all tables.
 */
interface ITable {

    /**
     * The prefix used by all table names.
     */
    const PREFIX = "codeverse_rsvp_";

    /**
     * Creates the table if it doesn't exist.
     */
    function create();
    
    /**
     * Migrates the table if needed.
     */
    function migrate();
    
    /**
     * Deletes the table.
     */
    function delete();
    
}

?>