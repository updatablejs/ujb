<?php

namespace ujb\database\orm\schema\joins;

use ujb\database\orm\schema\Schema;

class Junction {

	protected $tableName;
	protected $internalFields;
	protected $externalFields;
	
	protected $parent;
	
	public function __construct(array $values) {
		if (!isset($values['table'])) 
			throw new \Exception('Table is missing from $values.');

		$this->tableName = $values['table'];
		
		if (isset($values['internalFields']))
			$this->internalFields = $values['internalFields'];
			
		if (isset($values['externalFields']))
			$this->externalFields = $values['externalFields'];
	}
	
	public function setParent(AbstractJoin $parent) {
		$this->parent = $parent;
		
		return $this;
	}
	
	public function getTableName() {
		return $this->tableName;
	}
	
	public function getTable() {
		return $this->getSchema()->getTable($this->tableName);
	}
	
	public function getInternalFields() {
		return $this->internalFields ?
			$this->internalFields : $this->parent->getInternalFields();
	}
	
	public function getExternalFields() {
		return $this->externalFields ?
			$this->externalFields : $this->parent->getExternalFields();
	}
	
	public function hasInternalFields() {
		return !empty($this->internalFields);
	}
	
	public function hasExternalFields() {
		return !empty($this->externalFields);
	}
	
	public function getSchema() {
		return $this->parent->getSchema();
	}
	
	public function __get($name) {
		$method = 'get' . ucfirst($name);
		if (!method_exists($this, $method))
			'Undefined property via __get(): ' . $name;
		
		return $this->$method();
    }
}
