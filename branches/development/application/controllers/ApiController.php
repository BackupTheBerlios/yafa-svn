<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Db.php';
require_once 'Zend/Debug.php';

class ApiController extends Zend_Controller_Action
{
	public function init()
	{
		$this->db = Zend_Db::factory(Zend_Controller_Front::getInstance()->getParam('config')->database);
	}
	
    public function indexAction()
    {
    }

    public function sqlAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(9);
    	
    	$filter = $this->_getParam('q', 0);
    	if ($filter != 0) {
		    $this->db->setFetchMode(Zend_Db::FETCH_ARRAY);
    	
			$this->view->result = $this->db->fetchAll($filter);
    	}
	
    }
    
}


?>
