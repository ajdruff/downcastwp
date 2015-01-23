<?php

/**
 * FindMyHost Downcast Plugin
 *
 * Proprietary Plugin - Provides the core code for the GetThatHost.com functionality to find the org that runs the ip block of a given domain.
 * 
 * @package Downcast
 * @author Andrew Druffner <andrew@nomstock.com>
 * @copyright  2012 Andrew Druffner
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * 
 */
class FindMyHost extends DowncastPlugin {

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
        $this->PHP_WHOIS_EXAMPLE = false;





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

    /**
     * Short Description
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function formActionFindHost() {

        /*
         * Simulate response for testing
         */
        if ( false ){
            $response[ 'success' ] = true;
            $response[ 'success_message' ] = '<div class="alert alert-info">'
                    . 'simulated response for testing'
                    . '</div>';
//$response[ 'error_message' ] = 'Your html error response here';
            $response[ 'form' ] = $_POST[ 'form' ];
            $response_json = json_encode( $response );
            echo $response_json;

            if ( $this->downcast()->isAjax() ) {
                exit();
}
}

        /**
         * All Form Handlers must return a json response with the following
         *  variables
         * required:
         *   $response[ 'success' ] //boolean, true for success, false for failure
         *   $response[ 'form' ]=$_POST['form']; //the form's id so we can target multiple forms. 
         * optional:
         *  $response['success_message']//if not supplied, the form's default success message will be used, configurable using $form->setAjaxOptions
         *  $response['error_message'] //if not supplied, the form's default error message will be used, configurable using $form->setAjaxOptions
         */
        $form = new DowncastForm();


        /*
         * Add validation rules
         * For examples of all available rules, see the validation section in the docs
         */
        $form->setValidationRule(
                'website', //field name to be validated
                array( $form, 'validateDomainName' ), //callback
                null, //paramaters
                'Not a valid  domain name' //error message
        );



        /*
         * add some code here to process $_POST variables
         * e.g.: validate user, return search results,etc.
         * when done, set $response['success'] to true or false 
         * depending on whether process was successful
         * 
         */


        /*
         * Apply Server Side Validation
         * This will automatically return errors to the form
         * if the submission violates any validation rules
         * 
         */
        $form->validateAjaxForm();

        $domain = $_POST[ 'website' ]; //may need to strip out http,etc.




        $clues = $this->getHostClues( $domain );
        $this->downcast()->debugLog( '$clues = ', $clues, false, false );
        $hosting_company = $this->getHostFromClues( $clues );




        /*
         * return correct json content type
         * //returning a json content type allows client to understand 
         * response as an object so it parses it correctly 
         * without having to parse it explicitly
         */


        $response[ 'success' ] = true;
        $response[ 'success_message' ] = '<div class="alert alert-info">'
                . '<br> Hosting Company: ' . $hosting_company
                . '</div>';
//$response[ 'error_message' ] = 'Your html error response here';
        $response[ 'form' ] = $_POST[ 'form' ];
        $response_json = json_encode( $response );
        echo $response_json;

