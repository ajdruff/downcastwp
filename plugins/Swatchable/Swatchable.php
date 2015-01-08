<?php
/**
 * Swatchable Plugin
 *
 * This plugin adds Bootstrap namespace support provided by Simpli-Swatchable2. 
 * It simply adds a div surrounding downcast content that has the bootstrap class. 
 * 
 * @package Downcast
 * @author Andrew Druffner <andrew@nomstock.com>
 * @copyright  2012 Andrew Druffner
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * 
 */
class Swatchable extends DowncastPlugin {

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
        $this->BOOTSTRAP_NAMESPACE='class="bootstrap"';  //'class="bootstrap" or 'id="bootstrap"
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
         * Add a tag filter
         */

     $this->downcast()->addActionHook( 'dc_before_template', array( $this, 'addOpeningTag' ) );
          $this->downcast()->addActionHook( 'dc_after_template', array( $this, 'addClosingTag' ) );
       


    }



        public function addOpeningTag( ) {

        echo ('<div '.$this->BOOTSTRAP_NAMESPACE.'>test' );
    }

        public function addClosingTag( ) {

        echo ('</div>' );
        

    }

    
}

?>
