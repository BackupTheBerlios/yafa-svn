<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Db.php';
require_once 'Zend/Debug.php';

class BrowseController extends Zend_Controller_Action
{
	public function init()
	{
		$this->db = Zend_Db::factory(Zend_Controller_Front::getInstance()->getParam('config')->database);
	}
	
    public function indexAction()
    {
    	$this->_helper->viewRenderer('imgtable');
		$this->imgtableAction();
    }

    public function msgAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);
    	
    	$filter = $this->_getParam('q', 0);
	    $this->db->setFetchMode(Zend_Db::FETCH_OBJ);
    	
		$result = $this->db->fetchAll('SELECT 
				*
			FROM 
				Msg
			WHERE
				ID = ?', $filter);
		
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
					Msg_Images 
					INNER JOIN Image ON Image.ID = Msg_Images.Image_ID
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
    
    public function imgtableAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);
    	
    	$filter = $this->_getParam('q', 0);
	    $this->db->setFetchMode(Zend_Db::FETCH_OBJ);
		
		$result = $this->db->fetchAll('SELECT 
			Image.ID as ID, 
			origfilename, 
			url,
			urlthumb 
		FROM 
			Image INNER JOIN 
			Image_Tags ON Image.ID = Image_Tags.Image_ID
		WHERE
			Image_Tags.Tag_ID = ?', $filter);

		foreach ($result as $row) {
			$tags = $this->db->fetchAll('SELECT ID, name FROM Tag INNER JOIN Image_Tags ON Tag.ID = Image_Tags.Tag_ID WHERE Image_Tags.Image_ID = ?', $row->ID);
			$row->tags = $tags;
		}
		
		$this->view->images = $result;    	
    }
    
    public function searchImages($filter) {
		// Image.filename
		$result = $this->db->fetchOne("
	    	CREATE TEMPORARY TABLE temp_Search 
	    	SELECT
				Image.ID as ID, 
				origfilename, 
				url,
				urlthumb,
				MATCH (origfilename) AGAINST ( ? ) as score
			FROM 
				Image
			HAVING
				score > 3", $filter );
	    	
	    $result = $this->db->fetchAll("SELECT * FROM temp_Search ORDER BY score DESC LIMIT 99");
	    $count = $this->db->fetchOne("SELECT COUNT(*) FROM temp_Search");
	    $a = $this->db->fetchOne("DROP TEMPORARY TABLE temp_Search");

    	foreach ($result as $row) {
			$tags = $this->db->fetchAll('SELECT ID, name FROM Tag INNER JOIN Image_Tags ON Tag.ID = Image_Tags.Tag_ID WHERE Image_Tags.Image_ID = ?', $row->ID);
			$row->tags = $tags;
		}

		return array($count, $result);
    }

    public function searchTags($filter) {
		// Tag.name
		$result = $this->db->fetchOne("
	    	CREATE TEMPORARY TABLE temp_Search 
	    	SELECT DISTINCT
				Tag.ID as ID, 
				Tag.Name as tag,
				Category.name as Cat,
				MATCH (Tag.Name) AGAINST ( ? ) as score
			FROM 
				Tag
			  	INNER JOIN Category ON Tag.Category_ID = Category.ID 			
			HAVING 
				score > 3", $filter );
	    	
	    $result = $this->db->fetchAll("SELECT * FROM temp_Search WHERE Cat != 'Special' ORDER BY score DESC LIMIT 99");
	    $count = $this->db->fetchOne("SELECT COUNT(*) FROM temp_Search");
	    $a = $this->db->fetchOne("DROP TEMPORARY TABLE temp_Search");
	    			  	
    	foreach ($result as $row) {
			$category[$row->Cat]->tags[$row->ID] = $this->db->fetchOne('SELECT COUNT(Image_Tags.Image_ID) FROM Image_Tags WHERE Image_Tags.Tag_ID = ?', $row->ID);
			$category[$row->Cat]->names[$row->ID] = $row->tag;
			$category[$row->Cat]->score[$row->ID] = $row->score;
    	}
						
		return array($count, $category);
    }

    public function searchMsgs($filter) {
 		// headers
	   	$result = $this->db->fetchOne("
	    	CREATE TEMPORARY TABLE temp_Search 
	    	SELECT DISTINCT
				Msg.ID as ID, 
				sender, 
				subject,
				date,
				(MATCH (subject, sender) AGAINST ( ? )) * 1.2 as score
			FROM 
				Msg
			HAVING 
				score > 3", $filter );
	   	// body
	   	$result = $this->db->fetchOne("
	    	INSERT INTO temp_Search 
	    	SELECT DISTINCT
				Msg.ID as ID, 
				sender, 
				subject,
				date,
				MATCH (Text.body) AGAINST ( ? ) as score
			FROM 
				Msg
				INNER JOIN Text ON Msg.ID = Text.Msg_ID
			HAVING
				score > 3", $filter );
	    	
	   	$result = $this->db->fetchAll("SELECT * FROM temp_Search ORDER BY score DESC LIMIT 99");
	   	$count = $this->db->fetchOne("SELECT COUNT(*) FROM temp_Search");
	   	$a = $this->db->fetchOne("DROP TEMPORARY TABLE temp_Search");

	   	return array($count, $result);
    }
    
    public function msgsAction() {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);
    	$this->view->me = "/browse/msgs";
    	
     	$filter = $this->_getParam('q', 1);
     	$page = $this->_getParam('page', 1);
     	$this->db->setFetchMode(Zend_Db::FETCH_OBJ);

	    $result = $this->db->fetchAll("
	    	SELECT DISTINCT
				Msg.ID as ID, 
				sender, 
				subject,
				date
			FROM 
				Msg
			WHERE 
				?
			LIMIT ?", array($filter, 100 * $page) );
		$count = $this->db->fetchOne("
	    	SELECT Count(*) as count
			FROM 
				Msg
			WHERE 
				?", $filter );
		$this->view->page_count = $count;
		$this->view->page = $page;			
		
		foreach ($result as $row) {
	    	$images = $this->db->fetchAll('SELECT 
					ID,
					origfilename
				FROM 
					Msg_Images
					INNER JOIN Image ON Image.ID = Msg_Images.Image_ID
				WHERE
					Msg_Images.Msg_ID = ?', $row->ID);
			foreach ($images as $row2) {
				$row2->tags = $this->db->fetchAll('SELECT ID, name FROM Tag INNER JOIN Image_Tags ON Tag.ID = Image_Tags.Tag_ID WHERE Image_Tags.Image_ID = ?', $row2->ID);
			}
			$row->images = $images;
			
			$row->texts_count = $this->db->fetchOne('SELECT COUNT(*) FROM Text WHERE Msg_ID = ?', $row->ID);
		}
			
    	$this->view->msgmsg = "Showing all Messages matching $filter.";
		$this->view->msgs = $result;
    }
    
    public function searchAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);
		$filter = $this->_getParam('q', '');
		$type = $this->_getParam('type', '');
	    $this->db->setFetchMode(Zend_Db::FETCH_OBJ);
		
		if ( $filter != '' ) {
			// Tags
			if (($type == '') || ($type == 'Tags')) {
				list($count, $result) = $this->searchTags($filter);
			    $this->view->tagmsg = "Searching for $filter results in $count Tags. Showing first 99.";
				$this->view->tags = $result;
			}		
			
			// Images
			if (($type == '') || ($type == 'Images')) {
				list($count, $result) = $this->searchImages($filter);
			    $this->view->imagemsg = "Searching for $filter results in $count Images. Showing first 99.";
				$this->view->images = $result;
			}		
			
			// Messages
			if (($type == '') || ($type == 'Msgs')) {
				list($count, $result) = $this->searchMsgs($filter);
	    		$this->view->msgmsg = "Searching for $filter results in $count Messages. Showing first 99.";
				$this->view->msgs = $result;
			}
			
			$this->_helper->viewRenderer('searchresult');
    	}
    	else {
    		$this->view->msg = "Please enter a valid search string!";
    	}
    }

    public function imageAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);
    	$ID = $this->_getParam('ID', 0);
    	$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
		$result = $this->db->fetchAll('SELECT ID, origfilename, url FROM Image WHERE ID = ?', $ID);
		
		foreach ($result as $row) {
			$tags = $this->db->fetchAll('SELECT ID, name FROM Tag INNER JOIN Image_Tags ON Tag.ID = Image_Tags.Tag_ID WHERE Image_Tags.Image_ID = ?', $row->ID);
			$row->tags = $tags;
		}
		
		$this->view->image = $result[0];
    }
    
    public function tagcloudAction()
    {
    	Zend_Controller_Front::getInstance()->getParam('auth')->auth(1);
    	$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
		$result = $this->db->fetchAll("
			  SELECT 
			  	Tag.ID as ID, 
			  	Tag.name AS tag, 
			  	Category.name as Cat, 
			  	COUNT(Image_Tags.Image_ID) AS quantity
			  FROM Tag 
			  	INNER JOIN Category ON Tag.Category_ID = Category.ID 
			  	INNER JOIN Image_Tags ON Tag.ID = Image_Tags.Tag_ID
			  WHERE Category_ID != 1
			  GROUP BY Tag.ID
			  ORDER BY Tag.name ASC");

		foreach ($result as $row) {
			$category[$row->Cat]->tags[$row->ID] = $row->quantity;
			$category[$row->Cat]->names[$row->ID] = $row->tag;
		}
		
		$this->view->result = $category;
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
