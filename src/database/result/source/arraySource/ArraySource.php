<?php

namespace ujb\database\result\source\arraySource;

use ujb\database\result\source\AbstractSource;

abstract class ArraySource extends AbstractSource {

	protected $source = [];

	public function setSource(array $source) {
		$this->source = $source;
		reset($this->source);
		
		return $this;
	}
	
	public function count() {
		return count($this->source);
	}
	
	public function clear() {
		$this->source = [];
		
		return $this;
	}
		
	protected function _fetchPair() {
		$key = key($this->source);
		$current = current($this->source);
		
		if (is_null($key)) return null;
		
		unset($this->source[$key]);
			
		return [$key, $current];
	}
}
