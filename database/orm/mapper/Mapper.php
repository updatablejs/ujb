<?php

namespace core\database\orm\mapper;

use core\database\orm\Orm,
	core\database\orm\mapper\selector\Selector,
	core\collection\ArrayMap,
	library\App;

class Mapper {	

	protected $orm;
	protected $tableSchema;
	
	public function __construct($orm = null, $tableSchema = null) {
		if ($orm) $this->setOrm($orm);
		if ($tableSchema) $this->setTableSchema($tableSchema);
	}
	
	public function setOrm(Orm $orm) {
		$this->orm = $orm;
		
		return $this;
	}
	
	public function getOrm() {
		return $this->orm ? $this->orm : App::getOrm();
	}
	
	public function getMapper($table) {
		return $this->getOrm()->getMapper($table);
	}
	
	public function setTableSchema($tableSchema) {
		$this->tableSchema = $tableSchema;
		
		return $this;
	}
	
	public function getTableSchema() {
		if (empty($this->tableSchema)) {
			$tableSchema = $this->getDatabaseSchema()->getTableByMapper(get_class($this));
			
			if (!$tableSchema) throw new \Exception(
				'Table schema has not been found, ' . get_class($this) . '.');
			
			$this->tableSchema = $tableSchema;
		} 
		
		return $this->tableSchema;
	}
	
	public function getDatabase() {
		return $this->getOrm()->getDatabase();
	}
	
	public function getDatabaseSchema() {
		return $this->getOrm()->getSchema();
	}
	
	public function getSql($type) {
		return $this->getDatabase()->getSql($type);
	}
	
	public function getEntity(iterable $values = null) {
		if (!$class = $this->getTableSchema()->getEntity())
			$class = 'core\database\orm\entity\Entity';
		
		return new $class($values, $this);
	}
	
	public function prepare($sql, $params = null) {
		return $this->getDatabase()->prepare($sql, $params);
	}
	
	public function query($sql, $params = null, $validity = null, $assembler = null, $metadata = null) {
		return $this->getDatabase()->query($sql, $params, $validity, $assembler, $metadata);	
	}
	
	public function getSelector() {
		return new Selector($this);
	}

	public function __call($name, $args) {
		$methods = ['get', 'getByPrimaryKey', 'getByConditions', 'getOneByConditions'];
		if (in_array($name, $methods))
			return $this->getSelector()->$name(...$args);
		
		throw new \Exception(
            'Undefined method via __call(): ' . $name);
	}


	// Storage
	
	public function saveWithTransaction($entity) {
		try {  
			$this->getDatabase()->beginTransaction();
			
			$this->saveAll($entity);
			
			$this->getDatabase()->commit();
		}
		catch (\Exception $e) {
  			$this->getDatabase()->rollBack();
			
			throw new \Exception($e);
		}
	}
	
	public function save($entity, array $values = null) {
		if ($values) $entity->set($values);
		
		return $entity->hasPrimaryKey() && $entity->isSaved($this->getTableSchema()->getPrimaryKey()) ? 
			$this->update($entity) : $this->insert($entity);
	}
	
	public function insert($entity, array $onDuplicateUpdate = []) {		
		if (is_array($entity)) return $this->_insert($entity, $onDuplicateUpdate); 
		
		if ($entity->isSaved()) {
			$entity->getEvents()->trigger('afterSave');

			return true;
		}

		$result = $this->getSql('insert')
			->into($this->getTableSchema()->getName())
			->values($entity->getUnsavedValues())
			->onDuplicateUpdate($onDuplicateUpdate)
			->limit(1)
			->query();
		
		if ($this->getDatabase()->getLastInsertId()) {
			$primaryKey = (array) $this->getTableSchema()->getPrimaryKey();
			
			$entity->setRaw(array_shift($primaryKey), $this->getDatabase()->getLastInsertId());
		}
		
		$entity->markAsSaved()->getEvents()->trigger('afterSave');

		return (bool) $result->count(); 
	}
	
	protected function _insert(array $values, array $onDuplicateUpdate = []) {
		return $this->getSql('insert')
			->into($this->getTableSchema()->getName())
			->values($values)
			->onDuplicateUpdate($onDuplicateUpdate)
			->query()
			->count(); 
	}
	
	public function update($entity) {	
		if (!$entity->hasPrimaryKey()) 
			return $this->insert($entity);
			
		if ($entity->isSaved()) {
			$entity->getEvents()->trigger('afterSave');
			
			return true;
		}
	
		$result = $this->getSql('update')
			->table($this->getTableSchema()->getName())
			->set($entity->getUnsavedValues())
			->where($entity->getRaw($this->getTableSchema()->getPrimaryKey()))
			->limit(1)
			->query();

		$entity->markAsSaved()->getEvents()->trigger('afterSave');
			
		return (bool) $result->count();
	}
	
	public function delete($entity) {
		if (!$entity->hasPrimaryKey()) return false;
		
		return (bool) $this->getSql('delete')
			->from($this->getTableSchema()->getName())
			->where($entity->getRaw($this->getTableSchema()->getPrimaryKey()))
			->limit(1)
			->query()
			->count();
	}
	
	public function deleteByPrimaryKey($values) {
		if (count((array) $values) != count((array) $this->getTableSchema()->getPrimaryKey()))
			throw new \Exception('The number of values must be equal to the number of primaryKey fields.');
		
		return (bool) $this->getSql('delete')
			->from($this->getTableSchema()->getName())
			->where(
				array_combine((array) $this->getTableSchema()->getPrimaryKey(), (array) $values)
			)
			->limit(1)
			->query()
			->count();
	}
	
	public function saveAll($entity) {
		$list = $this->prepareEntityList($entity);
		
		do {
			$i = 0;
			foreach ($list as $e) {
				if ($e->isSaved()) continue;
				$e->save();
				
				$i = 1;
			}
		} 
		while ($i);
	}
	
	protected function prepareEntityList($entity, &$list = []) {
		$list[] = $entity;
		$tableSchema = $entity->getTableSchema();
		foreach ($entity->getRaw(array_keys($tableSchema->getJoins())) as $key => $value) {
			$storage = $value->getStorage();
			foreach ($storage as $entity) {
				if (!in_array($entity, $list, true))
					$this->prepareEntityList($entity, $list);
				
				if ($tableSchema->getJoin($key)->hasJunction()) {
					$junction = $storage->getInfo();
					if (!in_array($junction, $list, true))
						$this->prepareEntityList($junction, $list);
				}
			}
		}
		
		return $list;
	}
}
