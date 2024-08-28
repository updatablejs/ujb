<?php

namespace ujb\router;

class RouteFactory {
	
	static public function create(array $values) {
		$type = isset($values['models']) ? 'Segment' : 'Literal';
		
		$class = __NAMESPACE__ . '\\' . $type;
		
		return new $class($values);	
	}
}
