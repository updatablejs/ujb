<?php

namespace ujb\events;

use ujb\common\Util;
	
class Events {

	protected $events = [];
	protected $result = null;
	
	public function __construct(array $handlers = null) {
		if ($handlers) $this->setHandlers($handlers);
	}

	public function set($event, $handler, $values = null, $removeAfterUse = false) {
		return $this->setHandler($event, $handler, $values, $removeAfterUse);
	}

	public function setSingleUseHandler($event, $handler, $values = null) {
		return $this->setHandler($event, $handler, $values, true);
	}

	public function setHandler($event, $handler, $values = null, $removeAfterUse = false) {
		return $this->events[$event][] = new EventHandler($handler, $values, $removeAfterUse);
	}
	
	/*[
		['event', 'handler', 'values'],
		
		'event' => 'handler', // Not numeric array, it is difficult to differentiate a handler from a list of handlers.
		
		'event' => ['handler', ['class', 'method']],
		
		'event' => [
			'handler' => '',
			'values' => []
		],
		
		'event' => [
			['handler' => '',
				'values' => []],
						
			['handler' => '',
				'values' => []]
		]
	]*/
	public function setHandlers(array $handlers) {
		foreach ($handlers as $key => $value) {
			if (is_int($key))
				$this->setHandler(...$value);
			else 
				$this->_setHandlers($key, Util::isNumericArray($value) ? $value : [$value]);
		}
		
		return $this;
	}
	
	protected function _setHandlers($event, array $handlers) {
		foreach ($handlers as $handler) {
			if (!is_array($handler) || Util::isNumericArray($handler)) {
				$this->setHandler($event, $handler);	
			}
			else {
				['handler' => $handler, 'values' => $values,
					'removeAfterUse' => $removeAfterUse] = $handler + ['values' => null, 'removeAfterUse' => false];
					
				$this->setHandler($event, $handler, $values, $removeAfterUse);	
			}
		}
		
		return $this;
	}
	
	public function trigger($event, ...$values) {
		$this->result = null;
		
		if (empty($this->events[$event])) return $this;
			
		foreach ($this->events[$event] as $index => $handler) {
			$result = $handler->trigger($values);

			if ($handler->shouldBeRemoved())
				unset($this->events[$event][$index]);
			
			// Break on result. 
			if (!is_null($result)) {
				$this->result = $result;
				
				break;
			} 
		}
		
		return $this;
	}
	
	public function getResult() {
		return $this->result;
	}
	
	public function remove($event, EventHandler $handler = null) {
		if (isset($this->events[$event])) {
			if ($handler) {
				$this->events[$event] = array_values(array_filter($this->events[$event], function($h) use ($handler) {
					return $h !== $handler;
				}));
			}
			else 
				unset($this->events[$event]);
		}
		
		return $this;
	}
	
	public function clear() {
		$this->events = [];
		
		return $this;
	}
}
