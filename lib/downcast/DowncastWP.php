<?php

include ("DowncastBaseWP.php");

/**
 * Downcast
 *
 * Manages the Downcast Web Framework within WordPress
 *
 * todo: 
 * override addPage so we just have to define it from within the framework, not have to create a wordpress page for it.
 * create a way to 
 * @package Downcast
 * @author Andrew Druffner <andrew@nomstock.com>
 * @copyright  2012 Andrew Druffner
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @filesource
 */
class Downcast extends DowncastBaseWP   {
 
    /**
     * Config
     *
     * Configure - Add User Settings Here
     * @param none
     * @return void
     */
    public function config() {



        /*
         * SITE_CONFIG_FILE_PATH
         *
         * Set the Site Configuration File Location
         * Absolute or relative path.
         * Default: "config.json"
         */
        $this->SITE_CONFIG_FILE_PATH = "config.json";



    }





}

?>