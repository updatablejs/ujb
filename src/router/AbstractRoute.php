<?php

namespace ujb\router;

use ujb\http\Request,
	ujb\common\Util,
	ujb\collection\ArrayMap;

abstract class AbstractRoute extends AbstractRouteList {
	
	protected $parent;
	protected $path;
	protected $routable = true; // If it is false, only the children's routes will be checked.
	protected $values;
	
	public function __construct(array $values = null) {
		if ($values) 
			$this->hydrate($values);
	}
	
	abstract public function build();
	
	public function hydrate(array $values) {
		if (isset($values['values'])) {
			$values = array_merge($values, $values['values']);
				
			unset($values['values']);
		}
			
		$properties = ['path', 'routable', 'routes'];
		$_values = [];
		foreach ($values as $key => $value) {
			if (!in_array($key, $properties)) {
				$_values[$key] = $value;
					
				continue;
			}
				
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method))
				$this->$method($value);
			else
				$this->$key = $value;
		}
			
		$this->setValues($_values);
	}
	
	public function setParent($parent) {
		$this->parent = $parent;
		
		return $this;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function setPath($path) {
		$this->path = $path;
		
		return $this;	
	}
	
	public function getPath() {		
		if (Util::isAbsolutePath($this->path) || !($this->parent instanceof AbstractRoute))
			return $this->path;
		
		$path = $this->parent->getPath();
		if (is_null($path)) return $this->path;
		
		if (is_null($this->path)) return $path;
		
		return strpos($this->path, '[/') === 0 ? // [/:path]
			rtrim($path, '/') . $this->path :
			rtrim($path, '/') . '/' . ltrim($this->path, '/');		
	}

	public function setRoutable($routable) {
		$this->routable = (bool) $routable;
		
		return $this;
	}

	public function isRoutable() {
		return $this->routable;
	}
	
	public function setValues($values) {		
		$this->values = $values;
		
		return $this;
	}
	
	public function getValues(array $values = null) {
		$result = $this->getValuesContainerFactory()->create();
		
		if ($this->parent instanceof AbstractRoute)
			$result->setValues($this->parent->getValues());
		
		if ($this->values)
			$result->setValues($this->values);
	
		if ($values)
			$result->setValues($values);
		
		return new ArrayMap($result->toArray());
	}
	
	public function getValuesContainerFactory() {
		return $this->getParent()->getValuesContainerFactory();
	}
}
