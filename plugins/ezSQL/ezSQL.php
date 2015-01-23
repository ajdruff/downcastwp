<?php

/**
 * ezSQL
 *
 * ezSQL is a library that makes it easy to deal with databases. In fact, the WordPress $wbdb object is based on ezSQL.
 * From the intro by the author: ezSQL is a widget that makes it ridiculously easy for you to use PHP-PDO, mySQL, Oracle8, InterBase/FireBird, PostgreSQL, SQLite (PHP), SQLite (C++) or MS-SQL database(s) 
 * 
 * ezSQL can also be used within WordPress and is used in the WordPress mod to enable easier transition of code between WordPress and standalone.
 * 
 * For a demo, see demo/demo.php
 * 
 * Usage: $db = $this->getPlugin( "ezSQL" )->db;
 *  you can then use it as you normally would for either the $wpdb object (WordPress) or the $db object (ezSQL demo)
 * 
 * 
 * @package Downcast
 * @author Andrew Druffner <andrew@nomstock.com>
 * @copyright  2012 Andrew Druffner
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * 
 */
class ezSQL extends DowncastPlugin {

    public $db = null;

    /**
     * Configure
     *
     * Plugin Configuration
     * Add any code here to set variables and configuration values.
     *
     * @param none
     * @return void
     */
    public function config() {
        //File that includes constant definitions for DB_USER,DB_PASSWORD,DB_NAME,DB_HOST
        $this->DB_CONFIG_FILE = 'C:\\wamp\\www\\getthathost.com\\config\\wp-config-db.php';

    }

    /**
     * Inititialize
     *
     * Plugin Initialization
     * Add any code here that you want fired when you create plugin and just after configuration.
     *
     * @param none
     * @return void
     */
    public function init() {
        $this->loadEzSQL();


    }

    /**
     * Load EzSQL
     *
     * Loads an ezSQL or wpdb object depending on whether WordPress is available
     *
     * @param none
     * @return void
     */
    public function loadEzSQL() {

        /*
         * Create the ezSQL object to Initialise database object and establish a connection
         */
        if ( !class_exists( 'wpdb' ) ){//if not wordpress include ezsql
            // Include ezSQL core
            include_once $this->getRootDirectory() . "/libs/ezSQL/shared/ez_sql_core.php";

            // Include ezSQL core mysql component
            include_once $this->getRootDirectory() . "/libs/ezSQL/mysql/ez_sql_mysql.php";

//include DB_USER,DB_PASSWORD,DB_NAME,DB_HOST
            include_once($this->DB_CONFIG_FILE);
            $db = new ezSQL_mysql(
                    DB_USER, //'db_user',
                    DB_PASSWORD, //'db_password',
                    DB_NAME, //'db_name',
                    DB_HOST //db_host
            );
} else{ //WordPress already has these defined
            $db = new wpdb(
                    DB_USER, //'db_user',
                    DB_PASSWORD, //'db_password',
                    DB_NAME, //'db_name',
                    DB_HOST //db_host
            );
}
        $this->db = $db;

    }

    /**
     * Add Hooks
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function addHooks() {
        
        }


}

?>
