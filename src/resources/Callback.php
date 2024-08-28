<?php

namespace ujb\resources;

class Callback extends AbstractGenerator {
	
	public function generate(array $values = []) {
		return ($this->resource)(...$values);	
	}
	
	public function set($resource) {
		if (!is_callable($resource))
			throw new \Exception('The resource must be callable.');
			
		$this->resource = $resource;
		
		return $this;
	}
}
