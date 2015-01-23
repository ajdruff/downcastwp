<?php


/**
 * import-asn.php
 *
 * Imports the ASN Files 
 * 
 * Usage: Run this from the command line: 
 *      cd /cygdrive/c/wamp/www/getthathost.com/public_html/wp-content/plugins/downcastwp/plugins/FindMyHost/install
 *      php ./import-asn.php
 * 
 * Based on code provided by http://quaxio.com/bgp/ 
 *
 * 
 * ASN Files are provided by
 * http://thyme.apnic.net/current/data-raw-table
 * http://thyme.apnic.net/current/data-used-autnums
 * 
 * 
 * @package FindMyHost_
 * @author Andrew Druffner <andrew@nomstock.com>
 * @copyright  2012 Andrew Druffner
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @filesource
 */

include('ImportAsn.php');


$asn=new FindMyHost_ImportAsn();


$asn->importAsnIp();
$asn->importAsnOwners();



?>