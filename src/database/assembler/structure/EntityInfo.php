<?php

namespace ujb\database\assembler\structure;

// Table entity info.
class EntityInfo {

	// Where the entity will be attached.
	public $location;
	
	// The entity is part of a collection.
	public $isCollection;
	
	// Junction used in the union.
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
