<?php

define( 'ASN_DIR', dirname( dirname( __FILE__ ) ) . '/libs/asn' );






//$ip = $_GET[ 'ip' ];


$domain = "oranges.com";
$ip = gethostbyname( $domain );



// Loading the asn table (netmask => asn).
// You want to cache this across requests!
if (false){
$asn_table = array();
$fh = fopen( ASN_DIR . '/data-raw-table', 'r' );
while ( $line = fgets( $fh ) ) {
   
    $match = array();
    if ( preg_match( "%([0-9.]+)/\d+\s+(\d+)%", $line, $match ) ) {
        $asn_table[ ip2long( $match[ 1 ] ) ] = $match[ 2 ];
    }

    }
}
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
    die('exiting');
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