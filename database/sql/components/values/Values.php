<?php

namespace core\database\sql\components\values;

class Values extends AbstractValues {

	public function build() {
		$this->setParams($this->values);
	
		$values = array_fill_keys(array_keys($this->values), '?');
		
		return ' (' . implode(', ', array_keys($values)) . ')' 
			. ' VALUES (' . implode(', ', array_values($values)) . ')';
	}
}
