<?php
	require_once '../MP/Zend_DB_Table_Abstract.php';

	function getTableFromObjectString($objectstring) {
	   	switch ( strtolower($objectstring) ) {
	   		case "crud":
	   			$table = new Crud();
	   			break;	   			
	   	}
	   	return $table;
	}

	require_once 'Crud.php';
		
?>