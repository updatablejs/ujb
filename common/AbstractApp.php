<?php

namespace core\common;

use core\resources\Resources;

abstract class AbstractApp {

	protected $resources; 
	
	protected static $instance;
	
	protected function __construct(Resources $resources = null) {
		$this->resources = $resources ? $resources : new Resources();
	}

	public static function getInstance() {
        if (!isset(static::$instance))
            static::$instance = new static();
		
        return static::$instance;
    }

	public function setResources(Resources $resources) {
		$this->resources = $resources;
		
		return $this;
	}

	public function set($key, $resource, $shared = true) {
		$this->resources->set($key, $resource, $shared);
		
		return $this;
	}

	public function get($key, array $values = []) {		
		return $this->resources->get($key, $values);
	}
	
	public static function __callStatic($name, $args) {
		$name = lcfirst(preg_replace('~^get~', '', $name));
		
		return static::getInstance()->get($name, $args);
	}
	
	public function __get($name) {		
		return $this->get($name);
	}
}