        if ( $this->downcast()->isAjax() ) {
            exit();
}


    }

    /**
     * Database
     *
     * Returns the database property of the ezSQL Plugin
     *
     * @param none
     * @return object
     */
    public function db() {

//strange bug that if you add this to conditional, it silently takes down the website!
//so must define here
        $ezSQL_plugin = $this->downcast()->getPlugin( 'ezSQL' );
        if ( isset( $ezSQL_plugin ) ) {
            return $ezSQL_plugin->db;
} else {
            return null;

}
// if ( isset( $this->downcast()->getPlugin( 'ezSQL' ) ) ) {
// die('returning something');
//  return $this->downcast()->getPlugin( 'ezSQL' )->db;
//} else{
//  die('returning null');
// return null;
//}

    }

    /**
     * Get Host Clues
     *
     * Returns an array of clues that can help identify host
     *
     * @param string $domain The domain name
     * @return array
     */
    public function getHostClues( $domain, $include_asn=false,$use_rawdata = false ) {

        /*
         * Get IP and ASN
         */
        ini_set( 'error_reporting', E_ERROR | E_WARNING | E_PARSE );


        $ip = gethostbyname( $domain ); //if fail, will return input

        if ( $ip === $domain ) {

            $ip = '';


}


        /*
         * Domain Host Clues
         */


        $host_clues_domain = $this->_getHostCluesDomain( $domain, $use_rawdata );





        /*
         * IP Host Clues
         */


        $host_clues_ip = $this->_getHostCluesIp( $ip, $use_rawdata );

    

        /*
         * ASN Host Clues
         * ASN Makes things much slower and is not needed
         */

        if ( $include_asn ) {
            $host_clues_asn = $this->_getHostCluesAsn( $ip, $use_rawdata );     
}else {
    
     $host_clues_asn=array();
}
   


  


        $host_clues = array_merge( $host_clues_domain, $host_clues_ip, $host_clues_asn );





        return $host_clues;

//    echo "<pre>";
//print_r($clues);
//echo "</pre>";
//id                  int(11)       (NULL)             NO      PRI     (NULL)   auto_increment  select,insert,update,references
//domain              varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//org_name           varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//name_server_domain  varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//network_name        varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//network_handle      varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//ip                  varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//org_id        varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references



    }

    /*
     *
     * Extract a few 'clues' from the whois data and return them as an array
     * these clues will be used later to guess the hosting company
     *
     */

    protected function _getHostCluesAsn( $ip, $use_rawdata = false ) {

#init
        $clues = array();

        /*
         * 
         * ****************************
         *  asn
         *  Query : BGP (Border Gateway Protocol query based on http://quaxio.com/bgp/
         * ****************************
         * 
         */
        if ( $ip === '' ) {

            $asn = new stdClass();
            $asn->owner = 'none';
            $asn->number = 'none';

} else {

            $asn = $this->getAsn( $ip );

}




        $clues[ 'bgp_as_number' ] = $asn->number;
        $clues[ 'bgp_as_name' ] = $asn->owner;



        /*
         * ASN Query
         *
         *
         */

        $this->whois()->deep_whois = true;
        $whois_results = $this->whois()->Lookup( $asn->number );



        /*
         * 
         * ****************************
         * asn_org_name
         * 
         *  Query : ASN
         *  WhoIs: OrgName
         * ****************************
         * 
         */


        $clues[ 'asn_org_name' ] = $this->extractWhoisValue(
                $whois_results[ 'regrinfo' ][ 'owner' ], //$values, // e.g.: $whois_results[ 'regrinfo' ][ 'owner' ];
                'organization', // $index // the string that identifies the index in the $values array for which the desired value is associated e.g.: 'organization'
                true // $index // the string that identifies the index in the $values array for which the desired value is associated e.g.: 'organization'
        );



        /*
         * 
         * ****************************
         * asn_org_id
         * 
         *  Query : ASN
         *  WhoIs: OrgId
         * ****************************
         * 
         */

        $clues[ 'asn_org_id' ] = $this->extractWhoisValue(
                $whois_results[ 'regrinfo' ][ 'owner' ], //$values, // e.g.: $whois_results[ 'regrinfo' ][ 'owner' ];
                'handle' // $index // the string that identifies the index in the $values array for which the desired value is associated e.g.: 'organization'
        );



        /*
         * 
         * ****************************
         * asn_as_number
         * 
         *  Query : ASN
         *  WhoIs: ASNumber
         * ****************************
         * 
         */


        $clues[ 'asn_as_number' ] = (isset( $whois_results[ 'regrinfo' ][ 'domain' ][ 'name' ] )) ? $whois_results[ 'regrinfo' ][ 'domain' ][ 'name' ] : 'unknown'; //yes, domain/name hierarchy is correct

        /*
         * 
         * ****************************
         * asn_as_name
         * 
         *  Query : ASN
         *  WhoIs: ASName
         * ****************************
         * 
         */


        $clues[ 'asn_as_name' ] = (isset( $whois_results[ 'regrinfo' ][ 'AS' ][ 'name' ] )) ? $whois_results[ 'regrinfo' ][ 'AS' ][ 'name' ] : 'unknown';

        /*
         * 
         * ****************************
         * asn_as_handle
         * 
         *  Query : ASN
         *  WhoIs: ASHandle
         * ****************************
         * 
         */


        $clues[ 'asn_as_handle' ] = (isset( $whois_results[ 'regrinfo' ][ 'AS' ][ 'handle' ] )) ? $whois_results[ 'regrinfo' ][ 'AS' ][ 'handle' ] : 'unknown';



        /*
         * 
         * RETURN
         * 
         * If not processing raw data,return
         * 
         * 
         */

        if ( !$use_rawdata ) {
            return $clues;
}

        /*
         * 
         * ****************************
         * BEGIN RAW DATA PROCESSING
         * ****************************
         * 
         */

        $rawdata_array = ($use_rawdata) ? $this->rawDataToAssocArray( $whois_results[ 'rawdata' ] ) : array();



        /*
         * 
         * ****************************
         * asn_org_name
         * 
         *  Query : ASN
         *  WhoIs: rawdata/OrgName
         * ****************************
         * 
         */
        /*
         * Consider Multiple Orgs
         */

        if ( $clues[ 'asn_org_name' ] === 'unknown' ) {



            $clues[ 'asn_org_name' ] = (isset( $rawdata_array[ 'OrgName' ][ 1 ] )) ? $rawdata_array[ 'OrgName' ][ 1 ] : 'unknown';

            $clues[ 'asn_org_name' ] = ($clues[ 'asn_org_name' ] === 'unknown' && (isset( $rawdata_array[ 'OrgName' ] )) ) ? $rawdata_array[ 'OrgName' ] : 'unknown';

}



        /*
         * 
         * ****************************
         * asn_org_id
         * 
         *  Query : ASN
         *  WhoIs: rawdata/OrgId
         * ****************************
         * 
         */


        if ( $clues[ 'asn_org_id' ] === 'unknown' ) {



            $clues[ 'asn_org_id' ] = (isset( $rawdata_array[ 'OrgId' ][ 1 ] )) ? $rawdata_array[ 'OrgId' ][ 1 ] : 'unknown';

            $clues[ 'asn_org_id' ] = ($clues[ 'asn_org_name' ] === 'unknown' && (isset( $rawdata_array[ 'OrgId' ] )) ) ? $rawdata_array[ 'OrgId' ] : 'unknown';

}

        /*
         * 
         * ****************************
         * asn_as_number
         * 
         *  Query : ASN
         *  WhoIs: rawdata/ASNumber
         * ****************************
         * 
         */

        if ( $clues[ 'asn_as_number' ] === 'unknown' ) {



            $clues[ 'asn_as_number' ] = (isset( $rawdata_array[ 'ASNumber' ] )) ? $rawdata_array[ 'ASNumber' ] : 'unknown';

}

        /*
         * 
         * ****************************
         * asn_as_name
         * 
         *  Query : ASN
         *  WhoIs: rawdata/ASName
         * ****************************
         * 
         */



        if ( $clues[ 'asn_as_name' ] === 'unknown' ) {



            $clues[ 'asn_as_name' ] = (isset( $rawdata_array[ 'ASName' ] )) ? $rawdata_array[ 'ASName' ] : 'unknown';

}

        /*
         * 
         * ****************************
         * asn_as_handle
         * 
         *  Query : ASN
         *  WhoIs: rawdata/ASHandle
         * ****************************
         * 
         */


        if ( $clues[ 'asn_as_handle' ] === 'unknown' ) {



            $clues[ 'asn_as_handle' ] = (isset( $rawdata_array[ 'ASHandle' ] )) ? $rawdata_array[ 'ASHandle' ] : 'unknown';

}






        return $clues;


    }

    /*
     *
     * Extract a few 'clues' from the whois data and return them as an array
     * these clues will be used later to guess the hosting company
     *
     */

    protected function _getHostCluesDomain( $domain, $use_rawdata = false ) {

#init
        $clues = array();
        $clues[ 'nserver_domain' ] = '';
        $clues[ 'nserver' ] = "";
        $ip = gethostbyname( $domain );

        /*
         * ****************************
         * domain
         * Whois Query : Domain
         * WhoIs Field: name
         * ****************************
         * 
         */

        $clues[ 'domain' ] = $domain;
        /*
         * 
         * ****************************
         * ip
         *  Query : Domain
         *  WhoIs: N/A
         * ****************************
         * 
         */
        /*
         * when a domain is unregistered, its ip gets released
         * so a gethostbyname call will return the domain name isntead of the ip
         */
        if ( $ip === $domain ) {
            $clues[ 'ip' ] = 'released';
} else {
            $clues[ 'ip' ] = $ip;
}




        /*
         * Domain Query
         *
         */


        $this->whois()->deep_whois = true;
        $whois_results = $this->whois()->Lookup( $domain );




        /*
         * 
         * ****************************
         * nserver 
         *  Query : Domain
         *  WhoIs: nserver
         * ****************************
         * 
         */
        $clues[ 'nserver' ] = (isset( $whois_results[ 'regrinfo' ][ 'domain' ][ 'nserver' ] )) ? key( $whois_results[ 'regrinfo' ][ 'domain' ][ 'nserver' ] ) : 'unknown';


        /*
         * 
         * ****************************
         * nserver_domain
         * 
         *  Query : Domain
         *  WhoIs: nserver
         * ****************************
         * 
         */


        $nserver_array = explode( '.', $clues[ 'nserver' ] );
        array_shift( $nserver_array );

        $clues[ 'nserver_domain' ] = (isset( $whois_results[ 'regrinfo' ][ 'domain' ][ 'nserver' ] )) ? implode( '.', $nserver_array ) : 'unknown';
        /*
         * 
         * ****************************
         * registrar
         * 
         *  Query : Domain
         *  WhoIs: registrar
         * ****************************
         * 
         */
        $clues[ 'registrar' ] = (isset( $whois_results[ 'regyinfo' ][ 'registrar' ] )) ? $whois_results[ 'regyinfo' ][ 'registrar' ] : 'unknown';
        /*
         * 
         * ****************************
         * registrar_url
         * 
         *  Query : Domain
         *  WhoIs: rawdata/Registrar URL
         * ****************************
         * 
         */
        $clues[ 'registrar_url' ] = 'unknown'; //in rawdata only


        /*
         * 
         * ****************************
         * registrar_iana_id
         * 
         *  Query : Domain
         *  WhoIs: Raw Data/Registrar IANA ID
         * ****************************
         * 
         */
        $clues[ 'registrar_iana_id' ] = 'unknown'; //in rawdata only



        /*
         * 
         * ****************************
         * registrar_reseller
         * 
         *  Query : Domain
         *  WhoIs: Raw Data/Reseller
         * ****************************
         * 
         */


        $clues[ 'registrar_reseller' ] = 'unknown'; //in rawdata only



        /*
         * 
         * RETURN
         * 
         * If not processing raw data,return
         * 
         * 
         */

        if ( !$use_rawdata ) {
            return $clues;
}

        /*
         * 
         * ****************************
         * BEGIN RAW DATA PROCESSING
         * ****************************
         * 
         */

        $rawdata_array = ($use_rawdata) ? $this->rawDataToAssocArray( $whois_results[ 'rawdata' ] ) : array();

        /*
         * 
         * ****************************
         * registrar_url
         * 
         *  Query : Domain
         *  WhoIs: rawdata/Registrar URL
         * ****************************
         * 
         */

        if ( $clues[ 'registrar_url' ] === 'unknown' ) {
            $clues[ 'registrar_url' ] = (isset( $rawdata_array[ 'Registrar URL' ] )) ? $rawdata_array[ 'Registrar URL' ] : 'unknown';
}
        /*
         * 
         * ****************************
         * registrar_iana_id
         * 
         *  Query : Domain
         *  WhoIs: Raw Data/Registrar IANA ID
         * ****************************
         * 
         */

        if ( $clues[ 'registrar_iana_id' ] === 'unknown' ) {
            $clues[ 'registrar_iana_id' ] = (isset( $rawdata_array[ 'Registrar IANA ID' ] )) ? $rawdata_array[ 'Registrar IANA ID' ] : 'unknown';
}

        /*
         * 
         * ****************************
         * registrar_reseller
         * 
         *  Query : Domain
         *  WhoIs: Raw Data/Reseller
         * ****************************
         * 
         */
        /*
         * Reseller May Be an Array
         * With name, address,etc.
         * or it may just be a string
         */

        if ( $clues[ 'registrar_reseller' ] === 'unknown' ) {

            if ( is_array( $rawdata_array[ 'Reseller' ] ) ) {
                $clues[ 'registrar_reseller' ] = (isset( $rawdata_array[ 'Reseller' ][ 0 ] )) ? $rawdata_array[ 'Reseller' ][ 0 ] : 'none';

} else {

                $clues[ 'registrar_reseller' ] = (isset( $rawdata_array[ 'Reseller' ] )) ? $rawdata_array[ 'Reseller' ] : 'none';
}

}
        return($clues);






    }

    /*
     *
     * Extract a few 'clues' from the whois data and return them as an array
     * these clues will be used later to guess the hosting company
     *
     */

    protected function _getHostCluesIP( $ip, $use_rawdata = false ) {



#init
        $clues = array();

        $clues[ 'ip_org_name' ] = 'unknown';
        $clues[ 'ip_org_id' ] = 'unknown';
        $clues[ 'ip_net_name' ] = 'unknown';
        $clues[ 'ip_net_handle' ] = 'unknown';
        $clues[ 'ip_net_type' ] = 'unknown';
        if ( $ip === '' ) {
            return $clues;
}


        /*
         * IP Query
         *
         */
        $this->whois()->deep_whois = true;
        $whois_results = $this->whois()->Lookup( $ip );
        $this->downcast()->debugLog( '$whois_results = ', $whois_results, false, false );


        /*
         * 
         * ****************************
         *  ip_org_name
         *  Query : IP
         *  WhoIs: OrgName
         * ****************************
         * 
         */


        $clues[ 'ip_org_name' ] = $this->extractWhoisValue(
                $whois_results[ 'regrinfo' ][ 'owner' ], //$values, // e.g.: $whois_results[ 'regrinfo' ][ 'owner' ];
                'organization' // $index // the string that identifies the index in the $values array for which the desired value is associated e.g.: 'organization'
        );

        /*
         * sometimes its listed under 'name'
         */
        if (  $clues[ 'ip_org_name' ] ==='unknown' ) {
                $clues[ 'ip_org_name' ] = $this->extractWhoisValue(
                $whois_results[ 'regrinfo' ][ 'owner' ], //$values, // e.g.: $whois_results[ 'regrinfo' ][ 'owner' ];
                'name' // $index // the string that identifies the index in the $values array for which the desired value is associated e.g.: 'organization'
        );    
            
}


        /*
         * 
         * ****************************
         *  ip_org_id
         *  Query : IP
         *  WhoIs: Orgid
         * ****************************
         * 
         */
        //  $clues[ 'ip_org_id' ] = (isset( $whois_results[ 'regrinfo' ][ 'owner' ][ 'handle' ] )) ? $whois_results[ 'regrinfo' ][ 'owner' ][ 'handle' ] : 'unknown';

        $clues[ 'ip_org_id' ] = $this->extractWhoisValue(
                $whois_results[ 'regrinfo' ][ 'owner' ], //$values, // e.g.: $whois_results[ 'regrinfo' ][ 'owner' ];
                'handle' // $index // the string that identifies the index in the $values array for which the desired value is associated e.g.: 'organization'
        );

        /*
         * 
         * ****************************
         *  ip_net_name
         *  Query : IP
         *  WhoIs: NetName
         * ****************************
         * 
         */
        // $clues[ 'ip_net_name' ] = (isset( $whois_results[ 'regrinfo' ][ 'network' ][ 'name' ] )) ? $whois_results[ 'regrinfo' ][ 'network' ][ 'name' ] : 'unknown';
        $clues[ 'ip_net_name' ] = $this->extractWhoisValue(
                $whois_results[ 'regrinfo' ][ 'network' ], //$values, // e.g.: $whois_results[ 'regrinfo' ][ 'owner' ];
                'name' // $index // the string that identifies the index in the $values array for which the desired value is associated e.g.: 'organization'
        );


        /*
         * 
         * ****************************
         *  ip_net_handle
         *  Query : IP
         *  WhoIs: NetHandle
         * ****************************
         * 
         */
        //   $clues[ 'ip_net_handle' ] = (isset( $whois_results[ 'regrinfo' ][ 'network' ][ 'handle' ] )) ? $whois_results[ 'regrinfo' ][ 'network' ][ 'handle' ] : 'unknown';


        $clues[ 'ip_net_handle' ] = $this->extractWhoisValue(
                $whois_results[ 'regrinfo' ][ 'network' ], //$values, // e.g.: $whois_results[ 'regrinfo' ][ 'owner' ];
                'handle' // $index // the string that identifies the index in the $values array for which the desired value is associated e.g.: 'organization'
        );


        /*
         * 
         * ****************************
         *  ip_net_type
         *  Query : IP
         *  WhoIs: NetType
         * ****************************
         * 
         */
        //     $clues[ 'ip_net_type' ] = (isset( $whois_results[ 'regrinfo' ][ 'network' ][ 'status' ] )) ? $whois_results[ 'regrinfo' ][ 'network' ][ 'status' ] : 'unknown';
        $clues[ 'ip_net_type' ] = $this->extractWhoisValue(
                $whois_results[ 'regrinfo' ][ 'network' ], //$values, // e.g.: $whois_results[ 'regrinfo' ][ 'owner' ];
                'status' // $index // the string that identifies the index in the $values array for which the desired value is associated e.g.: 'organization'
        );

        /*
         * 
         * ****************************
         *  ip_origin_as
         *  Query : IP
         *  WhoIs: rawdata/OriginAS
         * ****************************
         * 
         */
        $clues[ 'ip_origin_as' ] = 'unknown'; //origin_as is not provided in query results - you must parse rawdata.



        /*
         * 
         * RETURN
         * 
         * If not processing raw data,return
         * 
         * 
         */

        if ( !$use_rawdata ) {
            return $clues;
}

        /*
         * 
         * ****************************
         * BEGIN RAW DATA PROCESSING
         * ****************************
         * 
         */

        $rawdata_array = ($use_rawdata) ? $this->rawDataToAssocArray( $whois_results[ 'rawdata' ] ) : array();


        /*
         * 
         * ****************************
         *  ip_org_name
         *  Query : IP
         *  WhoIs: rawdata/OrgName
         * ****************************
         * 
         */

        if ( $clues[ 'ip_org_name' ] === 'unknown' ) {
            $clues[ 'ip_org_name' ] = (isset( $rawdata_array[ 'OrgName' ] )) ? $rawdata_array[ 'OrgName' ] : 'unknown';
}


        /*
         * 
         * ****************************
         *  ip_org_id
         *  Query : IP
         *  WhoIs: rawdata/Orgid
         * ****************************
         * 
         */

        if ( $clues[ 'ip_org_id' ] === 'unknown' ) {
            $clues[ 'ip_org_id' ] = (isset( $rawdata_array[ 'Orgid' ] )) ? $rawdata_array[ 'Orgid' ] : 'unknown';
}

        /*
         * 
         * ****************************
         *  ip_net_name
         *  Query : IP
         *  WhoIs: rawdata/NetName
         * ****************************
         * 
         */
        if ( $clues[ 'ip_net_name' ] === 'unknown' ) {
            $clues[ 'ip_net_name' ] = (isset( $rawdata_array[ 'NetName' ] )) ? $rawdata_array[ 'NetName' ] : 'unknown';
}
        /*
         * 
         * ****************************
         *  ip_net_handle
         *  Query : IP
         *  WhoIs: rawdata/NetHandle
         * ****************************
         * 
         */
        if ( $clues[ 'ip_net_handle' ] === 'unknown' ) {
            $clues[ 'ip_net_handle' ] = (isset( $rawdata_array[ 'NetHandle' ] )) ? $rawdata_array[ 'NetHandle' ] : 'unknown';
}

        /*
         * 
         * ****************************
         *  net_type
         *  Query : IP
         *  WhoIs: rawdata/NetType
         * ****************************
         * 
         */
        if ( $clues[ 'ip_net_type' ] === 'unknown' ) {
            $clues[ 'ip_net_type' ] = (isset( $rawdata_array[ 'NetType' ] )) ? $rawdata_array[ 'NetType' ] : 'unknown';
}
        /*
         * 
         * ****************************
         *  origin_as
         *  Query : IP
         *  WhoIs: rawdata/OriginAS
         * ****************************
         * 
         */
        if ( $clues[ 'ip_origin_as' ] === 'unknown' ) {
            $clues[ 'ip_origin_as' ] = (isset( $rawdata_array[ 'OriginAS' ] )) ? $rawdata_array[ 'OriginAS' ] : 'unknown';
}


        return($clues);






    }

    /**
     * Get Asn
     *
     * Returns an object with 2 properties:
     * asn:  Autonomous System Number for a domain
     * owner: The block owner
     * @param string Ip address in dot notation
     * 
     * @author Alok Menghrajani http://quaxio.com/bgp/
     * @author Andrew Druffner
     * 
     * @return object
     */
    public function getAsn( $ip ) {



        $asn = new stdClass();

        $ip_long = ip2long( $ip );
        $ip2s = '';
        $all_ip2s = array();
        /*
         * asn Lookup
         */

        $result = false; //not found
        for ( $i = 0; $i < 32; $i++ )
{
            $ip2 = ($ip_long >> $i) << $i;
            $all_ip2s[] = $ip2;
            /*
             * Original Code 
             * Not used and instead replaced with a database Lookup
             * original code courtesy: http://quaxio.com/bgp/

              if ( false ){//this is replaced with a database Lookup
              if ( isset( $asn_table[ $ip2 ] ) ) {
              $asn = $asn_table[ $ip2 ];
              $result = $ip . ' maps to asn ' . $asn;
              if ( isset( $bgp_as_owners[ $asn ] ) ) {
              $result .= ' (' . $bgp_as_owners[ $asn ] . ')';
              }
              break;
              }




              }
             * 
             * 
             */


}




        /*
         * Remove 0's and nulls from ip2s  array
         */
        $all_ip2s_filtered = array_filter( $all_ip2s );

        /*
         * Turn ip2s array into string
         * so we can add it to query
         */
        $all_ip2s_filtered_string = implode( ',', $all_ip2s_filtered );



        /*
         * ASN Database Query
         * TODO: reformat for readability using concatenation and comments
         * this takes the first matching ip block record to find the asn. not sure why this works 
         * but is the database equivilent of what was in the original Menghrajani's algorythm
         * joins: http://stackoverflow.com/a/6035918/3306354
         */
        $asn_query = ('SELECT `table_bgp_ip`.`ip_long`,`table_bgp_ip`.`ip_long`,`table_bgp_owner`.`owner_id`,`table_bgp_owner`.`owner_name`
FROM ( SELECT `ip_long`,`asn`   FROM `find_host_bgp_ip` WHERE `ip_long` IN (
' . $all_ip2s_filtered_string . '
)) 
as `table_bgp_ip`

join 

`find_host_bgp_owner` as `table_bgp_owner`

on `table_bgp_ip`.`asn`=`table_bgp_owner`.`owner_id` order by `table_bgp_ip`.`ip_long` desc limit 1');



//echo (($asn_query)); //you cannot use debug for $super_query because it will reset the server for some reason.


        /*
         * Find All ASNs
         */


        $query_results = $this->db()->get_results( $asn_query );

//     echo '<pre>', print_r( $query_results, true ), '</pre>'; //must echo not debug or it will reset server


        if ( isset( $query_results[ 0 ] ) ) {

            $bgp_as_owner = $query_results[ 0 ]->owner_name;


            $asn_number = $query_results[ 0 ]->owner_id;

} else {
            $bgp_as_owner = 'unknown';
            $asn_number = 'unknown';

}

        $asn->owner = $bgp_as_owner;
        $asn->number = $asn_number;





        $this->CACHE_ASNS[ $ip ] = $asn; //cache in case there are multiple requests
        return $asn;

    }

    /*
     * this is a simple function that will return a string, even if given an array
     * useful since results occasionally return arrays when you expect strings
     *
     */

    function getAsString( $text, $full = false ) {

        if ( is_array( $text ) ) {
            if ( ($full ) ) {
                $result = implode( ' ', $text );
} else {

                $result = $text[ 0 ];
}
} else {

            $result = $text;

}
        return $result;
    }

    /**
     * Extract Whois Value
     *
     * Extract Value from a Whois result  in the form 
     * *[owner] => Array
      (
      [organization] => SoftLayer Technologies Inc.
      [handle] => SOFTL
      [address] => Array
      )
     * 
     * 
     *  OR 
     * 
     * 
     * 
     * [owner] => Array
      (
      [0] => Array
      (
      [organization] => SoftLayer Technologies Inc.
      [handle] => SOFTL
      [address] => Array
      (
      [street] => Array
      (
      [0] => 4849 Alpha Rd.
      )

      [city] => Dallas
      [state] => TX
      [pcode] => 75244
      [country] => US
      )

      )

      [1] => Array
      (
      [organization] => Dejarik
      )

      )
     * @param none
     * @return void
     */
    public function extractWhoisValue(
    $values, // e.g.: $whois_results[ 'regrinfo' ][ 'owner' ];
            $index, // the string that identifies the index in the $values array for which the desired value is associated e.g.: 'organization'
            $debug = false //whether to debug
    ) {

        $result = 'unknown';

        if ( $this->isAssoc( $values ) ) {

            $result = (isset( $values[ $index ] )) ? $values[ $index ] : 'unknown';



} else {

            foreach ( $values as $key => $value ) {


                $result = (isset( $value[ $index ] )) ? $value[ $index ] : "unknown";
                if ( $result !== "unknown" ) {
                    break; // if not found yet, continue to the next array
}


}




}


        return $result;
    }

    /**
     * Converts a WhoisRawData array to associative array
     *
     * Takes each line that looks like 'index here: value here', and converts
     * them to elements in an associative array
     *
     * @param none
     * @return void
     */
    private function rawDataToAssocArray( $rawdata_array ) {
        $rawdata_array = array_filter( $rawdata_array );
        $result = array();
        foreach ( $rawdata_array as $string ) {
            $string = trim( $string );
            if ( is_null( $string ) ) {
                continue;
}
            list($k, $v) = explode( ': ', $string );
            $k = trim( $k );
            $v = trim( $v );

            if ( isset( $result[ $k ] ) && (!is_array( $result[ $k ] )) ) {
                $result[ $k ] = array( $result[ $k ] );
                $result[ $k ][] = $v;
} else if ( is_array( $result[ $k ] ) ){
                $result[ $k ][] = $v;
} else{

                $result[ $k ] = $v;

}

}



        return $result;
    }

    /**
     * Public WhoIS Query Wrapper
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function formActionWhoisInternal() {



        $this->_formActionWhois(
                $_POST[ 'whois_query' ], //$query, //the query to be submitted to WhoIS . domain, IP, or ASN
                $_POST[ 'output' ], //$output, //the type of output 'nice','object', 'normal', 'json'
                true, //$ip_whois , //boolean, true to make a separate whois query using the ip.
                true, //$asn_whois, //boolean, true to make a separate whois query using asn.
                true, //$get_clues, //whether to display clues. this should never be public
                (!empty( $_POST[ 'clues_only' ] )) ? true : false, //$clues_only //true to display all clues and nothing else
                (!empty( $_POST[ 'rawdata_assoc' ] )) ? true : false //$rawdata_assoc //true to show raw data as an associative array                
         );


    }

    /**
     * Public WhoIS Query Wrapper
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function formActionWhois() {

        if ( $this->PHP_WHOIS_EXAMPLE ) {

            echo '<div><strong>(PHP Whois Example Form) </strong></div>';
            include('content/phpwhois-example.php');
            return;

}


        $this->_formActionWhois(
                $_POST[ 'whois_query' ], //$query, //the query to be submitted to WhoIS . domain, IP, or ASN
                $_POST[ 'output' ], //$output, //the type of output 'nice','object', 'normal', 'json'
                (!empty( $_POST[ 'include_ip' ] )) ? true : false, //$ip_whois , //boolean, true to make a separate whois query using the ip.
                false, //$asn_whois, //boolean, true to make a separate whois query using asn.
                false, //$get_clues, //whether to display clues. this should never be public
                false, //$clues_only //true to display all clues and nothing else
                false //$rawdata_assoc //true to show raw data as an associative array
        );
    }

    /**
     * Form Action - PhpWhois Form Handler (Private)
     *
     * Use a wrapper around this to choose type of query and results to display.
     *
     * @param none
     * @return void
     */
    private function _formActionWhois(
    $query, //the query to be submitted to WhoIS . domain, IP, or ASN
            $output, //the type of output 'nice','object', 'normal', 'json'
            $ip_whois, //boolean, true to make a separate whois query using the ip.
            $asn_whois, //boolean, true to make a separate whois query using asn.
            $get_clues, //whether to display clues. this should never be public
            $clues_only, //true to display all clues and nothing else
            $rawdata_assoc, //true to show raw data as an associative array
            $debug = false //whether to leave debug settings alone . set this to true when debugging,otherwise we will screen out warning       
    ) {
        /*
         * 
         * Initialize 
         * 
         */
        $tags[ 'HOST_CLUES' ] = "";
        $tags[ 'DOMAIN_WHOIS' ] = "";
        $tags[ 'DOMAIN_RAW_DATA' ] = "";
        $tags[ 'IP_WHOIS' ] = "";
        $tags[ 'IP_RAW_DATA' ] = "";
        $tags[ 'ASN_WHOIS' ] = "";
        $tags[ 'ASN_RAW_DATA' ] = "";

        $output_template = ""
                . " <div><strong>Host Clues </strong></div>"
                . "<div>{HOST_CLUES}</div>"
                . " <div><strong>Domain Whois </strong></div>"
                . "<div>{DOMAIN_WHOIS}</div>"
                . "<div>{DOMAIN_RAW_DATA}</div>"
                . " <div><strong>IP Whois </strong></div>"
                . "<div>{IP_WHOIS}</div>"
                . "<div>{IP_RAW_DATA}</div>"
                . " <div><strong>ASN Whois </strong></div>"
                . "<div>{ASN_WHOIS}</div>"
                . "<div>{ASN_RAW_DATA}</div>"
                . "";

        $host_clues_only_template = ""
                . " <div><strong>Host Clues </strong></div>"
                . "<div>{HOST_CLUES}</div>"
                . "";


        if ( !$debug ) {

            ini_set( 'error_reporting', E_ERROR );

}


        $this->downcast()->debugLog( '$_POST = ', $_POST, false, false );




        /*
         * Whois Options
         */


        $allowproxy = false; //true if you want to allow proxy requests

        $this->whois()->deep_whois = empty( $_POST[ 'fast' ] ); // true for slow and accurate, false for fast but less accurate        

        $this->whois()->non_icann = true; //true for support for non ICANN tld's       

        /*
         * HOST_CLUES
         * 
         */

        if ( $get_clues ) {

            $clues = $this->getHostClues( $query );

            $tags[ 'HOST_CLUES' ] = '<pre>' . print_r( $clues, true ) . '</pre>';





            if ( $clues_only ) {
                /*
                 * Output Clues Only
                 */
                echo $this->downcast()->crunchTpl( $tags, $host_clues_only_template );
                return;

}

}


        /*
         * DOMAIN_WHOIS
         * 
         */

        $result = $this->whois()->Lookup( $query );

        $tags[ 'DOMAIN_WHOIS' ] = $this->renderWhois(
                $result, $output, $rawdata_assoc, $allowproxy  //whois output
        );


        $tags[ 'DOMAIN_RAW_DATA' ] = ($rawdata_assoc) ? '<div><em>Domain Whois Raw Data </em></div><pre>' . print_r( $this->rawDataToAssocArray( $result[ 'rawdata' ] ), true ) . '</pre>' : '';



        /*
         * 
         * IP Query
         * 
         */

        if ( $ip_whois ) {
            $ip = gethostbyname( $query );



            $result = $this->whois()->Lookup( $ip, false );


            $tags[ 'IP_WHOIS' ] = $this->renderWhois(
                    $result, $output, $rawdata_assoc, $allowproxy  // whois output
            );



            $tags[ 'IP_RAW_DATA' ] = ($rawdata_assoc) ? '<div><em>IP Whois Raw Data </em></div><pre>' . print_r( $this->rawDataToAssocArray( $result[ 'rawdata' ] ), true ) . '</pre>' : '';


}


        /*
         * 
         * ASN Query
         * 
         */

        if ( $asn_whois ) {
            $ip = gethostbyname( $query );
            $asn = $this->getAsn( $ip );

            $result = $this->whois()->Lookup( $asn->number ); //



            $tags[ 'ASN_WHOIS' ] = $this->renderWhois(
                    $result, $output, $rawdata_assoc, $allowproxy  // whois output
            );


            $tags[ 'ASN_RAW_DATA' ] = ($rawdata_assoc) ? '<div><em>ASN Whois Raw Data </em></div><pre>' . print_r( $this->rawDataToAssocArray( $result[ 'rawdata' ] ), true ) . '</pre>' : '';






}


        echo $this->downcast()->crunchTpl( $tags, $output_template );





    }

    protected $_whois = null;
    protected $_whois_utils = null;

    /**
     * Whois Object
     *
     * Returns a Whois Object
     *
     * @param none
     * @return void
     */
    public function whois() {
        if ( is_null( $this->_whois ) ) {
            include_once($this->getRootDirectory() . '/libs/phpWhois/src/whois.main.php');
            $this->_whois = new Whois();
}

        return $this->_whois;
    }

    /**
     * Whois Utility Object
     *
     * Returns a Whois Utility Object
     *
     * @param none
     * @return void
     */
    public function utils() {
        if ( is_null( $this->_whois_utils ) ) {
            include_once($this->getRootDirectory() . '/libs/phpWhois/src/whois.main.php');
            include_once($this->getRootDirectory() . '/libs/phpWhois/src/whois.utils.php');

            $this->_whois_utils = new utils();
}
        return $this->_whois_utils;
    }

    /**
     * Render Whois
     *
     * Returns text or html of a whois result
     *
     * @author 
     * @author <andrew@nomstock.com>
     * @param none
     * @return void
     */
    public function renderWhois( $result, $output = 'default', $rawdata_assoc = false, $allowproxy = false ) {
        $winfo = '';

        switch ( $output )
{
            case 'object':



                if ( $this->whois()->Query[ 'status' ] < 0 )
{
                    $winfo = implode( $this->whois()->Query[ 'errstr' ], "\n<br></br>" );
} else
{

                    $winfo = $this->utils()->showObject( $result );

}
                break;
            case 'array':



                if ( $this->whois()->Query[ 'status' ] < 0 )
{
                    $winfo = implode( $this->whois()->Query[ 'errstr' ], "\n<br></br>" );
} else
{



                    $winfo = '<pre>' . print_r( $result, true ) . '</pre>';


//   $winfo = $this->utils()->showObject( $result );

}
                break;

            case 'nice':
                if ( !empty( $result[ 'rawdata' ] ) )
{

                    $winfo = $this->utils()->showHTML( $result );

} else
{
                    if ( isset( $this->whois()->Query[ 'errstr' ] ) )
                        $winfo = implode( $this->whois()->Query[ 'errstr' ], "\n<br></br>" );
                    else
                        $winfo = 'Unexpected error';
}
                break;

            case 'proxy':
                if ( $allowproxy )
                    exit( serialize( $result ) );

            default:
                if ( !empty( $result[ 'rawdata' ] ) )
{
                    $winfo .= '<pre>' . implode( $result[ 'rawdata' ], "\n" ) . '</pre>';
} else
{
                    $winfo = implode( $this->whois()->Query[ 'errstr' ], "\n<br></br>" );
}
}





        return $winfo;

    }

    /*
     * Usage: To include headers, each element in array needs to be an associative array
     */

    public function array2Html( $array, $table = true )
    {
        $out = '';
        foreach ( $array as $key => $value ) {
            if ( is_array( $value ) ) {
                if ( !isset( $tableHeader ) ) {
                    $tableHeader = '<th>' .
                            implode( '</th><th>', array_keys( $value ) ) .
                            '</th>';
}
                array_keys( $value );
                $out .= '<tr>';
                $out .= $this->array2Html( $value, false );
                $out .= '</tr>';
} else {
                $out .= "<td>$value</td>";
}
}

        if ( $table ) {
            return '<table>' . $tableHeader . $out . '</table>';
} else {
            return $out;
}


        /* TODO: write  a vertical header array2html 
         * 
          <table>
          <tbody>
          <tr>
          <th>ip</th>
          <th>domain</th>
          </tr>
          <tr>
          <td>1.2.3.4</td>
          <td>mydomain.com</td>
          </tr>
          <tr>
          <td>1.2.3.4</td>
          <td>mydomain.com</td>
          </tr>
          </tbody>
          </table>

          <table">
          <caption>First Way</caption>
          <tr>
          <th>Header 1</th>
          <td>data</td><td>data</td><td>data</td>
          </tr>
          <tr>
          <th>Header 2</th>
          <td>data</td><td>data</td><td>data</td>
          </tr>
          <tr>
          <th>Header 2</th>
          <td>data</td><td>data</td><td>data</td>
          </tr>
          </table>
         */



    }

    /**
     * Get Hosting Company Guess
     *
     * Derives Hosting Company from Clues Array
     *
     * @param none
     * @return void
     */
    public function getHostingCompanyGuess( $clues ) {
        /* in production, this would be a database lookup */
        $nameservers = array(
            'hostgator.com' => 'HostGator'
            , 'bluehost.com' => 'Bluehost'
            , 'dreamhost.com' => 'Dreamhost'
            , 'webhostinghub.com' => 'Web Hosting Hub'
            , 'domaincontrol.com' => 'GoDaddy.com'
        );

        if ( isset( $nameservers[ $clues[ 'nserver_domain' ] ] ) ) {
            $hosting_company = $nameservers[ $clues[ 'nserver_domain' ] ];
            return ($hosting_company);
} else {


            return($clues[ 'ip_org_name' ]);
}
    }

    /**
     *  Get Host From Clues
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function getHostFromClues( $clues ) {

        $query = "select `display_name` from (
select `webhosts`.* , `whois`.`nserver_domain`,`whois`.`ip_org_name`,`whois`.`ip_org_id` from 
`find_host_webhosts` webhosts
inner join `find_host_webhost_org_nserver` `whois`
on `webhosts`.`webhost_id`=`whois`.`webhost_id`
) table_c
where `ip_org_name` = '" . $clues[ 'ip_org_name' ] . "'
and `nserver_domain`='" . $clues[ 'nserver_domain' ] . "'
and `ip_org_id`='" . $clues[ 'ip_org_id' ] . "';";



        $query_results = $this->db()->get_row( $query,ARRAY_A );
  

        if ( !isset( $query_results[ 'display_name' ] ) ) {
            $hosting_company = $clues[ 'ip_org_name' ];
} else {
            $hosting_company = $query_results[ 'display_name' ];

}
        return $hosting_company;

    }

    /**
     * Is Associative Array
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function isAssoc( $arr ) {

        return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );


    }
}

?>