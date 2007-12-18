<?php
// Given an array of configuration data
$configArray = array(
	// Full path to application.
	// Most commonly autodetection is OK.
	// When getting errors like 'File not found', input full path manually 
	'BasePath' => realpath( '..' ),

	// Database Connection Data
	'database' => array(
        'adapter' => 'mysqli',
        'params'  => array(
            'host'     => 'localhost',
            'username' => 'yafa',
            'password' => 'Stollentroll',
            'dbname'   => 'yafa_yafa'
        )
    )
);




?>
