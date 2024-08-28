<?php

namespace ujb\router\valuesContainer;

abstract class AbstractValuesContainer {
	
	public $values = [];

	public function __construct(array $values = null) {
		if ($values) $this->setValues($values);
	}
	
	public function setValues($values) {
		if ($values instanceof AbstractValuesContainer)
			$values = $values->toArray();
	
		foreach ($values as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method))
				$this->$method($value);
			else 
				$this->values[$key] = $value;
		}
		
		return $this;
	}
	
	public function toArray() {
		return $this->values;
	}
}
