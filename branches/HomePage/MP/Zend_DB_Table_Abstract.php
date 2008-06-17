<?php

require_once 'Zend/Db/Table.php';
require_once 'Zend/Log.php';
require_once 'Zend/Log/Writer/Stream.php';

class MP_Zend_Db_Table_Abstract extends Zend_Db_Table_Abstract{
//	protected $_structure = array(
//		primary key:
//			"id" => array("ID", "integer"),
//		normal field:
//			"subject" => array("subject", "string", "Subject"),
//		sub table, read only, represented as a nested array
//			"messages" => array("messages", "Subtable", "Messages", "Message"),
//		sub attribute, read only, represented as a string joined by ', '
//			"senders" => array("senders", "Subattribute", "Senders", "Message", "sender", ', '),
//	);

	private $log;
	
	private $debug_level = 9;
	private $debug_target_type = 'stdout';
	public $debug_target = '';
	
	private function debug($msg, $level = 1) {
		if ($this->debug_level >= $level) {
			if ( is_array($msg) ) {
				if ($this->debug_target_type == 'stdout') {
					print_r($msg);
				}
			}
			else {
				$msg = $this->getTableName() . ': ' . $msg;
				if ($this->debug_target_type == 'stdout') {
					echo $msg . "\n";
				}
				elseif ($this->debug_target_type == 'logfile') {
	    			$this->debug_target->debug( $msg );
				}
				else {
					$this->debug_target .= $msg . "\n";
				}
			}
		}
	}
	
	public function MP_Zend_Db_Table_Abstract() {
		$this->log = new Zend_Log(new Zend_Log_Writer_Stream('/tmp/PHP-MP_Zend_Db_Table_Abstract.log'));
		return $this->__construct();
	}
	
	public function getReferenceMap() {
		return $this->_referenceMap;
	}
	public function getTableName() {
		return $this->_name;
	}
	public function getStructure() {
		return $this->_structure;
	}
	
	public function SetByArray($data) {
		if (! isset($data['ID']) or $data['ID'] == 0) {
    		// New row
    		$condition = array('ID' => 0);
    	}
		else {
			// Update row
    		$condition = array('ID' => $data['ID']);
		}
    	
		return $this->SetByArrayConditional($data, $condition);
		
	}
	
	public function SetByArrayConditional($data, $condition) {
		echo "SetByArrayConditional()\n";
//		echo "data = ";
//		print_r($data);
//		echo "condition = ";
//		print_r($condition);
		$data2 = array();
		$data_sub = array();
		foreach ($data as $name => $value) {
			if ( isset($this->_structure[strtolower($name)][0]) ) {
				// TODO: Validation of fields?
//				echo "Attribute type: " . strtolower($this->_structure[strtolower($name)][1]) . "\n";
				if (strtolower($this->_structure[strtolower($name)][1]) == 'subattribute') {
//					echo "Setting Subattribute $name\n";
					$sub_table = getTableFromObjectString($this->_structure[strtolower($name)][3]);
					foreach ($sub_table->getStructure() as $name_sub => $value_sub) {
//						echo "Checking substructure: $name_sub\n";
						if ($value_sub[3] == $this->getTableName()) {
//							echo "Found my FK: $name_sub\n";
							$data_sub[$this->_structure[strtolower($name)][3]] = array(
									'FK_name' => $name_sub,
									$name => $value,
								);
							break;
						}
					}
//					print_r($data_sub);
//					echo "Set\n";
				}
				elseif (strtolower($this->_structure[strtolower($name)][1]) == 'subtable') {
					// Ignore
				}
				else {
					// Normal field
					$data2[$this->_structure[strtolower($name)][0]] = $value;
				}
			}
		}
		
		$tmp = $this->GetArrayOneExtended( $condition );
		if ( count($tmp) == 0 ) {
//		if (! isset($tmp['ID']) or $tmp['ID'] == 0) {
			// New row
			// return the last value generated by an auto-increment column
			$data2['ID'] = $this->insert($data2);
    	}
		else {
			// Update row
			foreach ($condition as $name => $value) {
    			$condition_text[] = $this->getAdapter()->quoteInto($this->_structure[strtolower($name)][0] . ' LIKE ?', $value);
//    			$data2[$name] = $value;
    		}
			$this->update( $data2, $condition_text );
    		if ( isset($tmp['ID']) ) {
				$data2['ID'] = $tmp['ID'];
    		}
    	}
    	
    	foreach ($data_sub as $name => $value) {
    		$value[ $value['FK_name'] ] = $data2['ID'];
    		unset($value['FK_name']);
//    		foreach ($value as $name_sub => $value_sub) {
//    			$data2[$this->_structure[strtolower($name_sub)][0]] = $value_sub;
//    		}
    		getTableFromObjectString($name)->SetByArrayConditional($value, $value);
    	}
    	
    	return $data2;
	}	
	
