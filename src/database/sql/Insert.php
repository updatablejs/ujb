<?php

namespace ujb\database\sql;

class Insert extends Sql {
	
	protected function _build() {
		$query = 'INSERT ';
		
		if (!$this->isEmpty('tables')) 
			$query .= 'INTO ' . $this->get('tables');
		
		if (!$this->isEmpty('values')) 
			$query .= $this->get('values');
			
		if (!$this->isEmpty('replacementValues')) 
			$query .= ' ON DUPLICATE KEY UPDATE ' . $this->get('replacementValues');
		
		return $query;
	}
}
