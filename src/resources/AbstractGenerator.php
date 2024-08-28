<?php

namespace ujb\resources;

abstract class AbstractGenerator extends AbstractResource {

	protected $shared;
	protected $generated; 
	
	public function __construct($resource, bool $shared = true) {
		parent::__construct($resource);
		$this->setShared($shared);
	}
	
	abstract public function generate(array $values);
	
	public function get(array $values = []) {		
		if (!$this->shared) return $this->generate($values);
		
		if (is_null($this->generated))
			$this->generated = $this->generate($values);
			
		return $this->generated;
	}	
	
	public function setShared(bool $shared) {
		$this->shared = $shared;
		
		return $this;
	}
	
	public function reset() {
		$this->generated = null;
		
		return $this;
	}
}
