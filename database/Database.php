<?php

namespace core\database;

use core\database\statement\Statement,
	core\database\adapters\Driver,
	core\database\sql\SqlFactory,
	core\database\sql\Sql,
	core\common\Chronometer,
	core\cache\Cache,
	core\common\Util
	
	core\common\Util
	;

class Database {
	
	private $driver;
	public $queries = [];
	protected $cache;
	
	public function __construct(Driver $driver) {		
		$this->driver = $driver;
	}
	
	public function open() {		
		return $this->driver->open();
	}
	
	public function close() {
		return $this->driver->close();
	}
	
	public function quote($value) {
		if (is_int($value) || is_float($value)) 
			return $value;
		
		if (is_array($value)) {
			foreach ($value as $k => $v)
				$value[$k] = $this->quote($v);
			
			return $value;
		}
		
		return !is_null($value) ?
			$this->driver->quote($value) : 'null';
	}
	
	public function getLastInsertId() {
		return $this->driver->getLastInsertId();
	}
	
	public function getDriver() {
		return $this->driver;
	}
	
	public function setCache(Cache $cache) {
		$this->cache = $cache;
		
		return $this;
	}
	
	public function getCache() {
		return $this->cache;
	}
	
	public function getSql($type) {
		return SqlFactory::create($type, $this);
	}
	
	public function getQueries() {
		return $this->queries;
	}
	
	public function getExecutionTime() {
		return array_reduce($this->queries, function($total, $query) {
			return $total + $query->executionTime;
		});
	}
	
	public function __destruct() {
        $this->close();
    }
	
	
	// Query
	
	public function prepare($sql, $params = null) {
		$statement = new Statement($this, $sql);
		if ($params)
			$statement->setParams((array) $params);
		
		return $statement;
	}
	
	public function query($sql, $params = null, $validity = null, $assembler = null, $metadata = null) {
		return $this->prepare($sql, $params)
			->setValidity($validity)
			->setAssembler($assembler)
			->setMetadata($metadata)
			->execute();		
	}


	// Transaction 
	
	public function beginTransaction() {
		return $this->driver->beginTransaction();
	}
	
	public function rollBack() {
		return $this->driver->rollBack();
	}
	
	public function commit() {
		return $this->driver->commit();
	}
	
    public function transaction($callback) {
		$this->beginTransaction();
		try {  
			call_user_func($callback);
		}
		catch (\Exception $e) {
  			$this->rollBack();
 			
			throw $e;
		}
		
		return $this->commit();
    }
}
