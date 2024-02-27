<?php

namespace core\database\orm\entity;

use core\database\orm\mapper\Mapper,
	core\database\orm\entity\collection\JoinEntityList,
	core\collection\ArrayMap,
	core\common\Arrayable,
	core\events\Events,
	library\App;

class Entity implements \JsonSerializable, Arrayable {

	use TraitArrayDepth;

	protected $mapper;
	
	public $events;
	public $joinFieldSyncObservable;
	
	protected $values = [];
	public $savedValues = [];
	
	public function __construct(iterable $values = null, $mapper = null) {
		if ($mapper) $this->setMapper($mapper);
		
		$this->events = new Events();
		$this->joinFieldSyncObservable = new JoinFieldSyncObservable();
		
		if ($values) $this->setValues($values);
	}
	
	public function setMapper($mapper) {
		$this->mapper = $mapper;
		
		return $this;
	}

	public function getMapper() {
		if (!$this->mapper) {
			$tableSchema = App::getOrm()->getSchema()->getTableByEntity(get_class($this));
			
			if (!$tableSchema) throw new \Exception(
				'Table schema has not been found, ' . get_class($this) . '.');
			
			$this->mapper = App::getOrm()->getMapper($tableSchema);
		}
		
		return $this->mapper;
	}
	
	public function getOrm() {
		return $this->getMapper()->getOrm();
	}
	
	public function getTableSchema() {
		return $this->getMapper()->getTableSchema();
	}

	public function has($key) {
		return array_key_exists($key, $this->values);
	}

	public function markAsSaved($keys = null) {
		$keys = !is_null($keys) ? 
			array_intersect((array) $keys, $this->getTableSchema()->getFields()) : $this->getTableSchema()->getFields();
		
		$this->savedValues = array_merge($this->savedValues, $this->getRaw($keys));
		
		return $this;
	}
	
	public function getUnsavedValues() {
		return array_diff_assoc(
			$this->getRaw($this->getTableSchema()->getFields()), $this->savedValues);
	}
	
	public function markAsUnsaved($keys = null) {
		if (!is_null($keys)) {
			foreach ((array) $keys as $key)
				unset($this->savedValues[$key]);
		}
		else
			$this->savedValues = [];
	
		return $this;
	}
	
	public function isSaved($keys = null) { // todo
		$values = $this->getRaw(is_null($keys) ? $this->getTableSchema()->getFields() : $keys);
		
		return $values ?
			!array_diff_assoc($values, $this->savedValues) : false;
	}

	public function setEvent($event, $handler, $values = null, $removeAfterUse = false) {
		$this->events->set($event, $handler, $values, $removeAfterUse);
		
		return $this;
	}

	public function getEvents() {
		return $this->events;
	}

	public function setPrimaryKey($values) {
		$values = array_combine(
			array_slice((array) $this->getTableSchema()->getPrimaryKey(), 0, count((array) $values)), 
			(array) $values);
		
		$this->set($values);
		
		return $this;
	}
	
	public function hasPrimaryKey() {
		foreach ((array) $this->getTableSchema()->getPrimaryKey() as $field) {
			if (!isset($this->values[$field])) return false;
		}
		
		return true;
	}
	
	public function toArray($arrayDepth = null) {
		$arrayDepth = $this->getArrayDepth($arrayDepth);
		
		$values = $this->getValues();
		foreach ($values as $key => $value) {
			if (!$this->getTableSchema()->hasJoin($key)) continue;
				
			if (!isset($arrayDepth[$key])) {
				unset($values[$key]);
				
				continue;
			}
				
			$values[$key] = $values[$key]->toArray($arrayDepth[$key]);
		}
		
		return $values;
	}
	
	public function jsonSerialize() {
		return $this->toArray();
	}

	public function __set($key, $value) {
		return $this->setValue($key, $value);
    }
	
	public function __get($key) {
		return $this->getValue($key);
    }
	
	public function __isset($key) {
		return $this->has($key);
	}


	// Set
	
