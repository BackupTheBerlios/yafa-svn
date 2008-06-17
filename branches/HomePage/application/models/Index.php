<?php

class Index extends MP_Zend_Db_Table_Abstract{
	protected $_name = 'Index';
	
	protected $_structure = array(
								"id" => array("ID", "integer"),
								"name" => array("name", "string", "Name"),
								"body" => array("body", "text", "body"),
		);

}


?>