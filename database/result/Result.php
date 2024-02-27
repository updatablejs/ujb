<?php

namespace core\database\result;

use core\database\result\source\Source;

class Result implements \Iterator, \Countable {

	protected $source;
	protected $fetchStyle = FetchStyle::Associative;
	protected $pair;
	
	public function __construct(Source $source) {
		$this->source = $source;
	}
	
	public function setFetchStyle($fetchStyle) {
		$this->fetchStyle = $fetchStyle;
		
		return $this;
	}
	
	public function setObjectFactory(ObjectFactory $objectFactory) {
		$this->source->setObjectFactory($objectFactory);
		
		return $this;
	}
	
	public function getSource() {
		return $this->source;
	}
	
	public function fetch($fetchStyle = null, $callback = null) {
		$pair = $this->fetchPair($fetchStyle, $callback);
		
		return $pair ? $pair[1] : null;
	}
	
	public function fetchPair($fetchStyle = null, $callback = null) {
		if (is_callable($fetchStyle)) {
			$callback = $fetchStyle;
			$fetchStyle = $this->fetchStyle;
		}
		elseif (is_null($fetchStyle))
			$fetchStyle = $this->fetchStyle;
	
		$this->pair = $this->source->fetchPair($fetchStyle);
		if ($this->pair && $callback)
			$this->pair[1] = $callback($this->pair[1]);
		
		return $this->pair;
	}
	
	public function fetchAll($fetchStyle = null) {
		if (is_null($fetchStyle))
			$fetchStyle = $this->fetchStyle;
	
		return $this->source->fetchAll($fetchStyle);
	}
	
	public function fetchColumn($column = 0, $default = null) {
		$values = is_int($column) ? $this->fetch(FetchStyle::Numeric) : $this->fetch(FetchStyle::Associative);
		
		return $values ? $values[$column] : $default; 
	}
	
	public function count() {
		return $this->source->count();
	}
	
	public function clear() {
		$this->source->clear();
		
		return $this;
	}
	
	public function getMetadata() {
		return $this->source->getMetadata();
	}
	

	// Iterator
	
	public function rewind() {
		$this->pair = null;
		
		$this->next();
	}
	
	public function next() {
		$this->fetch();
	}
	
    public function valid() {
		return $this->pair !== null;
    }

	public function current() {
		return $this->pair[1];
	}
	
	public function key() {
		return $this->pair[0];
	}
}
