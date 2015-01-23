<?php


/**
 * build-host-lookups-table.php
 *
 * Imports websites from file, looks up their hosts, and inserts host clues and guesses into lookup table
 * 
 * Usage: 
 * before running, disable unnecessary plugins to avoid errors, including:
 * 
 * 
 * 
 * Then run from the command line: 
 *      cd /cygdrive/c/wamp/www/getthathost.com/public_html/wp-content/plugins/downcastwp/plugins/FindMyHost/shell-scripts
 *      php ./build-host-lookups-table.php
 * 
 * 
 * 
 * @package FindMyHost_
 * @author Andrew Druffner <andrew@nomstock.com>
 * @copyright  2012 Andrew Druffner
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @filesource
 */

include('BuildHostLookups.php');


$hosts=new FindMyHost_BuildHostLookups();


$domains=$hosts->buildHostLookups();






?>