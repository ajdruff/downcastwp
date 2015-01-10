<?php

/**
 * WordPress
 *
 * This plugin adds a few WordPress shortcodes to support WordPress integration.
 * It should not be activated except from within the DowncastWP WordPress plugin.
 * 
 * @package Downcast
 * @author Andrew Druffner <andrew@nomstock.com>
 * @copyright  2012 Andrew Druffner
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * 
 */
class WordPress extends DowncastPlugin {

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

        /*
         * Force Jquery
         * Do not set to true unless you've tried everything else.
         * Even if set to false, WordPress will automatically load jQuery as long as you set deps to 'jquery' in config.json for your script
         */
        $this->FORCE_JQUERY = false;//TRUE will enqueue the version of jquery set in JQUERY_VERSION normally set to false. Jquery will automatically load as long as you set deps to 'jquery' in config.json
        $this->JQUERY_VERSION = "1.7.2";//only used if FORCE_JQUERY is true


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
        /*
         * Add a WordPress Content Filter
         */

        add_filter( 'the_content', array( $this, 'hookContentFilter' ) );
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

        add_shortcode( $this->getSlug() . '_include', array( $this, 'hookShortcodeInclude' ), 10 );

        add_filter( 'the_content', array( $this, 'hookfilterContent' ) );



        /* 
         * Force Jquery Support for a Specific Version
         * Normally, you should never have to do this...
         */



        if ( $this->FORCE_JQUERY ) {
            if ( !is_admin() )
                add_action( "wp_enqueue_scripts", array( $this, "hookForceEnqueueJquery" ), 11 );

}


        }

    function hookForceEnqueueJquery() {
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', "http" . ($_SERVER[ 'SERVER_PORT' ] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/" . $this->JQUERY_VERSION  . "/jquery.min.js", false, null );
        wp_enqueue_script( 'jquery' );
    }

    public function hookContentFilter( $content ) {
        //     $this->downcast()->debugLog( '$this->SITE = ', $this->downcast()->CONFIG['SITE']['CONFIG']['SAFE_PARSE'], true, true );
        //  $this->downcast()->debugLog( '$content = ', $content, true, true );

        return $this->downcast()->parseMarkdown( $content );

#[downcast_content path="/plugins/Forms/content/my-first-ajax-form.php"]

    }

    /**
     *  hook - Shortcode - Include
     * 
     * 
     *  You may use an absolute path 
     *  or a relative path from root.
     * You may not use backslashes 
     * 
     * 
     *  [downcast_include path="/content/test.md"]
     *  to show the content in C:/wamp/www/wpdev.com/public_html/wp-content/plugins/simpliwp-downcast/content/test.md
     *  or 
     * [downcast_content path="C:/wamp/www/test.md"]
     *  to show the content in C:/wamp/www/test.md 
     * 
     * 
     * 
     *
     *  */
    public function hookShortcodeInclude( $atts, $content, $tag ) {

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

        $_path = $this->downcast()->file_getRealPath( ($atts[ 'path' ] ) );
        $path = ($_path === false) ? $atts[ 'path' ] : $_path;

        /*
         * Render the content as defined by the current URI
         * If a path attribute was passed, render that instead
         */

        $result = ($path === false) ? $this->downcast()->EMBED_TAGS[ 'CONTENT' ] : $this->downcast()->renderFile( $path );






        return $result;
       }

    /**
     * Hook Filter Content
     *
     * Long Description
     *
     * @param none
     * @return void
     */
    public function hookfilterContent( $content ) {

        /*
         * We use a buffer in case there is any output provided by plugins via hooks
         */
        ob_start();

        $this->downcast()->doActionHooks( 'dc_controller_start' );
        $this->downcast()->doActionHooks( 'dc_before_template' );
        echo $content;

        $this->downcast()->doActionHooks( 'dc_after_template' );
        $this->downcast()->doActionHooks( 'dc_controller_end' );
        ;
        $content = ob_get_clean();





        return $content;
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
}

?>
