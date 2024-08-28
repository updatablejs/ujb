<?php

namespace ujb\resources;

class Initializer extends AbstractGenerator {
	
	public function generate(array $values = []) {
		return new $this->resource(...$values);	
	}
	
	public function set($resource) {
		$this->resource = $resource;
		
		return $this;
	}
}
