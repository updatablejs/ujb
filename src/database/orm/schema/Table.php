<?php

namespace ujb\database\orm\schema;

use ujb\database\orm\schema\joins\JoinFactory;

class Table {

	protected $name;
	protected $fields = [];
	protected $primaryKey;
	
	protected $joins = [];
	
	protected $mapper;
	protected $entity;
	
	protected $parent;
	
	public function __construct(array $values) {
		if (!isset($values['name']) || !isset($values['fields']) || !isset($values['primaryKey']))
			throw new \Exception('Incomplete $values.');
		
		$this->name = $values['name'];
		$this->fields = $values['fields'];
		$this->primaryKey = $values['primaryKey'];

		if (isset($values['mapper'])) $this->mapper = $values['mapper'];
		if (isset($values['entity'])) $this->entity = $values['entity'];
		if (isset($values['joins'])) $this->setJoins($values['joins']);
	}
	
	public function getPrimaryKey() {
		return $this->primaryKey;
	}
	
	public function isPrimaryKeyComposite() {
		return count((array) $this->primaryKey) > 1;
	}
	
	public function isPartOfPrimaryKey($field) {
		return in_array($field, (array) $this->getPrimaryKey());	
	}
	
	public function getName() {
		return $this->name;
	}

	public function getSchema() {
		return $this->parent;
	}
	
	public function getMapper() {
		return $this->mapper;
	}
	
	public function getEntity() {
		return $this->entity;
	}

	public function hasMapper() {
		return !empty($this->mapper);
	}
	
	public function hasEntity() {
		return !empty($this->entity);
	}

	public function setParent(Schema $parent) {
		$this->parent = $parent;
		
		return $this;
	}

	public function __get($name) {
		if (property_exists($this, $name)) {
			$method = 'get' . ucfirst($name);
			return method_exists($this, $method) ? 
				$this->$method() : $this->$name;
        }
		
        throw new \Exception(
            'Undefined property via __get(): ' . $name);
	}


	// Joins

	public function setJoins(array $joins) {
		foreach ($joins as $name => $values) {
			$values['name'] = $name;
			$this->joins[$name] = JoinFactory::create($values)->setParent($this);
		}
	}
	
	public function getJoins() {
		return $this->joins;
	}
		
	public function getJoin($name) {
		return isset($this->joins[$name]) ?
			 $this->joins[$name] : null;
	}
	
	public function hasJoins() {
		return !empty($this->joins);
	}
	
	public function hasJoin($name) {
		return isset($this->joins[$name]);
	}
	
	
	// Fields
	
	public function setFields(array $fields) {
		$this->fields = $fields;
	}
	
	public function getFields() {
		return $this->fields;
	}
	
	public function hasFields($fields) {
		foreach ((array) $fields as $field) {
			if (!$this->hasField($field)) 
				return false;
		}
		
		return true;
	}
	
	public function hasField($field) {
		return in_array($field, $this->fields, true);
	}
}
