<?php

namespace ujb\router;

class Router extends AbstractRouteList {
	
	public function __construct(array $routes = null) {
		if ($routes) 
			$this->setRoutes($routes);
	}

	public function getRoute($key) {
		$keys = explode('.', $key);
		
		if (!$route = parent::getRoute(array_shift($keys))) return null;
	
		foreach ($keys as $key) {
			if (!$route = $route->getRoute($key)) return null;
		}
		
		return $route;
	}
}
