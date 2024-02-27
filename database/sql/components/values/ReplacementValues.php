<?php

namespace core\database\sql\components\values;

class ReplacementValues extends AbstractValues {
	
	public function build() {
		$result = $values = [];
		foreach ($this->values as $key => $value) {
			if (is_int($key)) {
				$result[] = preg_match('/^[\w$]+$/', $value) ? 
					$value . ' = VALUES(' . $value . ')' : $value;
			}
			else {
				$result[] = $key . ' = ?';
				$values[] = $value;
			}
		}
		
		$this->setParams($values);
		
		return implode(', ', $result);
	}
}
