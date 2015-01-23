<?php

/*
 * import-asn-ip-table.php
 * 
 * Run this from a command line
 * Imports http://thyme.apnic.net/current/data-raw-table into the find_host_asn_ip table 
 * 
 * Configure:
 * 
 * Usage:
 * php ./import-asn-ip-table.php
 * 
 * 
 * Reference: http://quaxio.com/bgp/ 
 * 
 * http://thyme.apnic.net/current/data-raw-table
 * http://thyme.apnic.net/current/data-used-autnums
 * 
 */


/*
*
* Include the Downcast Library
*
*/
// prevent silly notices, notably errors stemming from referencing $_SERVER when running via command line
ini_set('error_reporting', E_ERROR | E_WARNING | E_PARSE);

include_once("../../../lib/downcast/Downcast.php");
include_once("../../../lib/downcast/DowncastPlugin.php");
/*
* Create a new Plugin Object
*/

$downcast = new Downcast();

$ezsql_plugin=$downcast->getPlugin('ezSQL');
$FindMyHost=$downcast->getPlugin('FindMyHost');
$ezSQL=$downcast->getPlugin('ezSQL')->db;


/*
 * Configure Database
 */
//include('C:\\wamp\\www\\getthathost.com\\config\\wp-config-db.php');

define( 'ASN_TABLE_NAME', 'find_host_asn_owners' );




/*
 * Limits
 */
define( 'MAX_ASNS', 5 ); //max lines of asn files to read
define( 'MAX_ENABLED', true ); //whether to use the max or not. default false




/*
 * Enable New ASN File Download
 */

define( 'DOWNLOAD_NEW_ASN', false ); //max lines of asn files to read





/*
 * Paths
 */

/*
 * ASN FILE URL
 * Reference: 
 * http://quaxio.com/bgp/ 
 * http://thyme.apnic.net/
 */

define( 'ASN_FILE_URL', 'http://thyme.apnic.net/current/data-used-autnums' );

/*
 * Path to ASN files
 * 
 * 
 */
define( 'ASN_DIR', $FindMyHost->getRootDirectory() . '/install' );

/*
 * Include Required Libraries - Only need this if you aren't using this script via downcast
 */
// Include ezSQL core
//include_once (dirname( dirname( __FILE__ ) ) . '/libs/ezSQL/shared/ez_sql_core.php');

// Include ezSQL database specific component
//include_once (dirname( dirname( __FILE__ ) ) . '/libs/ezSQL/mysql/ez_sql_mysql.php');


// Initialise database object and establish a connection
// at the same time - db_user / db_password / db_name / db_host

/*
 * Download a new file
 */

if ( DOWNLOAD_NEW_ASN) {
    exec( "wget " . ASN_FILE_URL );
}

if (false){
/*
 * Create the ezSQL object
 */

$ezSQL = new ezSQL_mysql(
        DB_USER, //'db_user',
        DB_PASSWORD, //'db_password',
        DB_NAME, //'db_name',
        DB_HOST //db_host
);

}

#init
$count = 0;

    /*
     * Increase memory limits or will get errors
     */
    ini_set( 'memory_limit', '-1' ); //http://stackoverflow.com/a/7895598/3306354


/*
 * 
 * Read in ASN Table to Memory Array
 * 
 */


/*
 * Initialize Table
 */
    $asn_table = array();
    
    /*
     * Open the ASN File For Reading
     */
    $fh = fopen( ASN_DIR . '/data-used-autnums', 'r' );
    
    /*
     * Read Each Line for Input
     * 
     * 
     */
    while ( $line = fgets( $fh ) ) {
        $count++;
        $match = array();
    
 if ( preg_match( "%\s*(\d+)\s+(.*)%", $line, $match ) ) {
            $owner_id =   $ezSQL->escape($match[ 1 ]);
            $owner_name =  $ezSQL->escape($match[ 2 ]);
            $asn_table[] = " ('{$owner_id}', '{$owner_name }') ";



    }


    
        /*
         * Limit The Insert ( use for testing only ) 
         */
        if (
                MAX_ENABLED && $count >= MAX_ASNS ) {

            break;

}



    }



    /* About the insert
     * 
     * Because the table is huge (over 500k rows), 
     * we import using a multiple insert (for speed) instead of
     * inserting after each row is read (which can take well over an hour )
     * 
     * Because we read everything into memory, we need to increase memory limits
     * before we do the final query
     */



    /*
     * Truncate the table
     */
    $ezSQL->query( 'TRUNCATE TABLE `' . ASN_TABLE_NAME . '`' );

    /*
     * Increase mysql buffer limits or will get errors
     */

    $ezSQL->query( 'set global net_buffer_length=1000000' ); //http://stackoverflow.com/a/722656/3306354
    $ezSQL->query( 'set global max_allowed_packet=1000000000' );


    /*
     * Bulk Insert Values Into Table
     * This is *much* faster - about 1 minute compared to approx 1+ hours using individual inserts within a loop
     * ref:http://stackoverflow.com/a/16105520/3306354
     */
    $values = implode( " , ", $asn_table );
    $dbquery = "INSERT IGNORE INTO " . ASN_TABLE_NAME . " (`owner_id`,`owner_name`)  VALUES {$values} ; ";
//echo $dbquery;
   $ezSQL->query( $dbquery );


   die( 'exiting' . __LINE__ );






// Loading the owners table (asn => owner)
// You want to also cache this across requests!
$asn_owners = array();
$fh = fopen( ASN_DIR . '/data-used-autnums', 'r' );
while ( $line = fgets( $fh ) ) {
    echo '<br>' . $line;
    $match = array();
    if ( preg_match( "%\s*(\d+)\s+(.*)%", $line, $match ) ) {
        $asn_owners[ $match[ 1 ] ] = $match[ 2 ];
    }
    echo '<pre>', print_r( $asn_owners, true ), '</pre>';
    die( 'exiting' );
    }

// The actual lookup
$ip_value = ip2long( $ip );
$result = 'sorry, not found...';
for ( $i = 0; $i < 32; $i++ ) {
    $ip2 = ($ip_value >> $i) << $i;
    if ( isset( $asn_table[ $ip2 ] ) ) {
        $asn = $asn_table[ $ip2 ];
        $result = $ip . ' maps to asn ' . $asn;
        if ( isset( $asn_owners[ $asn ] ) ) {
            $result .= ' (' . $asn_owners[ $asn ] . ')';
    }
        break;
    }
    }

// Returning the response as JSONP
$arr = array( 'result' => $result );
header( 'Content-type: application/json' );
echo 'ip_to_asn(' . json_encode( $arr ) . ')';
?>