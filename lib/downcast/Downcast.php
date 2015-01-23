<?php


include ("DowncastBase.php");


/**
* Downcast
*
* Manages the Downcast Web Framework
*

* @package Downcast
* @author Andrew Druffner <andrew@nomstock.com>
* @copyright  2012 Andrew Druffner
* @license    http://www.php.net/license/3_01.txt  PHP License 3.01
* @filesource
*/
class Downcast extends DowncastBase   {

/**
* Config
*
* Configure - Add User Settings Here
* @param none
* @return void
*/

public function config() {

    /*
     * Change Time Zone Here if needed
     *     ini_set('date.timezone', 'Asia/Calcutta');
     * http://php.net/manual/en/timezones.php for a list of timezones
     * To check which timezone you are using, add this line: echo date_default_timezone_get();
     */
    

    

/*
* SITE_CONFIG_FILE_PATH
*
* Set the Site Configuration File Location
* Absolute or relative path.
* Default: "config.json"
*/
$this->SITE_CONFIG_FILE_PATH = "config.json";

/*
* Whether this is a WordPress site or not
*/

$this->WORDPRESS = false;


}


}



?>