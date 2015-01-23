<?php

/**
* Example Form For phpWhois
*
* Based on the example.php and example.html form provided by the phpWhois library
 * 
 * Usage: 
 * add this with a addPage or WordPress shortcode
 * add a Forms handler for phpwhois_example action
*

* @package Downcast
 * @subpackage FindMyHost
* @author Andrew Druffner <andrew@nomstock.com>
* @copyright  2012 Andrew Druffner
* @license    http://www.php.net/license/3_01.txt  PHP License 3.01
* @filesource
*/


/*
 * Declare the form object with our Form's ID
 */

$form = new DowncastForm(
        "phpwhois_example", // form id , arbitrary string that is unique for the form
        false, //$ajax whether we want the form to use ajax. 
        'phpwhois_example'
);


$options = array(
    'hide_on_success' => false, //hides the form on success
    'collapse_on_hide' => true, //completely removes all form html from page when form is hidden);
    'reset_on_success' => false,
    "view" => new View_Inline,
    "labelToPlaceholder" => 0
);
$form->setAjaxOptions( $options );


/*
 * Add Form Elements
 */
//$form->addElement( new Element_HTML( '<legend>Login</legend>' ) );


$form->addElement( new Element_Textbox( "Domain, Ip , or ASN:", "whois_query", array(
    "value" => (isset( $_POST [ 'whois_query' ] ) ? $_POST [ 'whois_query' ] : 'example.com'),
    "class" => "input-xxlarge",
    "style" => "height: 40px;font-size: 36px;",
    "placeholder" => "http://problogger.com",
        // "append" => '<button class="btn-large btn-primary">Get That Host</button>'
) ) );
$form->addElement( new Element_HTML( '<button class="btn-block btn btn-success">WhoIs</button>' ) );
/*
 * Include IP 
 */

$user_options = array( "ip" => "Include IP WhoIS" );
//$element_options=array("value"=>(empty($_POST ['query_type'])?'':$_POST ['query_type'][0]));
$default_checked=array("ip");
$checked=(empty($_POST ['include_ip']))?array():$_POST ['include_ip'];
$element_options = array( "value" =>(empty($_POST))?$default_checked:$checked );

$form->addElement(
        new Element_Checkbox(
        "", //label
        "include_ip", //dom name
        $user_options, //values
        $element_options //element options set default values here :  array("value"=>array("ip","asn"));
        )
);



/*
 * Output
 */
$user_options = array( "default" => "Regular", "nice" => "Htmlized", "object" => "Object" );
$element_options = array( "value" => (isset( $_POST [ 'output' ] ) ? $_POST [ 'output' ] : 'default'), );
$form->addElement( new Element_Radio( "Output Format:", "output", $user_options, $element_options ) );

/*
 * Fast
 */
$user_options = array( "fast" => "Fast But Less Accurate, Uncheck for 'Deep' Whois Query" );
$element_options = array( "value" => (empty( $_POST [ 'fast' ] ) ? '' : $_POST [ 'fast' ][ 0 ]) );
$form->addElement( new Element_Checkbox( "", "fast", $user_options, $element_options ) );




$form->render();

if ( isset( $_POST[ 'form' ] ) ) {
    $FindMyHostPlugin = $form->downcast()->getPlugin( 'FindMyHost' );
    $FindMyHostPlugin->formActionWhois();
}
?>

