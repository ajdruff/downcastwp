<?php


/**
* Downcast WP
*
* Integrates Downcast into WordPress
* Plugin Name: Downcast WP
* Plugin URI: http://simpliwp.com/forms/
* Description: Adds the Downcast Web Framework to WordPress
* @version: 1.0.0
* @author Andrew Druffner <andrew@nomstock.com>
* Author URI: http://nomstock.com/about/
* @copyright  2012-2013 Nomstock, LLC
* @license   All Rights Reserved
* @package DowncastWP
* @subpackage Core
* @filesource
*
*/

/*
*
* Include the Downcast Library
*
*/

include_once("lib/downcast/DowncastWP.php");
include_once("lib/downcast/DowncastPlugin.php");


// disable auto-p
remove_filter('the_content', 'wpautop');



// disable texturize
remove_filter('the_content', 'wptexturize');


/*
* Create a new Plugin Object
*/

$simpli_downcast = new Downcast();



?>