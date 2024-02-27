<?php

namespace core\database\sql\components\values;

class Set extends AbstractValues {

	public function build() {
		$this->setParams($this->values);
		
		$result = [];
		foreach (array_keys($this->values) as $key)
			$result[] = $key . ' = ?';

		return implode(', ', $result);
	}
}
