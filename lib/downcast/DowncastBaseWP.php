<?php

include_once ("DowncastBase.php");

/**
* DowncastBaseWp (Parent Class)
*
* Manages the Downcast Web Framework when used within a WordPress plugin
* DowncastBaseWP should be limited to overriding those methods in DowncaseBase that are not compatible with WordPress
 * All other WordPress specific functionality should go in DowncastWP 
 * 
* @package Downcast
* @author Andrew Druffner <andrew@nomstock.com>
* @copyright  2012 Andrew Druffner
* @license    http://www.php.net/license/3_01.txt  PHP License 3.01
* @filesource
*/
class DowncastBaseWP extends DowncastBase {


/**
* Wrapper around _wpAddCssAndJs so we can call it via the correct WordPress hook
*
* This is a bit tricky - we are overriding the parent method so we can call the parent method with a hook
* We have to do this because wordpress will only enqueue scripts when called via hook
*
* @param none
* @return void
*/
public function addCssAndJs() {
    

    add_action( 'wp_enqueue_scripts', array($this,'parentAddCssAndJs') );
add_action( 'wp_head', array($this,'addInlineScriptHeader') );
add_action( 'wp_footer', array($this,'addInlineScriptFooter') );

}

/**
* Parent Add Css and Js
*
* Wrapper around the parent class's method so we can use the child in a hook
*
* @param none
* @return void
*/
public function parentAddCssAndJs(  ) {

parent::addCssAndJs();

}


/**
* Implode Resource
*
* Converts a resource path into a string containining inline css or js, or resource links
*
*
* @param $path string The path to the resource
* @param $css bool True if the resource is css, false if script
* @param $inline bool True if you want the resources returned as inline, false to return as resource links
* @return string A concatenated string containing the resources either as inline <style></style> and <script></script> tags or linked resource
*/
protected function _implodeResource( $resource_relative_path, $path, $css = true, $inline = false ) {
//initialize
$string = '';





/*
* If this is an external url, don't attempt to make it a relative url
*/
if ( preg_match( '/^http/', $path, $matches ) ){

$url = $path;

// todo: enqueue it depending on whether css or not


} else
/*
* If Not an external url, check $inline setting and add Inline or Link form depending on setting
*/

{

$url = $this->file_joinPaths( $this->file_getRootUrl(),$resource_relative_path, $path );

if ( $inline ){

$link = $this->addLeadingSlash( $this->file_joinPaths( $resource_relative_path, $path ) );


/*
* If Inline, read the file and add to the result string
*/
if ( $css ) {
$string.= "\n<style>" . file_get_contents( $this->file_getRealPath( $link ) ) . '</style>';


} else {
$string.= "\n<script type=\"text/javascript\" >" . file_get_contents( $this->file_getRealPath( $link ) ) . '</script>';

}

return $string;
}
/*
*
* If Not Inline
* then  enqueue it
*

*/



if ( $css ) {
//todo: wp_enqueue style here
    
    
 
            
    wp_enqueue_style(
$url, //$handle,
$url, //$src,
null, //$deps,
1, //$ver,
'all'//(optional) String specifying the media for which this stylesheet has been defined. Examples: 'all', 'screen', 'handheld', 'print'. See this list for the full range of valid CSS-media-types. Default: 'all' 
);        
            
} else {
wp_enqueue_script(
$url, //$handle,
$url, //$src,
null, //$deps,
1, //$ver,
false//$in_footer
);


}





}



}


/**
* Short Description
*
* Long Description
*
* @param none
* @return void
*/
public function addInlineScriptHeader() {

echo $this->CONTENT_TAGS['JS'];
echo $this->CONTENT_TAGS['CSS'];

}

/**
* Short Description
*
* Long Description
*
* @param none
* @return void
*/
public function addInlineScriptFooter() {
echo $this->CONTENT_TAGS['JS_FOOTER'];

/*
 * Add Javascript to Footer That Provides PHP Variables to Javascript
 */
    echo $this->_addScriptVars('','');

}
}

?>