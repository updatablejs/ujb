<?php

namespace ujb\collection;

class ArrayMap extends AbstractCollection {
	
	const IGNORE = 'ignore';
	
	public function __construct(iterable $values = null) {
		if ($values) {
			if (is_array($values))
				$this->values = $values;
			else
				$this->setValues($values);
		}
	}
	
	public function set($key, $value = null) {
		if (is_iterable($key)) 
			$this->setValues($key);
		else
			$this->values[$key] = $value;
		
		return $this;
	}
	
	public function setValues(iterable $values) {
		foreach ($values as $key => $value)
			$this->values[$key] = $value;
			
		return $this;
	}
	
	public function get($key = null, $default = null) {
		if (is_array($key)) 
			return $this->getValues($key, $default);
		
		if (is_null($key)) 
			return $this->toArray();
			
		return $this->hasKey($key) ?
			$this->values[$key] : $default;
	}
	
	public function getValues(array $keys = null, $default = null) {
		if (is_null($keys)) 
			return $this->toArray();
		
		$values = [];
		foreach ($keys as $key) {
			if (!$this->hasKey($key)) {
				if ($default == self::IGNORE) continue;
					
				$values[$key] = $default;
			}
			else 
				$values[$key] = $this->values[$key];	
		}
		
		return $values;
	}
	
	public function getKeys() {
		return array_keys($this->values);
	}
	
	public function hasKey($key) {
		return array_key_exists($key, $this->values);
	}
	
	public function has($key) {
		return $this->hasKey($key);
	}
	
	public function hasKeys(...$keys) {
		foreach ($keys as $key) {
			if (!array_key_exists($key, $this->values))
				return false;
		}
			
		return true;
	}
	
	public function hasValue($value, $strict = false) {
		return in_array($value, $this->values, $strict);
	}
	
	public function removeKey($key) {
		unset($this->values[$key]);
	}
	
	
	// Overloading 
	
	public function __set($key, $value) {
		$this->values[$key] = $value;
	}
	
	public function __get($key) {
		if (!$this->hasKey($key)) throw new \Exception(
			'Undefined property via __get(): ' . $key);
		
		return $this->values[$key];
    }
	
	public function __isset($key) {
		return $this->hasKey($key);
	}
	
	public function __unset($key) {
		unset($this->values[$key]);
	}
}
