<?php

/* * ********************************************************************
 *  ezSQL initialisation for mySQL
 */

$db = $this->getPlugin( "ezSQL" )->db;




/* * ********************************************************************
 *  ezSQL demo for mySQL database
/*
 * This is an abbrievated demo, removing any methods that are not supported by the WordPress wpdb object
 * unsupported methods:     $db->debug(),sysdate()
 */
// Get list of tables from current database..
$my_tables = $db->get_results( "SHOW TABLES", ARRAY_N );
$this->debugLog( '$my_tables = ', $my_tables, true, false );


// Loop through each row of results..
foreach ( $my_tables as $table )
        {
    // Get results of DESC table..
   $results= $db->get_results( "DESC {$table[ 0 ]}" );

    // Print out last query and results..
$this->debugLog( '$results = ', $results, true, false );

        }
?>