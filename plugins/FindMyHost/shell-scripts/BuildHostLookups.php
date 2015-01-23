<?php

/**
 * FindMyHost_BuildHostLookups
 *
 * Imports websites from file, looks up their hosts, and inserts host clues and guesses into lookup table
 * 
 * Usage:
 * 
 * See shell script build-host-lookups.php
 * 
 * 
 * @package FindMyHost_
 * @author Andrew Druffner <andrew@nomstock.com>
 * @copyright  2012 Andrew Druffner
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @filesource
 */
class FindMyHost_BuildHostLookups{

    public $DB = null;
    public $FIND_MY_HOST_PLUGIN = null;
    public $EZ_SQL_PLUGIN = null;

    /**
     * Configure
     *
     * Configure
     *
     * @param none
     * @return void
     */
    public function config() {
        $this->DOMAINS_FILE_ROW_START = 4434; //where to start in the import file

        $this->DOMAINS_FILE_MAX_ROWS = 12984; //maximum rows to read of import file

        $this->DOMAINS_FILE_PATH = $this->FIND_MY_HOST_PLUGIN->getRootDirectory() . '/shell-scripts/host-files/980k-1m.csv';

        $this->SECONDS_BEFORE_WHOIS_MIN = 12; //seconds between dns lookup , based on advice from http://www.domainpunch.com/kb/whoislimits.php (minimum 12 seconds). although i've never had a problem starting this at 10)
        $this->SECONDS_BEFORE_WHOIS_MAX = 20; //seconds between dns lookup

        $this->ACCEPTED_TLDS = array( 'com', 'net', 'org' ); //white list of tlds we'll accept when processing file
    }

    private $_downcast = null;

    /**
     * Short Description
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function downcast() {

        if ( is_null( $this->_downcast ) ) {
            include_once("../../../lib/downcast/Downcast.php");
            include_once("../../../lib/downcast/DowncastPlugin.php");
            /*
             * Create a new Plugin Object
             */

            $this->_downcast = new Downcast();
}
        return $this->_downcast;

    }

    public function __construct() {


        /*
         * Increase memory limits or will get errors
         */
        ini_set( 'memory_limit', '-1' ); //http://stackoverflow.com/a/7895598/3306354


        /*
         *
         * Include the Downcast Library
         *
         */
// prevent silly notices, notably errors stemming from referencing $_SERVER when running via command line
        ini_set( 'error_reporting', E_ERROR | E_WARNING | E_PARSE );



        $this->EZ_SQL_PLUGIN = $this->downcast()->getPlugin( 'ezSQL' );
        $this->FIND_MY_HOST_PLUGIN = $this->downcast()->getPlugin( 'FindMyHost' );
        $this->DB = $this->downcast()->getPlugin( 'ezSQL' )->db;


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

    /**
     * Get Domains From File
     *
     * Returns an array of domains from a text file
     * containing a domain on each row.
     *
     * @param none
     * @return array Hosts
     */
    public function getDomainsFromFile() {


        $row = 0;

        $array_domains = array();

        /*
         * Open File Handle of $this->DOMAINS_FILE_PATH
         * and read rows into $array_domains
         */
        if ( ($handle = fopen( $this->DOMAINS_FILE_PATH, "r" )) !== FALSE ) {
            while ( ($data = fgetcsv( $handle, 1000, "," )) !== FALSE ) {
                if ( $row > $this->DOMAINS_FILE_ROW_START + $this->DOMAINS_FILE_MAX_ROWS ) {
                    break;
        }

                $num = count( $data );

                $row++;
                if ( ($row >= $this->DOMAINS_FILE_ROW_START ) ) {


                    for ( $c = 0; $c < $num; $c++ ) {
                        /*
                         * Read Each Row into Array
                         */
                        $array_domains[] = $data[ $c ];
            }
        }
    }
            fclose( $handle );
}



        return $array_domains;
    }

    /**
     * Build Host Lookups
     *
     * Imports websites from file, looks up their hosts, and inserts host clues and guesses into a database table
     *
     * @param none
     * @return void
     */
    public function buildHostLookups() {


        /*
         * Initialize
         */

        $host_lookups_record = array(); //an array that contains all the fields and field values of the host record to be inserted into db
        $array_domains = array();
        $counter = $this->DOMAINS_FILE_ROW_START;


        /*
         * Import Domains
         */

        $array_domains = $this->getDomainsFromFile();




        foreach ( $array_domains as $domain ) {
            /*
             * Initialize Loop
             */
            $clues = array();

            $counter++;
            //scrub domain - only accept com, net, org
            // $parts=parse_url($domain);
            //  print_r ($parts);

            /*
             * Split out the Top Level Domain
             */
            $parts = explode( '.', $domain );

            $tld = end( $parts );



            /*
             * Skip Unacceptable Domains
             */
            if ( !in_array( $tld, $this->ACCEPTED_TLDS, true ) ) {
                echo "\n skipping $domain, we only do .com, .net, and .org! ";
                continue;
    }


            /*
             * Wait a Random Amount of Time Before Whois Request
             */
            $wait_throttle = rand( $this->SECONDS_BEFORE_WHOIS_MIN, $this->SECONDS_BEFORE_WHOIS_MAX );



            $clues = $this->FIND_MY_HOST_PLUGIN->getHostClues( $domain );

            $this->downcast()->debugLog( '$clues = ', $clues, false, false );

            /*
             * Get Hosting Company
             */

            $host_lookups_record = $clues;
            $host_lookups_record[ 'webhost_guess' ] = $this->FIND_MY_HOST_PLUGIN->getHostingCompanyGuess( $clues );

      

            echo "\n #$counter '$domain'=> '{$host_lookups_record[ 'webhost_guess' ]}'" . ' waiting ' . $wait_throttle . ' seconds...';

            /*
             * 
             * Add to Datbase
             * 
             */

            $fields = "`" . implode( "`,`", array_keys( $host_lookups_record ) ) . "`";
            $values = "'" . implode( "','", $host_lookups_record ) . "'";

            $this->downcast()->debugLog( '$fields = ', $fields, false, false );

            $this->downcast()->debugLog( '$values = ', $values, false, false );


            $dbquery = 'Insert into `find_host_hostlookups` ('
                    . $fields
                    . ') '
                    . 'VALUES '
                    . "("
                    . $values
                    . ") "
                    . ";";





            $this->DB->query( $dbquery );


            /*
             * Wait Random Time Before Next Whois
             */

            sleep( $wait_throttle ); //throttle




}



    }


}

?>