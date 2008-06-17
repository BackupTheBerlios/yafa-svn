<?php
	require_once '../MP/Zend_DB_Table_Abstract.php';

	function getTableFromObjectString($objectstring) {
	   	switch ( strtolower($objectstring) ) {
	   		case "index":
	   			$table = new Index();
	   			break;	   			
	   	}
	   	return $table;
	}

	require_once 'Index.php';
		
?>