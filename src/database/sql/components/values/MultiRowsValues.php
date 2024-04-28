<?php

namespace ujb\database\sql\components\values;

use ujb\database\sql\components\AbstractComponent,
	ujb\common\Util;

// Declaration of MultiRowsValues::set($values) should be compatible with AbstractValues::set($key, $value = null); 
class MultiRowsValues extends AbstractComponent {

	public $values = [];

	// setValues([['key' => 'value', 'key' => 'value', 'key' => 'value'], ...]);
	public function set($values) {
		if (Util::isNumericArray($values))
			$this->values = array_merge($this->values, $values);
		else
			$this->values[] = $values;
	}

	public function build() {
		$values = array();
		foreach ($this->values as $row) {
			$values = array_merge($values, array_values($row));
		}

		$this->setParams($values);
	
		$placeholders = array_fill(0, count($this->values), 
			'(' . implode(',', array_fill(0, count($row), '?')) . ')'
		);
		
		return ' (' . implode(', ', array_keys($row)) . ') VALUES ' . implode(',', $placeholders);
	}

	public function isEmpty() {
		return empty($this->values);
	}
}
