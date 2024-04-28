<?php

namespace ujb\collection;

abstract class AbstractCollection implements \IteratorAggregate, \JsonSerializable {
	
	protected $values = [];

	public function getIterator() {
		return new \ArrayIterator($this->values);
	}

	public function isEmpty() {
		return empty($this->values);
	}

	public function clear() {
		$this->values = [];
	}
	
	public function toArray() {
		return $this->values;
	}
	
	public function jsonSerialize() {
		return $this->values;
	}
}
