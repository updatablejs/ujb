<?php

namespace ujb\database\sql;

use ujb\database\sql\components\ComponentFactory;

abstract class Sql {
	
	protected $database;
	
	protected $components = [];
	protected $translations = [
		'from' => 'tables', 
		'into' => 'tables',
		'table' => 'tables',
		'join' => 'joins',
		'onDuplicateUpdate' => 'replacementValues'
	];
	
  	/**
     * Parameter values.
     * @var array
     */
	protected $params = [];
	protected $paramBindingEnabled = true;

	public function __construct($database = null) {
		$this->database = $database;
	}
	
	abstract protected function _build();
	
	public function build() {
		$query = $this->_build();
		if (!$this->isParamBindingEnabled()) {
			$query = $this->replaceParameterPlaceholders($query);
			$this->clearParams();
		}
		
		return $query;
	}
	
	public function setDatabase($database) {
		$this->database = $database;
		
		return $this;
	}

	public function getDatabase() {
		return $this->database;
	}

	public function query($validity = null) {
		return $this->getDatabase()->query($this, null, $validity);
	}
	
	public function prepare() {
		return $this->getDatabase()
			->prepare($this);
	}
	
	public function quote($value) {		
		return $this->getDatabase()->quote($value);
	}
	
	public function __toString() {
		return $this->build();
	}
	
	public function __call($name, $args) {
		$key = $this->translate($name);
		if ($component = $this->getComponent($key))
			$component->set(...$args);	
		else
			$this->components[$key] = ComponentFactory::create($key, $args, $this);
			
		return $this;
	}
	

	// Params
	
	public function setParam($value) {	
		$this->params[] = $value;
		
		return $this;
	}
	
	public function setParams(array $values) {
		foreach ($values as $value)
			$this->params[] = $value;
		
		return $this;
	}
	
	public function getParams() {
		return $this->params;
	}

	public function hasParams() {
		return !empty($this->params);
	}

	public function clearParams() {
		$this->params = [];
	}

	public function isParamBindingEnabled() {
		return $this->paramBindingEnabled;
	}
	
	public function setParamBindingEnabled(bool $value) {
		$this->paramBindingEnabled = $value;
		
		return $this;
	}

	public function replaceParameterPlaceholders($query) {
		$params = $this->quote($this->getParams());
		foreach ($params as $value) {
			$query = preg_replace('~\?~', $value, $query, 1);
		}

		return $query;
	}
	
	
	// Components
	
	public function get($key) {
		return $this->getComponent($key);
	}
	
	public function getComponent($key) {
		return $this->hasComponent($key) ? $this->components[$key] : null;
	}
	
	public function isEmpty($key) {
		return !isset($this->components[$key]) || $this->components[$key]->isEmpty();
	}

	public function hasComponent($key) {
		return isset($this->components[$key]);
	}

	public function translate($value) {
		return isset($this->translations[$value]) ? $this->translations[$value] : $value;
	}
}
