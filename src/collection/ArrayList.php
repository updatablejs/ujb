<?php

namespace ujb\collection;

class ArrayList extends AbstractCollection {

	public function __construct(iterable $values = null) {
		if ($values) $this->addValues($values);
	}

	public function add($value) {
		$this->values[] = $value;
		
		return $this;
	}
	
	public function addValues(iterable $values) {	
		foreach ($values as $value)
			$this->values[] = $value;
		
		return $this;
	}
	
	public function set($index, $value) {
		if (!is_int($index))
			throw new \Exception('The parameter $index must be of integer type.');
	
		$this->values[$index] = $value;
		
		return $this;
	}
	
	public function get($index = null, $default = null) {
		if (is_null($index)) 
			return $this->toArray();
		
		return $this->hasIndex($index) ?
			$this->values[$index] : $default;
	}
	
	public function hasIndex($index) {
		return array_key_exists($index, $this->values);
	}
	
	public function hasValue($value, $strict = false) {
		return in_array($value, $this->values, $strict);
	}
	
	public function has($value, $strict = false) {
		return $this->hasValue($value, $strict);
	}
}