	public function ConvertToArray($main_row, $depth = 0) {
//		$this->log->debug("ConvertToArray(" . $main_row->getTableClass() . ") ".
//			$this->_name . "\n");
			
		++$depth;
		$data = $main_row->toArray();
		if ($depth <= 2) {
			foreach ($this->_structure as $name => $value) {
//				$this->log->debug("ConvertToArray: " . $name );
				switch (strtolower($value[1])) {
					case "subtable":
						$sub_table = getTableFromObjectString($value[3]);
//						$select = new Zend_Db_Table_Select();
//						$select->from( $sub_table, $value[0] );
//						$sub_rows = $main_row->findDependentRowset( $sub_table, $select );
						$sub_rows = $main_row->findDependentRowset( $sub_table );
						foreach ($sub_rows as $sub_row) {
							$data[$name][] = $sub_table->ConvertToArray($sub_row, $depth);
						}
						
//						$sub_ReferenceMap = $sub_table->getReferenceMap();
//						
//						$data[$name] = $sub_table->GetArrayAll( array($sub_ReferenceMap['Texts'] => $data['ID']), null, null, null, $depth );
						break;
					case "subattribute":
//						echo $value[3] ."\n";
						$sub_table = getTableFromObjectString($value[3]);
						$sub_rows = $main_row->findDependentRowset( $sub_table );
						$tmp = array();
						foreach ($sub_rows as $sub_row) {
							$sub_row = $sub_table->ConvertToArray($sub_row, $depth);
							$tmp[] = $sub_row[$value[4]];
						}
						$data[$name] = join($value[5], $tmp);
//						$select = new Zend_Db_Table_Select($sub_table);
//						$select->from( $sub_table, array($value[4]) );
//						$sub_rows = $main_row->findDependentRowset( $sub_table, $select );
//						$tmp = array();
//						foreach ($sub_rows as $sub_row) {
//							$sub_row = $sub_table->ConvertToArray($sub_row, $depth);
//							$tmp[] = $sub_row[$value[4]];
//						}
						$data[$name] = join($value[5], $tmp);
						break;
					case "parent":
						$tmp = $main_row->findParentRow( getTableFromObjectString($value[3]) )->toArray();
//						echo $this->getTableName() . ": {$value[3]}: $name = {$value[0]}\n";
						$data[$name] = $tmp[$value[0]];
						break;
				}
			}
		}
		return $data;
	}
	
	public function GetArrayAll($where = null, $order = null, $count = null, $offset = null, $depthoffset = 1) {
//		$this->log->debug("GetArrayAll($where = null, $order = null, $count = null, $offset = null)" .
//			$this->_name . "\n");
//		echo "GetArrayAll() ";
		$main_rows = $this->fetchAll( $where, $order, $count, $offset );
		$data = array(); 
		foreach ( $main_rows as $main_row ) {
			$data[] = $this->ConvertToArray($main_row, $depthoffset);
		}
		return $data;
	}
	
	public function GetArrayOne($PK) {
//		$this->log->debug("GetArrayOne($PK)" .
//			$this->_name . "\n");
		$data = array();
		$row = $this->find( $PK )->current();
		if (isset($row)) {
			$data = $this->ConvertToArray( $row );
		}
		return $data;
	}

	public function GetArrayOneExtended($parameters) {
		$this->debug("GetArrayOneExtended(...) ",5);
		$this->debug($parameters, 6);

		//TODO: include parent record
		$data2 = array();
		foreach ($parameters as $name => $value) {
//			$this->log->debug("GetArrayOneExtended $name => $value");
			if ( isset($this->_structure[strtolower($name)][0]) and substr($this->_structure[strtolower($name)][1],0,3) != 'Sub' ) {
				$data2[] = $this->getAdapter()->quoteInto( $this->_structure[strtolower($name)][0] . ' LIKE ?', $value );
			}
		}
//		$this->log->debug("GetArrayOneExtended $data2:" . $data2[0]."/".$data2[1]."/".$data2[2]);
		$data = array();
		$row = $this->fetchRow($data2);
		if (isset($row)) {
			$data = $this->ConvertToArray( $row );
		}
		return $data;
	}
	
	public function count() {
		//TODO: Faster way to count a whole table?
		return $this->fetchAll()->count();
	}
}

?>