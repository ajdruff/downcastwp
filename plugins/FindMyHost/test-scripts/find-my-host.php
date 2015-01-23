<?php
//
//$nserver="my.domain.me.com";
//$nserver_array=array_reverse(explode('.',$nserver));
//array_pop($nserver_array);
//$nserver_array=array_reverse($nserver_array);
//$nserver=implode('.',$nserver_array);
//
//echo "\n";
//echo '<pre>';
//print_r($nserver_array);
//
//echo '</pre>';
//echo "\n" . $nserver . "\n";
//$nserver="domain.me.com";
//$nserver_array=explode('.',$nserver);
//array_shift($nserver_array);
//#array_pop($nserver_array);
//#$nserver_array=array_reverse($nserver_array);
//$nserver=implode('.',$nserver_array);
//
//echo "\n";
//echo '<pre>';
//print_r($nserver_array);
//
//echo '</pre>';
//echo "\n" . $nserver . "\n";
//
//exit();




/* load wordpress so we can access its database for storing results */
include('nstockWP-static.class.php');
nstockWPS::load_wordpress();

$accepted_tlds = array('com', 'net', 'org');
$wait_throttle_min = '';
$wait_throttle_min = 12; //seconds between dns lookup , based on advice from http://www.domainpunch.com/kb/whoislimits.php (minimum 12 seconds). although i've never had a problem starting this at 10)
$wait_throttle_max = 20; //seconds between dns lookup
$window_start = 1;
$row = 0;
$max_rows = 12984;
$domain_hosts = array();
$array_domains = array();
if (($handle = fopen(dirname(__FILE__) . '/../files/980k-1m.csv', "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($row > $window_start + $max_rows) {
            break;    /* You could also write 'break 1;' here. */
        }

        $num = count($data);
        //echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;
        if (($row >= $window_start)) {


            for ($c = 0; $c < $num; $c++) {
                //   echo $data[$c] . "<br />\n";
                $array_domains[] = $data[$c];
            }
        }
    }
    fclose($handle);
}
//echo "<pre>";
//print_r($array_domains);
//echo "</pre>";
//exit();

//$array_domains = array('zalman-eu.com', 'net4me.net');
include(dirname(__FILE__) . '/../libs/phpWhois/whois.main.php');
include(dirname(__FILE__) . '/../libs/phpWhois/whois.ip.php');
$counter = $window_start;
//error_reporting(0);






    $clues = get_host_clues('getthathost.com');
    echo 'Clues<pre>', print_r( $clues, true ), '</pre>';
    die('exiting on line ' . __LINE__);
    exit();
    
    
    
foreach ($array_domains as $domain) {
    $counter++;
    //scrub domain - only accept com, net, org
    // $parts=parse_url($domain);
    //  print_r ($parts);
    $parts = explode('.', $domain);

    $tld = end($parts);
    echo "\n" . $tld;
    if (!in_array($tld, $accepted_tlds, true)) {
        echo "\n skipping $domain, we only do .com, .net, and .org! ";
        continue;
    }

    $clues = array();
    $wait_throttle = rand($wait_throttle_min, $wait_throttle_max);



    $clues = get_host_clues($domain);
    die('exiting on line ' . __LINE__);
    exit();
    
    
    $hosting_company = get_hosting_company($clues);



    $domain_hosts[$domain] = $hosting_company;
    echo "\n #$counter '$domain'=> '$hosting_company'" . ' waiting ' . $wait_throttle . 'seconds...';

    /* add to database */
    $dbquery = 'Insert into speedtmh_hostlookups (domain,org_name,name_server,name_server_domain,network_name0,network_handle0,network_name1,network_handle1,network_status0,network_status1,ip,org_id,webhost_guess) '
            . 'VALUES '
            . "('"
            . $domain . "'"
            . ",'" . $clues['org_name'] . "'"
            . ",'" . $clues['name_server'] . "'"
            . ",'" . $clues['name_server_domain'] . "'"
            . ",'" . $clues['network_name0'] . "'"
            . ",'" . $clues['network_handle0'] . "'"
           . ",'" . $clues['network_name1'] . "'"
            . ",'" . $clues['network_handle1'] . "'"
           . ",'" . $clues['network_status0'] . "'"
            . ",'" . $clues['network_status1'] . "'"
            . ",'" . $clues['ip'] . "'"
            . ",'" . $clues['org_id'] . "'"
            . ",'" . $hosting_company . "'"
            . ") "
            . ";";

//echo $dbquery;exit();

    $wpdb->get_results($dbquery, OBJECT);

//domain              varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//org_name           varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//name_server_domain  varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//network_name        varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//network_handle      varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//ip                  varchar(255)  latin1_swedish_ci  YES             (NULL)                   select,insert,update,references
//org_id

    sleep($wait_throttle); //throttle
}
echo "<pre>";
print_r($domain_hosts);
echo "</pre>";
exit();


/*
 *
 * Extract a few 'clues' from the whois data and return them as an array
 * these clues will be used later to guess the hosting company
 *
 */

