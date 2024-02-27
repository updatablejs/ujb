<?php

namespace core\database\orm\mapper\selector;

use core\database\orm\mapper\Mapper,
	core\database\result\Result,
	core\database\result\FetchStyle,
	core\database\result\source\arraySource\AssociativeArraySource,
	core\database\assembler\Assembler,
	core\common\Util;

class Selector {

	protected $mapper;
	protected $sql;
	protected $validity;
	protected $assembler;
	protected $entityFactory;
	protected $entityFactoryEnabled = false;
	
	public function __construct(Mapper $mapper) {
		$this->mapper = $mapper;
		
		$this->sql = $mapper->getSql('select')
			->from($this->mapper->getTableSchema()->getName());
	}
	
	public function setEntityFactory(EntityFactory $entityFactory) {
		$this->entityFactory = $entityFactory;
		
		return $this;
	}
	
	public function getEntityFactory() {
		return $this->entityFactory ? 
			$this->entityFactory : new EntityFactory($this->mapper->getOrm(), $this->mapper->getTableSchema());
	}
	
	public function setEntityFactoryEnabled(bool $value) {
		$this->entityFactoryEnabled = $value;
		
		return $this;
	}
	
	public function isEntityFactoryEnabled() {
		return $this->entityFactoryEnabled;
	}
	
	public function setAssembler($assembler) {
		$this->assembler = $assembler;
		
		return $this;
	}
	
	public function setValidity($validity) {
		$this->validity = $validity;
		
		return $this;
	}
	
	public function __call($name, $args) {
		$this->sql->$name(...$args);
		
		return $this;
	}
	

	// Query

	public function get(int $limit = null, int $validity = null) {
		if (!is_null($limit))
			$this->sql->limit($limit);

		$this->validity = $validity;
		
		return $this->_get($this->sql);
	}

	public function getByPrimaryKey($values, int $validity = null) {
		if (count((array) $values) != count((array) $this->mapper->getTableSchema()->getPrimaryKey()))
			throw new \Exception('The number of values must be equal to the number of primaryKey fields.');
		
		$this->sql->where(array_combine(
			(array) $this->mapper->getTableSchema()->getPrimaryKey(), (array) $values));
			
		return $this->get(1, $validity)->fetch();	
	}

	public function getByConditions(array $conditions, int $limit = null, int $validity = null) {
		$this->sql->where($conditions);
		
		return $this->get($limit, $validity);
	}
	
	public function getOneByConditions(array $conditions, int $validity = null) {	
		return $this->getByConditions($conditions, 1, $validity)->fetch();
	}
	
	public function fetchAll($fetchStyle = null, int $validity = null) {
		$this->validity = $validity;
		
		return $this->_get($this->sql)->fetchAll($fetchStyle);
	}
	
	public function fetch($fetchStyle = null, int $validity = null) {
		$this->validity = $validity;
		
		return $this->_get($this->sql)->fetch($fetchStyle);
	}
	
	protected function _get($sql) {
		$result = $this->mapper->getDatabase()->prepare($sql)
			->setValidity($this->validity)
			->setAssembler($this->assembler)
			->execute();
		
		if ($this->isEntityFactoryEnabled()) {
			$result->setObjectFactory($this->getEntityFactory())
				->setFetchStyle(FetchStyle::Object);
		}
		
		return $result;
	}
}
