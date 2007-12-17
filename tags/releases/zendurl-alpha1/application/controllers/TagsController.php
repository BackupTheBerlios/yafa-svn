<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Db.php';
require_once 'Zend/Debug.php';

class TagsController extends Zend_Controller_Action
{
	public function init()
	{
		$this->db = Zend_Db::factory(Zend_Controller_Front::getInstance()->getParam('config')->database);
	}
	
    public function indexAction()
    {
    	$this->view->realname = Zend_Controller_Front::getInstance()->getParam('auth')->realname;
    	$this->view->level = Zend_Controller_Front::getInstance()->getParam('auth')->level;
    }

    public function taggingAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(3);
    	
    	$filter = $this->_getParam('q', 0);
    	if ($filter != 0) {
    		$filter = "WHERE ID = $filter"; 
    	}
    	else {
    		$filter = "WHERE tagged = 0 LIMIT 1";
    	}
	    $this->db->setFetchMode(Zend_Db::FETCH_OBJ);
    	
		$result = $this->db->fetchAll('SELECT 
				*
			FROM 
				Msg
			' . $filter);
		
		if ($result != NULL) {
			$row = $result[0];
			$msg->ID = $row->ID;
			$msg->sender = $row->sender;
			$msg->subject = $row->subject;
			$msg->date = $row->date;
			
			$images = $this->db->fetchAll('SELECT 
					ID,
					origfilename,
					url,
					urlthumb
				FROM 
					Image 
					INNER JOIN Msg_Images ON Image.ID = Msg_Images.Image_ID
				WHERE
					Msg_Images.Msg_ID = ?', $msg->ID);
			foreach ($images as $row) {
				$row->tags = $this->db->fetchAll('SELECT ID, name FROM Tag INNER JOIN Image_Tags ON Tag.ID = Image_Tags.Tag_ID WHERE Image_Tags.Image_ID = ?', $row->ID);
			}
			$msg->images = $images;
			
//			$msg->texts = $this->db->fetchAll('SELECT content_type, body FROM Text WHERE Msg_ID = ?', $msg->ID);
			$msg->texts = array();
		}
		
		$this->view->msg = $msg;
		
    }
    
    private function InsertOrUpdate($table, $PrimKey, $data)
    {
    	$sql = "SELECT COUNT(*) FROM $table WHERE ";
    	$where = array();
    	$and_bit = false;
    	foreach ($PrimKey as $key) {
    		if ($and_bit)
    		{
    			$sql .= ' and ';
    		}
    		$sql .= "$key = '" . $data[$key] ."'";
    		$where[] = "$key = '" . $data[$key] ."'";
    		$and_bit = true;
    	}
		
    	if ($this->db->fetchOne($sql) == 0 )
    	{
    		//insert
    		Zend_Controller_Front::getInstance()->getParam('auth')->auth(2);
    		$this->db->insert($table, $data);
    	}
    	else
    	{
    		//update
    		Zend_Controller_Front::getInstance()->getParam('auth')->auth(3);
    		$this->db->update($table, $data, $where);
    	}
    }
}


?>
