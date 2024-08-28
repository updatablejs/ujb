<?php

namespace ujb\resources;

class Resources {
	
	protected $resources = [];
	
	public function set($key, $resource, bool $shared = true, bool $bindContext = true) {
		if (is_callable($resource) && $bindContext)
			$resource = \Closure::bind($resource, $this, $this);
		
		$this->resources[$key] = ResourceFactory::create($resource, $shared);
		
		return $this;
	}
	
	public function get($key, array $values = []) {
		if (!isset($this->resources[$key])) 
			throw new \Exception('Unknown resource ' . $key);
			
		return $this->resources[$key]->get($values);
	}
	
	public function has($key) {
		return isset($this->resources[$key]);
	}
	
	public function __call($name, $args) {
		$name = lcfirst(preg_replace('~^get~', '', $name));
		
		return $this->get($name, $args);
	}
	
	public function __get($name) {		
		return $this->get($name);
	}
}
