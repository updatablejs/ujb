<?php

namespace core\database\orm\entity\collection;

use core\common\Arrayable;

abstract class AbstractEntityList implements \Countable, Arrayable {

	protected $storage;

	public function __construct() {
		$this->storage = new \SplObjectStorage();
	}
	
	public function count() {
		return $this->storage->count();
	}
	
	
	// Storage
	
	public function set($entity, $extra = null) {		
		$this->storage->attach($entity, $extra);

		return $this;
	}
	
	public function has($entity) {
		return $this->storage->contains($entity);
	}

	public function remove($entity) {	
		$this->storage->detach($entity);
		
		return $this;
	}
	
	public function getStorage() {
		return $this->storage;
	}
}
