<?php



function whois($domain, $server) {

    // format input for the specific server
    if($server == 'whois.arin.net') {
        $domain = "n + $domain";
    }

    // connect and send whois query
    $connection = fsockopen($server, 43, $errno, $errstr, 30);
    $request = fputs($connection, $domain . "\r\n");

    if(!$connection OR !$request){
       return "Error $errno: $errstr.";
    }

    // get the whois data
    $data = '';
    while(!feof($connection)){
            $data .= fgets($connection);
    }

    fclose($connection);
    return trim($data);
}
$domain='livescience.com';
$ip=gethostbyname($domain);

echo '<pre>', print_r(  whois($ip, 'whois.arin.net'), true ), '</pre>';
exit();


/*
 * https://github.com/phpWhois/phpWhois
 */

/*
 * Define Root of 4.2.5 Library
 */
define('PHP_WHOIS_ROOT',dirname(dirname(__FILE__)). '/libs/phpWhois');

	include_once(PHP_WHOIS_ROOT . '/src/whois.main.php');
	 include_once(PHP_WHOIS_ROOT . '/src/whois.utils.php');


$whois = new utils();
$domain = 'example.com';
$domain = 'livescience.com';
$inferred_ip=gethostbyname($domain);
$ip='62.97.102.115';

$result = $whois->Lookup($domain,false);
echo "Domain Lookup for " .$domain .'<pre>', print_r( ($result), true ), '</pre>';



$result = $whois->Lookup($inferred_ip,false);
echo "Ip Lookup for " .$inferred_ip .'<br> domain name is ' . gethostbyaddr($inferred_ip).'<pre>', print_r( ($result), true ), '</pre>';


$result = $whois->Lookup('AS6315',false);
echo "ASN for " .' AS6315' .'<pre>', print_r( ($result), true ), '</pre>';





$result = $whois->Lookup($ip,false);
echo "Ip Lookup for " .$ip .'<pre>', print_r( ($result), true ), '</pre>';




?>