	public function set($key, $value = null) {
		if (is_iterable($key))
			$this->setValues($key);
		else
			$this->setValue($key, $value);
			
		return $this;
	}

	public function setValues(iterable $values) {
		foreach ($values as $key => $value) {
			$this->setValue($key, $value);
		}
		
		return $this;
	}
	
	public function setValue($key, $value) {
		$method = 'set' . ucfirst($key);
		if (method_exists($this, $method) && !in_array($key, ['value', 'values']))
			$this->$method($value);
		else
			$this->_setValue($key, $value);
			
		return $this;	
	}	
	
	protected function _setValue($key, $value) {
		if (!$this->getTableSchema()->hasJoin($key)) {
			$this->values[$key] = $value;
		
			$this->joinFieldSyncObservable->notify($key, $value);
		}
		else
			$this->attach($key, $value);
	}
	
	public function setRaw($key, $value = null) {		
		if (is_iterable($key))
			$values = is_array($key) ? $key : $key->toArray();
		else 
			$values = [$key => $value];

		foreach ($values as $key => $value) {
			if ($this->getTableSchema()->hasJoin($key)) continue;
			
			$this->values[$key] = $value;
			
			$this->joinFieldSyncObservable->notify($key, $value);
		}

		return $this;
	}
	
	
	// Get
	
	public function get($key = null) {
		return is_string($key) ? 
			$this->getValue($key) : $this->getValues($key);
	}

	public function getValues(array $keys = null) {
		$keys = !is_null($keys) ? 
			array_intersect($keys, array_keys($this->values)) : array_keys($this->values);
		
		$result = [];
		foreach ($keys as $key) {			
			$result[$key] = $this->getValue($key);
		}
		
		return $result;
	}
	
	public function getValue($key) {
		$method = 'get' . ucfirst($key);
		return method_exists($this, $method) && !in_array($key, ['value', 'values']) ? 
			$this->$method() : $this->_getValue($key);
	}
	
	protected function _getValue($key) {		
		if (!$this->has($key)) 
			throw new \Exception($key . ' is undefined.');
		
		if ($this->getTableSchema()->hasJoin($key)) {
			if ($this->getTableSchema()->getJoin($key)->isCollection())
				return $this->values[$key];
			
			$storage = $this->values[$key]->getStorage();
			$storage->rewind();
			
			return $storage->current();
		}
		
		return $this->values[$key];
	}

	public function getRaw($keys = null) {
		if (is_null($keys)) return $this->values;
		
		$keys = array_intersect((array) $keys, array_keys($this->values));
		$result = [];
		foreach ($keys as $key)
			$result[$key] = $this->values[$key];
		
		return $result;
	}
	

	// Joins

	public function attach($join, $entity) {
		$this->getJoinEntityList($join)->attach($entity);
		
		return $this;
	}
	
	public function detach($join, $entity = null) {
		if (!$joinSchema = $this->getTableSchema()->getJoin($join))
			throw new \Exception('Unknown join ' . $join);
		
		if (isset($this->values[$join]))
			$this->values[$join]->detach($entity);
		
		return $this;	
	}

	public function getJoinEntityList($join) {
		if (!$joinSchema = $this->getTableSchema()->getJoin($join))
			throw new \Exception('Unknown join ' . $join);
		
		if (!$this->has($join))
			$this->values[$join] = new JoinEntityList($this, $joinSchema);
		
		return $this->values[$join];
	}
	
	
	// Storage
	
	public function saveWithTransaction() {
		return $this->getMapper()->saveWithTransaction($this);
	}
	
	public function save(array $values = null) {
		if ($values) $this->set($values);
		
		return $this->getMapper()->save($this);
	}
	
	public function insert(array $values = null) {
		if ($values) $this->set($values);
		
		return $this->getMapper()->insert($this);
	}
	
	public function update(array $values = null) {
		if ($values) $this->set($values);
		
		return $this->getMapper()->update($this);
	}
	
	public function delete() {
		return $this->getMapper()->delete($this);
	}
	
	public function saveAll() {
		return $this->getMapper()->saveAll($this);
	}
}
