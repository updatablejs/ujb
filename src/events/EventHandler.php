<?php

namespace ujb\events;

class EventHandler {

	protected $handler;
	protected $values;
	protected $removeAfterUse;

	// handler, values
	// [handler, context], values
	// class.method, values
	// [class, method], values
	public function __construct($handler, $values, $removeAfterUse) {
		if (is_string($handler) && strpos($handler, '.'))
			$handler = explode('.', $handler);
		
		if (is_array($handler) && count($handler) != 2) 
			throw new \Exception('Handler has a wrong format.');
			
		$this->handler = $handler;
		$this->values = $values;
		$this->removeAfterUse = $removeAfterUse;
	}
	
	public function trigger(array $values) {
		if (!$values)
			$values = is_object($this->values) ? [$this->values] : (array) $this->values;
	
		if (is_array($this->handler)) {
			if (is_callable(current($this->handler))) {
				list($handler, $context) = $this->handler;
					
				$handler = \Closure::bind($handler, $context, $context);
					
				return $handler(...$values);
			}
			elseif (is_object(current($this->handler))) {
				list($object, $method) = $this->handler;
					
				return $object->$method(...$values);
			}
			else {
				list($class, $method) = $handler;

				return (new $class())->$method(...$values);
			}
		}
		else {
			return $handler(...$values);	
		}
	}
	
	public function shouldBeRemoved() {
		return $this->removeAfterUse;
	}
}
