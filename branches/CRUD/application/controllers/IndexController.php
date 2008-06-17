<?php

/** CrudController_Action */
require_once 'Classes/CrudController_Abstract.php';

class IndexController extends CrudController_Abstract
{
	// String with the Name of the Model Class, parameter to getTableFromObjectString()
	// Configuration Variable.
	// Will be replace by auto detection based on class name
	public $_modelname = "CRUD";
	
	// Number of records shown on one page
	// Configuration Variable.
	public $_page_length = 50;
		
}
?>
