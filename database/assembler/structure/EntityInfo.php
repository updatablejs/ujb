<?php

namespace core\database\assembler\structure;

class EntityInfo {
	
	public $location;
	public $isCollection;
	public $junction;
	
	public function __construct(array $location, bool $isCollection = null, string $junction = null) {
		$this->location = $location;
		$this->isCollection = $isCollection;
		$this->junction = $junction;
	}
	
	public function hasJunction() {
		return (bool) $this->junction;
	}

	public function isCollection() {
		return (bool) $this->isCollection;
	}
}
