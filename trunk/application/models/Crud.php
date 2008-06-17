<?php

class Crud extends MP_Zend_Db_Table_Abstract{
	protected $_name = 'Crud';
	
	protected $_structure = array(
								"id" => array("ID", "integer"),
								"name" => array("name", "string", "Name"),
								"body" => array("body", "text", "body"),
		);

}


?>