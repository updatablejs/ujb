<?php

namespace ujb\router;

use ujb\http\Request;

class Segment extends AbstractRoute {

	protected $models = [];
	
	public function hydrate(array $values) {
		if (isset($values['models'])) {
			$this->setModels($values['models']);
			unset($values['models']);
		}
		
		parent::hydrate($values);
	}
	
	public function setRoute($key, $route) {
		if (!($route instanceof AbstractRoute))
			$route = array_merge(['models' => []], $route); // Change Literal to Segment.

		return parent::setRoute($key, $route);
	}
	
	public function setModels(array $models) {
		$this->models = $models;
	}
	
	public function getModels() {
		$models = [];
		$parent = $this->getParent();
		while ($parent) {
			if ($parent instanceof $this) {
				$models = $parent->getModels();
				
				break;
			}
			
			$parent = $parent instanceof AbstractRoute ? $parent->getParent() : null;
		}
		
		return array_merge($models, $this->models);
	}
	
	public function match(Request $request) {
		if (!$this->routable)
			return parent::match($request);

		$result = @preg_match($this->getRegex(), $request->getPath(), $matches);
		
		if ($result === false) throw new \Exception(
			'Route error: ' . $this->path . '<br />Regex: ' . $this->getRegex());
			
		$values = array_intersect_key($matches, $this->getModels());
	
		return $result ? 
			$this->getValues($values) : parent::match($request);	
	}
	
	public function build(array $values = null) {
		$values = $this->getValues($values);
		$models = $this->getModels();
		$path = $this->replaceOptionalSegments($this->getPath(), '$1');
		foreach ($values as $key => $value) {
			if (isset($models[$key]))
				$path = str_replace(':' . $key, $value, $path);
		}

		return $path;	
	}

	public function getRegex() {
		$regex = $this->replaceOptionalSegments('~^' . $this->getPath() . '$~', '($1)?');
		foreach ($this->getModels() as $key => $value) {
			$regex = str_replace(':' . $key, '(?<' . $key . '>' . $value . ')', $regex);
		}
		
		return $regex;
	}
	
	// Ce se afla intre paranteze patrate este optional.
	// ex. [/:page], [/:controller[/:action]]
	protected function replaceOptionalSegments($string, $replacement) {
		$regex = '/'
			. '\['
				. '([^\[]+)'	
			. '\]'
			. '/U';
		while (preg_match($regex, $string))
			$string = preg_replace($regex, $replacement, $string);
		
		return $string;
	}
}
