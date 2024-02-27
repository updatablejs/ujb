<?php

namespace core\database\statement;

use core\database\result\Result,
	core\database\result\FetchStyle,
	core\database\result\source\arraySource\NumericArraySource,
	core\database\adapters\StatementSource,
	core\database\assembler\AssemblerFactory,
	core\database\assembler\Assembler,
	core\database\Database,
	core\database\sql\Sql,
	core\common\Chronometer,
	core\common\Util;

class Statement {

	protected $database;
	protected $sql;
	protected $validity;
	protected $assembler;
	protected $metadata;
	protected $params = [];
	protected $options = [];
	
	protected $statement;

	public $queryInfo;

	public function __construct(Database $database, $sql) {
		$this->database = $database;
		
		if ($sql instanceof Sql) {
			$this->sql = $sql->build();
			$this->setParams($sql->getParams());
		}
		else
			$this->sql = $sql;
	}

	public function setParams(array $params) {
		foreach ($params as $key => $values) {
			if (!is_array($values))
				$values = [$values];
		
			$this->setParam($key, ...$values);
		}
		
		return $this;
	}
	
	public function setParam($key, $value = null, int $type = Parameter::String) {
		if (func_num_args() == 1)
			$this->params[] = [$key, $type];
		else 
			$this->params[$key] = [$value, $type];
		
		return $this;
	}

	public function getParams() {
		return $this->params;
	}

	public function setValidity($validity) {
		$this->validity = $validity;
		
		return $this;
	}
	
	public function getValidity() {
		return $this->validity;
	}

	public function setAssembler($assembler = null) {
		$this->assembler = (is_null($assembler) || $assembler instanceof Assembler) ? 
			$assembler : AssemblerFactory::create($assembler);

		return $this;
	}
	
	public function getAssembler() {
		return $this->assembler;
	}

	public function setMetadata(array $metadata = null) {
		$this->metadata = $metadata;
		
		return $this;
	}
	
	public function getMetadata(array $metadata = []) {
		if ($this->metadata) {
			foreach ($this->metadata as $index => $_metadata) {
				$metadata[$index] = isset($metadata[$index]) ? 
					array_merge($metadata[$index], $_metadata) : $_metadata;
			}
		}
		
		return $metadata;
	}
	
	public function setOptions(array $options) {
		$this->options = $options;

		return $this;
	}
	
	public function getOptions() {
		return $this->options;
	}
	
	public function getStatement() {
		if (!$this->statement)
			$this->statement = $this->database->getDriver()->prepare($this->sql, $this->options);
		
		return $this->statement;
	}
	
	public function getQueryInfo() {
		return $this->queryInfo;
	}
	
	public function getCache() {
		return $this->database->getCache();
	}


	// Execute
	
	public function execute() {
		$chronometer = new Chronometer();
		
		$this->queryInfo = $this->database->queries[] = new QueryInfo($this->sql);
		
		if (!is_null($this->validity)) {
			$key = $this->getCacheKey();
			
			if ($this->getCache()->needUpdate($key, $this->validity)) {
				$result = $this->_execute();
				if ($result->getSource() instanceof StatementSource) {
					$result = new Result(new NumericArraySource(
						$result->fetchAll(FetchStyle::Numeric), $result->getMetadata()));
				}
				
				$this->getCache()->set($key, $result);
			}
			else {
				$result = $this->getCache()->get($key);
			
				$this->queryInfo->fromCache = true;
			}
		}
		else {
			$result = $this->_execute();
		}

		$this->queryInfo->executionTime = $chronometer->stop();
		
		return $result;
	}
	
	protected function getCacheKey() {
		// serialize($this->assembler) Serialization of 'Closure' is not allowed.
		return $this->sql . ';' . json_encode($this->params) . ';' . json_encode($this->metadata);
	}
	
	protected function _execute() {		
		$this->getStatement()->setParams($this->getParams());
		
		$source = $this->getStatement()->execute();
		if ($this->metadata) {
			$source->setMetadata(
				$this->getMetadata($source->getMetadata()));
		}
		
		return $this->assembler ? 
			$this->assembler->assemble(new Result($source)) : new Result($source);
	}
}
