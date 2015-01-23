<?php

/**
 * FindMyHost_ImportAsn
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


class FindMyHost_ImportAsn{

    /**
     * Configure
     *
     * Configure
     *
     * @param none
     * @return void
     */
    public function config() {
        $this->MAX_ROWS = 5;// Max Number of Rows to Import
        $this->MAX_ENABLED = false;//whether to limit import to maximum rows
        $this->DOWNLOAD_NEW_ASN = false; //Whether to Download New Asn File
        $this->ASN_DIR = $this->FIND_MY_HOST_PLUGIN->getRootDirectory() . '/install';



    }

    public function __construct() {

        /*
         *
         * Include the Downcast Library
         *
         */
// prevent silly notices, notably errors stemming from referencing $_SERVER when running via command line
        ini_set( 'error_reporting', E_ERROR | E_WARNING | E_PARSE );

        include_once("../../../lib/downcast/Downcast.php");
        include_once("../../../lib/downcast/DowncastPlugin.php");
        /*
         * Create a new Plugin Object
         */

        $downcast = new Downcast();

        $this->EZ_SQL_PLUGIN = $downcast->getPlugin( 'ezSQL' );
        $this->FIND_MY_HOST_PLUGIN = $downcast->getPlugin( 'FindMyHost' );
        $this->DB = $downcast->getPlugin( 'ezSQL' )->db;


        $this->config();
        $this->init();
    }

    /**
     * Initialize
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function init() {
        
    }

    public $DB = null;
    public $FIND_MY_HOST_PLUGIN = null;
    public $EZ_SQL_PLUGIN = null;

    /**
     * Import Asn Ip
     *
     * Downloads and Imports Asn to Ip Mapping
     *
     * @param none
     * @return void
     */
    public function importAsnIp() {


        if ( $this->DOWNLOAD_NEW_ASN ) {
$this->getFileFromWeb(
        'http://thyme.apnic.net/current/data-raw-table', //url
        $this->ASN_DIR .'/data-raw-table'//file
        );
            
            
}


        $this->import(
                $this->ASN_DIR . '/data-raw-table', //$import_file
                "%([0-9.]+)/\d+\s+(\d+)%", // $line_regex                
                'find_host_asn_ip', //$asn_table_name
                'ip_long', //$id_field_name
                'asn', //$value_field_name
                'ip2long'//$id_field_name_callback
        );



    }

    /**
     * Import Asn Owners
     *
     * Downloads and Imports Asn Owners into Database
     *
     * @param none
     * @return void
     */
    public function importAsnOwners() {
        if ( $this->DOWNLOAD_NEW_ASN ) {
$this->getFileFromWeb(
        'http://thyme.apnic.net/current/data-used-autnums', //url
        $this->ASN_DIR .'/data-used-autnums'//file
        );
            
            
}


        $this->import(
                $this->ASN_DIR . '/data-used-autnums', //$import_file
                "%\s*(\d+)\s+(.*)%", // $line_regex
                'find_host_asn_owners', //$asn_table_name
                'owner_id', //$id_field_name
                'owner_name'
        );



    }

    /**
     * Import
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function import(
    $import_file, //file path for file to import
            $line_regex, //regex pattern to catch each line and column
            $asn_table_name, //table name
            $id_field_name, //`owner_id`,`owner_name`
            $value_field_name, //`owner_id`,`owner_name`
            $id_field_name_callback=null, //callback to operate on $id_field_name value
            $value_field_name_callback=null //callback to operate on $value_field_name value
    ) {


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
        $fh = fopen( $import_file, 'r' );

        /*
         * Read Each Line for Input
         * 
         * 
         */
        while ( $line = fgets( $fh ) ) {
            $count++;
            $match = array();

            if ( preg_match( $line_regex, $line, $match ) ) {

                if ( !is_null( $id_field_name_callback ) && is_callable( $id_field_name_callback ) ) {
                    $col1 = $this->DB->escape( call_user_func( $id_field_name_callback, $match[ 1 ] ) );
} else {
                    $col1 = $this->DB->escape( $match[ 1 ] );
}

                if ( !is_null( $id_field_name_callback ) && is_callable( $id_field_name_callback ) ) {
                    $col2 = $this->DB->escape( call_user_func( $id_field_name_callback, $match[ 2 ] ) );
} else {
                    $col2 = $this->DB->escape( $match[ 2 ] );
}


                $asn_table[] = " ('{$col1}', '{$col2 }') ";



    }



            /*
             * Limit The Insert ( use for testing only ) 
             */
            if (
                    $this->MAX_ENABLED && $count >= $this->MAX_ROWS ) {

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
        $this->DB->query( 'TRUNCATE TABLE `' . $asn_table_name . '`' );

        /*
         * Increase mysql buffer limits or will get errors
         */

        $this->DB->query( 'set global net_buffer_length=1000000' ); //http://stackoverflow.com/a/722656/3306354
        $this->DB->query( 'set global max_allowed_packet=1000000000' );


        /*
         * Bulk Insert Values Into Table
         * This is *much* faster - about 1 minute compared to approx 1+ hours using individual inserts within a loop
         * ref:http://stackoverflow.com/a/16105520/3306354
         */
        $values = implode( " , ", $asn_table );
        $dbquery = "INSERT IGNORE INTO " . $asn_table_name . " (`" . $id_field_name . "`,`" . $value_field_name . "`)  VALUES {$values} ; ";
//echo $dbquery;
        $this->DB->query( $dbquery );

        echo("\n<br>Import Successfull");

    }
    /**
     *  Get File From Web
     *
     * Wrapper around curl to force overwrite
     * wget does not work - no way to force an overwrite.
     *
     * @param none
     * @return void
     */
    public function getFileFromWeb( $url,$file ) {
                         $command="curl   " . $url . ' > ' . $file ;
       //     echo $command;
            exec($command );   

    }


}
?>