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
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);	
    	$this->returnSQL($this->_getParam('q', ''));	
    }
    
    public function addmsgAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);	
		$this->db->setFetchMode(Zend_Db::FETCH_ASSOC);
    	
    	$subject= $this->_getParam('subject', '');
    	$sender = $this->_getParam('sender', '');
    	$date	= $this->_getParam('date', '');
    	$msgid 	= $this->_getParam('msgid', '');
    	
    	$retval = -1;
    	$sql = "SELECT COUNT(*) as count FROM Msg WHERE messageid LIKE '$msgid'";
    	$ref = $this->db->fetchAll($sql);
    	$retval = $ref;
        if ($ref[0]['count'] != 0) {
            $ref = $this->db->fetchAll("SELECT ID FROM Msg WHERE messageid LIKE '$msgid'");
            $retval = $ref[0]['ID'];
            $data = array(
			    'unread'      => 1,
			);
			$this->db->update('Msg', $data, "ID = $retval");
        }
        else {
            //Add to Database
            $data = array(
			    'subject'   => $subject,
			    'sender' 	=> $sender,
			    'date'      => $date,
			    'messageid' => $msgid,
			);
			$this->db->insert('Msg', $data);
            $retval = $this->db->lastInsertId();
        }
        $this->returnCustom( array('NewID' => $retval) );
    }
    
    public function addimageAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);	
    	$this->db->setFetchMode(Zend_Db::FETCH_ASSOC);
    	
    	$myFilename		= $this->_getParam('myFilename', '');
    	$origFilename	= $this->_getParam('origFilename', '');
    	$msg_id			= $this->_getParam('msg_id', '');
    	$checksum 		= $this->_getParam('checksum', '');
        
        $stmt = $this->db->query( 'SELECT COUNT(*) as count FROM Image WHERE checksum LIKE :checksum' );
        $stmt->execute( array(':checksum' => $checksum) );
        $ref = $stmt->fetchAll();
        if ($ref[0]['count'] != 0) {
            // Found Image in Database. Using the old Image_ID
        	$stmt = $this->db->query( 'SELECT ID FROM Image WHERE checksum LIKE :checksum' );
        	$stmt->execute( array(':checksum' => $checksum) );
			$ref = $stmt->fetchAll();
            $retval['New_ID'] = $ref[0]['ID'];
            $retval['upload'] = 0;
            
        }
        else {
            #Add to Database
            $data = array(
			    'filename'   => $myFilenamet,
			    'origfilename' 	=> $origFilename,
			    'checksum'      => $checksum,
			);
			$this->db->insert('Image', $data);
            $retval['New_ID'] = $this->db->lastInsertId();
            $retval['upload'] = 1;
        }

        $data = array(
		    'Msg_ID'   => $msg_id,
		    'Image_ID' 	=> $image_id,
		);
		$this->db->insert('Msg_Images', $data);

		$this->returnCustom( $retval );
    }

    public function addtextAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);
    	
   	    #Add to Database
        $data = array(
		    'Msg_ID'   		=> $this->_getParam('msg_id', ''),
		    'content_type' 	=> $this->_getParam('content_type', ''),
		    'body'      	=> $this->_getParam('body', ''),
		);
		$this->db->insert('Text', $data);
			
		$this->returnCustom( array('success' => 1) );
    }

    public function addtagAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(4);
    	$this->db->setFetchMode(Zend_Db::FETCH_ASSOC);
    	
    	$tag_name= $this->_getParam('tag_name', '');
    	
        $tag_id = -1;
        
        $sql = "SELECT COUNT(*) as count FROM Tag WHERE name LIKE '$tag_name'";
        $ref = $this->db->fetchAll($sql);
        if ($ref[0]['count'] != 0) {
            // Found Tag in Database. Using the old Tag_ID
            $ref = $this->db->fetchAll("SELECT ID FROM Tag WHERE name LIKE '$tag_name'");
            $tag_id = $ref[0]['ID'];
        }
        else {
            #Add to Database
            $data = array(
			    'name'   => $tag_name,
			);
			$this->db->insert('Tag', $data);
            $tag_id = $this->db->lastInsertId();
        }
        $this->returnCustom( array('Tag_ID' => $tag_id) );
    }

    public function addtagsugAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(3);
    	
    }
    
    public function tagimageAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(4);
    	
    	$image_id	= $this->_getParam('image_id', '');
    	$tag_id		= $this->_getParam('tag_id', '');
    	
		$stmt = $this->db->query( 'INSERT IGNORE INTO Image_Tags (Image_ID, Tag_ID) VALUES (:image, :tag)' );
		$stmt->execute( array(':image' => $image_id, ':tag' => $tag_id) );
		
		$this->returnCustom( array('success' => 1) );
    }
    
    private function returnSQL($sql) {
    	if ($sql != '') {
		    $this->db->setFetchMode(Zend_Db::FETCH_ASSOC);
    	
			$this->view->result = $this->db->fetchAll($sql);
    	}
    	$this->_helper->viewRenderer('sql');
    }
    
    private function returnCustom($value) {
		$this->view->result = $value;
    	$this->_helper->viewRenderer('sql');
    }
}


?>
