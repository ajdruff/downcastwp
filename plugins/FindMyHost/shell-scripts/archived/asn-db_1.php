<?php

define( 'ASN_DIR', dirname( dirname( __FILE__ ) ) . '/libs/asn' );


ini_set('memory_limit', '-1'); //http://stackoverflow.com/a/7895598/3306354
$expand_buffer_sql='set global net_buffer_length=1000000;set global max_allowed_packet=1000000000;';

// Include ezSQL core
include_once (dirname( dirname( __FILE__ ) ) . '/libs/ezSQL/shared/ez_sql_core.php');

// Include ezSQL database specific component
include_once (dirname( dirname( __FILE__ ) ) . '/libs/ezSQL/mysql/ez_sql_mysql.php');

// Initialise database object and establish a connection
// at the same time - db_user / db_password / db_name / db_host
$ezSQL = new ezSQL_mysql(
        'getthathost_wp', //'db_user',
        '@v)f^3RW2G%W&m76', //'db_password',
        'getthathost_wp', //'db_name',
        'localhost' //db_host
);




//$ip = $_GET[ 'ip' ];


$domain = "oranges.com";
$ip = gethostbyname( $domain );


$count=0;

$count_max=5000;
$limit=false;
// Loading the asn table (netmask => asn).
// You want to cache this across requests!
if ( true ){
    $asn_table = array();
    $fh = fopen( ASN_DIR . '/data-raw-table', 'r' );
    while ( $line = fgets( $fh ) ) {
$count++;
        $match = array();
        if ( preg_match( "%([0-9.]+)/\d+\s+(\d+)%", $line, $match ) ) {

            $ip_long = ip2long( $match[ 1 ] );
            $asn = $match[ 2 ];
           //    $asn_table[ ip2long( $match[ 1 ] ) ] = $match[ 2 ];
    $asn_table[] = " ('{$ip_long}', '{$asn }') " ;

            /* add to database */
               if(false){
            $dbquery = 'Insert IGNORE  into find_host_asn_ip (`ip_long`,`asn`) '
                    . 'VALUES '
                    . "('"
                    . $ip_long . "'"
                    . ",'" . $asn . "'"
                    . ") "
                    . ";";
               }
//echo $dbquery;exit();

   //         $wpdb->get_results( $dbquery, OBJECT );


         //   @$ezSQL->query( $dbquery );

            

            
    }
    
             if ( $limit && $count>=$count_max) {break;
             
} 



    }
            
 $values = implode(" , ", $asn_table) ;
//$query = "INSERT INTO blogtags (blogid, tag) VALUES {$values} ; " ;
 
 
 
 
$dbquery = $expand_buffer_sql . "INSERT IGNORE INTO find_host_asn_ip_copy (`ip_long`,`asn`)  VALUES {$values} ; " ;

  $ezSQL->query( $dbquery );
  
  

exit();

$sql = "INSERT INTO email_list (R_ID, EMAIL, NAME) VALUES ".$values;
            $dbquery = 'Insert IGNORE  into find_host_asn_ip (`ip_long`,`asn`) '
                    . 'VALUES '
                    . "('"
                    . $ip_long . "'"
                    . ",'" . $asn . "'"
                    . ") "
                    . ";";
            
            

die('exiting' . __LINE__);
}


exit();
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