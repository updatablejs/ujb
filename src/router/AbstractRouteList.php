<?php

namespace ujb\router;

use ujb\router\valuesContainer\valuesContainerFactory\AbstractValuesContainerFactory,
	ujb\router\valuesContainer\valuesContainerFactory\ValuesContainerFactory,  
	ujb\http\Request;

abstract class AbstractRouteList {

	protected $routes = [];
	protected $valuesContainerFactory;

	public function match(Request $request) {
		foreach ($this->routes as $route) {
			if ($result = $route->match($request))
				return $result;
		}
		
		return null;
	}
	
	public function setRoutes(array $routes) {
		foreach ($routes as $key => $route)
			$this->setRoute($key, $route);
		
		return $this;
	}
	
	public function setRoute($key, $route) {
		if (!($route instanceof AbstractRoute))
			$route = RouteFactory::create($route);
		
		$this->routes[$key] = $route->setParent($this);
		
		return $this;	
	}
	
	public function getRoute($key) {
		return isset($this->routes[$key]) ? 
			$this->routes[$key] : null;
	}
	
	public function getValuesContainerFactory() {
		if (!$this->valuesContainerFactory)
			$this->valuesContainerFactory = new ValuesContainerFactory();
		
		return $this->valuesContainerFactory;
	}
	
	public function setValuesContainerFactory(AbstractValuesContainerFactory $valuesContainerFactory) {
		$this->valuesContainerFactory = $valuesContainerFactory;
		
		return $this;
	}
}
