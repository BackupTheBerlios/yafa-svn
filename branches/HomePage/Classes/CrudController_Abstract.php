<?php
/** CRUD Template, Version 1.00 */

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

class CrudController_Abstract extends Zend_Controller_Action
{
	// Abstract Class Modell
	// Only internally used
	private $_table;
	
	// String with the Name of the Model Class, parameter to getTableFromObjectString()
	// Configuration Variable.
	// TODO: Replace by auto detection based on class name
	public $_modelname = "CRUD";
	
	// Number of records shown on one page
	// Configuration Variable.
	public $_page_length = 50;
	
	public function init()
	{
		Zend_Controller_Front::getInstance()->getParam('log')->debug("CrudController_Abstract::init()");
		$this->_table = getTableFromObjectString($this->_modelname);
		$this->view->ControllerName = $this->_getParam('controller', '');;
		$this->_fields = $this->_table->getStructure();
		foreach ($this->_fields as $name => $value) {
			if ($value[1] == "Subtable") {
				$this->_fields[$name][3] = getTableFromObjectString($value[3])->getStructure();
			}
		}
		$this->view->fields = $this->_fields;
	}
	
    public function indexAction()
    {
//    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);
    	// All
		$this->view->page = $this->_getParam('page', 1);;		
    	$this->view->page_count = ceil($this->_table->count() / $this->_page_length);;
		
    	$result = $this->_table->GetArrayAll(null, null, $this->_page_length, ($this->view->page-1)*$this->_page_length );
		$this->view->data = $result;
    }

    public function newAction()
    {
//		Zend_Controller_Front::getInstance()->getParam('auth')->auth(3);
    }

	public function editAction()
	{
//    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(3);
    	
		$this->view->data = $this->_table->GetArrayOne( $this->_getParam('ID', 0) );
   	}
	
	public function showAction()
	{
//		Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);
		
		$this->view->data = $this->_table->GetArrayOne( $this->_getParam('ID', 0) );
	}
	
	public function deleteAction()
	{
//		Zend_Controller_Front::getInstance()->getParam('auth')->auth(5);
		
		$ID = 	$this->_getParam('ID', 0);
		if ($ID != 0) {
			$this->_table->delete( 'ID = ' . $ID );			
			$this->view->msg = "Record delete!";
		}
		$this->_helper->viewRenderer('index');
		$this->indexAction();
	}
	
	public function processAction()
	{
//    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(3);
    	
        $ID = 	$this->_getParam('ID', 0);
        $this->view->data = array();
    	foreach ($this->_fields as $field) {
			$this->view->data[$field[0]] = $this->_getParam($field[0], 0);
		} 

		// Write to DB
        $this->view->data = $this->_table->SetByArray($this->view->data);
		
		// display the item
		$this->_helper->viewRenderer('show');
		
		return;
	}

}


?>
