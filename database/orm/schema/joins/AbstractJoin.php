<?php

namespace core\database\orm\schema\joins;

use core\database\orm\schema\Table;

abstract class AbstractJoin {

	protected $name;
	protected $tableName;
	protected $junction;
	
	protected $parent;
	
	public function __construct(array $values) {
		if (!isset($values['name'])) 
			throw new \Exception('Name is missing from $values.');
			
		$this->name = $values['name'];
	
		$this->hydrate($values);
	}

	abstract protected function hydrate(array $values);
	
	abstract public function getType();
	
	abstract public function getInternalFields();
	
	abstract public function getExternalFields();
	
	abstract public function getOppositeJoin();
	
	abstract public function getJunction();
	
	abstract public function hasJunction();
	
	// Tabelul care contine uniunea este parent
	// in relatia cu tabelul din uniune.
	abstract public function isParent();

	public function setParent(Table $parent) {
		$this->parent = $parent;
		
		return $this;
	}

	public function getExternalTable() {
		return $this->parent->getSchema()
			->getTable($this->tableName);
	}
	
	public function isCollection() {
		return in_array($this->getType(), ['otm', 'mtm']);
	}
	
	public function getSchema() {
		return $this->parent->getSchema();
	}
	
	public function __get($name) {
		$method = 'get' . ucfirst($name);
		if (method_exists($this, $method))
			return $this->$method();
		elseif (property_exists($this, $name))
			return $this->$name;
		else 
			throw new \Exception('Undefined property via __get(): ' . $name);
    }
}
