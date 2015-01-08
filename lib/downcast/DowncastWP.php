<?php

include ("DowncastBaseWP.php");

/**
 * Downcast
 *
 * Manages the Downcast Web Framework within WordPress
 *
 * todo: 
 * override addPage so we just have to define it from within the framework, not have to create a wordpress page for it.
 * create a way to 
 * @package Downcast
 * @author Andrew Druffner <andrew@nomstock.com>
 * @copyright  2012 Andrew Druffner
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @filesource
 */
class Downcast extends DowncastBaseWP   {

    /**
     * Config
     *
     * Configure - Add User Settings Here
     * @param none
     * @return void
     */
    public function config() {



        /*
         * SITE_CONFIG_FILE_PATH
         *
         * Set the Site Configuration File Location
         * Absolute or relative path.
         * Default: "config.json"
         */
        $this->SITE_CONFIG_FILE_PATH = "config.json";



    }

    /**
     * Short Description
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function init() {

        $this->addHooks();

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


        /*
         * [downcast_content]
         * Show Form Shortcode
         */

        add_shortcode( $this->getSlug() . '_content', array( $this, 'hookShortcodeShowContent' ), 10 );




        add_filter( 'the_content', array( $this, 'hookContentFilter' ) );


        }

    /**
     * Short Description
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function getSlug() {

        return 'downcast';

        }

    /**
     *  hook - Shortcode - ShowContent
     * 
     * 
     *  You may use an absolute path 
     *  or a relative path from root.
     * You may not use backslashes 
     * 
     * 
     *  [downcast_content path="/content/test.md"]
     *  to show the content in C:/wamp/www/wpdev.com/public_html/wp-content/plugins/simpliwp-downcast/content/test.md
     *  or 
     * [downcast_content path="C:/wamp/www/test.md"]
     *  to show the content in C:/wamp/www/test.md 
     * 
     * 
     * 
     *
     *  */
    public function hookShortcodeShowContent( $atts, $content, $tag ) {

        /*
         * the add_shortcode will always pass $content and $tag to us.
         * in the case of a non-enclosed shortcode, such as this, $content should be null,
         * but for some reason , when used within a class method, $content will not evaluate to null
         * unless we explicitly set it to null within the method, so we do that here:
         */

        //
        //configure. 
        //
        //$enclosed true/false
        //if this shortcode is not intended to have a closing tag, set this to false.
        $enclosed = false; //set to true if it is expected to come with a closing tag and contain content within the opening and closing tags.

        if ( !$enclosed ) { $content = null; }



        $defaults = array(
            'path' => null, //set equal to a them and it will load all the layout examples
        );



        $atts = shortcode_atts( $defaults, $atts ); //scrub the attributes with the defaults

        $_path = $this->file_getRealPath( ($atts[ 'path' ] ) );
        $path = ($_path === false) ? $atts[ 'path' ] : $_path;

/*
 * Render the content as defined by the current URI
 * If a path attribute was passed, render that instead
 */

        $result = ($path === false) ? $this->EMBED_TAGS[ 'CONTENT' ] : $this->renderFile( $path );
        
        /*
         * We use a buffer in case there is any output provided by plugins via hooks
         */
        ob_start();

        $this->doActionHooks( 'dc_controller_start' );
        $this->doActionHooks( 'dc_before_template' );
        echo $result;

        $this->doActionHooks( 'dc_after_template' );
        $this->doActionHooks( 'dc_controller_end' );
        ;
        $result = ob_get_clean();





        return $result;
       }

    /**
     * Short Description
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function hookContentFilter( $content ) {

        return $content;

    }

}

?>