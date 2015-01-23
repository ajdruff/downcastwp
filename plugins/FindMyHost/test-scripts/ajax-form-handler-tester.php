<?php

/*
 * Testing
 * 
 * non-ajax-universal-form-handler.php
 * 
 * This is intended to be used to test ajax form handlers
 * 
 * Usage:
 * 
 * Create a page that includes this script.
 * If in WordPress:
 * [downcast_include path="/wp-content/plugins/downcastwp/plugins/ezSQL/test/ajax-form-handler-tester.php"]
 * otherwise, use the addPage() method.
 * 
 * Then hardcode the $_POST vars that would otherwise be passed via the form
 * 
 */
/*
 * Hardcode $_POST vaars
 */


/*
 * Command Line?
 * sometimes its nice to run via command line to troubleshoot
 * Some errors only show themselves on command line
 */
$command_line=false;



   
   
   $_POST[ 'website' ]="websitewelcome.com";
   $_POST[ 'website' ]="htaccessbasics.com";
   $_POST[ 'form' ]=1;
   

   /*
    * Call the form handler
    */
   if ( !$command_line ) {
     ini_set( 'error_reporting', E_ALL );

     $FindMyHostPlugin=$this->getPlugin( 'FindMyHost') ;
  
}else {
    
        /*
         *
         * Include the Downcast Library
         *
         */
// prevent silly notices, notably errors stemming from referencing $_SERVER when running via command line
     //   ini_set( 'error_reporting', E_ERROR | E_WARNING | E_PARSE );
ini_set( 'error_reporting', E_ERROR  );
        include_once("../../../lib/downcast/Downcast.php");
        include_once("../../../lib/downcast/DowncastPlugin.php");
        /*
         * Create a new Plugin Object
         */

        $downcast = new Downcast();

  




    $FindMyHostPlugin=$downcast->getPlugin( 'FindMyHost') ;
    
    
    
               
 
    
}



   $FindMyHostPlugin->formActionFindHost();


?>
