<?php

namespace ujb\resources;

abstract class AbstractResource {
	
	protected $resource; 
	
	public function __construct($resource) {
		$this->set($resource);
	}
	
	abstract public function set($resource);
	
	abstract public function get();
}
