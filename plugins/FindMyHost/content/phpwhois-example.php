<?php
/*
Whois.php        PHP classes to conduct whois queries

Copyright (C)1999,2005 easyDNS Technologies Inc. & Mark Jeftovic

Maintained by David Saez
Modified by Andrew Druffner for use in Downcast
 * 
 * Downcast Usage: Set FormAction to



For the most recent version of this package visit:

http://www.phpwhois.org

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

//header('Content-Type: text/html; charset=UTF-8');



/*
 * Downcast Notes:
 * File has been modified to work with the Downcast Framework
 * 
 * To See this in action, set $this->PHPWHOIS_DEMO=true in FindMyHost.php and 
 * submit the form-phpwhois-example.
 */




$out='{results}';
$resout='{result}';
$_GET=$_POST;
$_GET['query']=$_GET['whois_query'];


/*
 * To get ip to print out everything:
 * $ip=gethostbyname("seoplani.com");
 * $_GET['query']=$ip;

 * $whois->deep_whois=true ( CHECK 'fast' checkbox)
 * 
 */



if (isset($_GET['query']))
	{
	$query = $_GET['query'];

	if (!empty($_GET['output']))
		$output = $_GET['output'];
	else
		$output = '';
//commented out to work with downcast . 
	//include_once('src/whois.main.php');
	//include_once('src/whois.utils.php');

	$whois = $this->whois();
	// Set to true if you want to allow proxy requests
	$allowproxy = false;

 	// get faster but less acurate results
 	$whois->deep_whois = empty($_GET['fast']);


         // To use special whois servers (see README)
	//$whois->UseServer('uk','whois.nic.uk:1043?{hname} {ip} {query}');
	//$whois->UseServer('au','whois-check.ausregistry.net.au');

	// Comment the following line to disable support for non ICANN tld's
	$whois->non_icann = true;

	$result = $whois->Lookup($query);
        
 




	$resout = str_replace('{query}', $query, $resout);
	$winfo = '';

	switch ($output)
		{
		case 'object':
			if ($whois->Query['status'] < 0)
				{
				$winfo = implode($whois->Query['errstr'],"\n<br></br>");
				}
			else
				{
				$utils = $this->utils();
				$winfo = $utils->showObject($result);
				}
			break;

		case 'nice':
			if (!empty($result['rawdata']))
				{
				$utils = $this->utils();
				$winfo = $utils->showHTML($result);
				}
			else
				{
				if (isset($whois->Query['errstr']))
					$winfo = implode($whois->Query['errstr'],"\n<br></br>");
				else
					$winfo = 'Unexpected error';
				}
			break;

		case 'proxy':
			if ($allowproxy)
				exit(serialize($result));

	  	default:
			if(!empty($result['rawdata']))
				{
				$winfo .= '<pre>'.implode($result['rawdata'],"\n").'</pre>';
				}
			else
				{
				$winfo = implode($whois->Query['errstr'],"\n<br></br>");
				}
		}

	$resout = str_replace('{result}', $winfo, $resout);
	}
else
	$resout = '';

$out = str_replace('{ver}',$whois->CODE_VERSION,$out);

echo '<strong>WhoIs Query Results</strong>';



echo (str_replace('{results}', $resout, $out));


/*
 * 
 */
$ip=gethostbyname($_GET['query']);




$result = $whois->Lookup($ip);
echo '<strong>IP Results for '.$ip.'</strong><pre>', print_r( $result, true ), '</pre>';



//-------------------------------------------------------------------------

function extract_block (&$plantilla,$mark,$retmark='')
{
$start = strpos($plantilla,'<!--'.$mark.'-->');
$final = strpos($plantilla,'<!--/'.$mark.'-->');

if ($start === false || $final === false) return;

$ini = $start+7+strlen($mark);

$ret=substr($plantilla,$ini,$final-$ini);

$final+=8+strlen($mark);

if ($retmark===false)
	$plantilla=substr($plantilla,0,$start).substr($plantilla,$final);
else	
	{
	if ($retmark=='') $retmark=$mark;
	$plantilla=substr($plantilla,0,$start).'{'.$retmark.'}'.substr($plantilla,$final);
	}
	
return $ret;
}
?>