function get_host_clues($domain) {

    #init
    $clues = array();
    $clues['org_name'] = '';
    $clues['org_id'] = '';
    $clues['network_name0'] = '';
    $clues['network_handle0'] = '';
        $clues['network_name1'] = '';
    $clues['network_status0'] = '';
    $clues['network_status1'] = '';
    $clues['network_handle1'] = '';
    $clues['name_server_domain'] = '';
    $clues['ip'] = '';

    $whois = new Whois();
    $ip = gethostbyname($domain);
    $clues['ip'] = $ip;
    $whois->deep_whois = true;


    /*
     * IP Query
     * First, look at what we can find out form the IP
     *
     */
    



        //testing only
   if (false){ 
if (isset($argv[1])){
	$domain = $argv[1];
}


$result = $whois->Lookup($domain);


echo '<pre>', print_r( $result, true ), '</pre>';

echo '<br> Now try with ip ' . $ip;
$result = $whois->Lookup($ip);


echo '<pre>', print_r( $result, true ), '</pre>';

exit();
   }
   
   
   // $whois_results = $whois->Lookup($ip); //removed for testing
   $whois_results = $whois->Lookup($ip);
   
   echo '$whois_results<pre>', print_r( $whois_results, true ), '</pre>';
   
   
    /* if more than one owner is listed, just get the last one */

    echo '<pre>' , __LINE__;
    print_r($whois_results['regrinfo']['owner']);
    echo '</pre>';



    if (isset($whois_results['regrinfo']['owner'][1])) {
        $clues['org_name'] = getAsString($whois_results['regrinfo']['owner'][1]['organization'],false);
        if (isset($whois_results['regrinfo']['owner'][1]['handle'])) {
            $clues['org_id'] = getAsString($whois_results['regrinfo']['owner'][1]['handle'],false);
        }
    } elseif (isset($whois_results['regrinfo']['owner']['organization'])) {

        $clues['org_name'] = getAsString($whois_results['regrinfo']['owner']['organization'],false);




        if (isset($whois_results['regrinfo']['owner']['handle'])) {
            $clues['org_id'] = getAsString($whois_results['regrinfo']['owner']['handle'],false);
        }
    } else {
        $clues['org_name'] = 'unknown';
        $clues['org_id'] = 'unknown';
    }


/*
 *
 * Save the network owner names and handles
 *
 */

            if (isset($whois_results['regrinfo']['network'][1]['handle'])) {
            $clues['network_handle1'] = $whois_results['regrinfo']['network'][1]['handle'];
            $clues['network_handle0'] = $whois_results['regrinfo']['network'][0]['handle'];

        }elseif (isset($whois_results['regrinfo']['network']['handle'])) {
            $clues['network_handle0'] = $whois_results['regrinfo']['network']['handle'];

        }
            if (isset($whois_results['regrinfo']['network'][1]['name'])) {
            $clues['network_name1'] = $whois_results['regrinfo']['network'][1]['name'];
            $clues['network_name0'] = $whois_results['regrinfo']['network'][0]['name'];
        }elseif (isset($whois_results['regrinfo']['network']['name'])) {
            $clues['network_name0'] = $whois_results['regrinfo']['network']['name'];

        }

                    if (isset($whois_results['regrinfo']['network'][1]['status'])) {
            $clues['network_status1'] = $whois_results['regrinfo']['network'][1]['status'];
            $clues['network_status0'] = $whois_results['regrinfo']['network'][0]['status'];
        }elseif (isset($whois_results['regrinfo']['network']['status'])) {
            $clues['network_status0'] = $whois_results['regrinfo']['network']['status'];

        }



    $whois_results = $whois->Lookup($domain);

    if ((isset($whois_results['regrinfo']['domain']['nserver']))) {


        $nserver = key($whois_results['regrinfo']['domain']['nserver']);
$nserver_array=explode('.',$nserver);
array_shift($nserver_array);
#array_pop($nserver_array);
#$nserver_array=array_reverse($nserver_array);
$nserver_domain=implode('.',$nserver_array);

    } else {

        $nserver = 'unknown';
//    echo "<pre>";
//print_r($whois_results['regrinfo']['domain']['nserver']);
//echo('domain name server = ' . key($whois_results['regrinfo']['domain']['nserver']));
//echo "</pre>";
    }
    $clues['name_server_domain'] = $nserver_domain;
     $clues['name_server'] = $nserver;
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










    return($clues);
}

/*
 *
 * Guesses the hosting company based on clues from the clues provided by the whois lookups
 *
 */

function get_hosting_company($clues) {
    /* in production, this would be a database lookup */
    $nameservers = array(
        'hostgator.com' => 'HostGator'
        , 'bluehost.com' => 'Bluehost'
        , 'dreamhost.com' => 'Dreamhost'
        , 'webhostinghub.com' => 'Web Hosting Hub'
        , 'domaincontrol.com' => 'GoDaddy.com'
    );
    $nameserver = $clues['name_server_domain'];
    if (isset($nameservers[$nameserver])) {
        $hosting_company = $nameservers[$nameserver];
        return ($hosting_company);
    } else {


        return($clues['org_name']);
    }
}

