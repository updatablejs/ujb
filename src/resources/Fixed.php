<?php

namespace ujb\resources;

class Fixed extends AbstractResource {
	
	public function set($resource) {
		$this->resource = $resource;
		
		return $this;
	}
	
	public function get() {
		return $this->resource;
	}
}
