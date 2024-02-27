<?php

namespace core\database\sql\components\values;

use core\database\sql\components\AbstractComponent;

abstract class AbstractValues extends AbstractComponent {

	public $values = [];

	public function set($key, $value = null) {
		if (is_array($key))
			return $this->setValues($key);
			
		$this->values[$key] = $value;
	}

	// setValues(['key' => 'value']);
	public function setValues(array $values) {
		$this->values = array_merge($this->values, $values);
	}

	public function isEmpty() {
		return empty($this->values);
	}
}
