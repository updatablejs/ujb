<?php

namespace core\database\adapters\pdo;

use core\database\adapters\Statement as StatementInterface,
	core\database\statement\Parameter,
	core\common\Util;

class Statement implements StatementInterface {

	protected $statement;
	protected $driverType;
	
	public function __construct($statement, $driverType = 'mysql') {		
		$this->statement = $statement;
		$this->driverType = $driverType;
	}
	
	public function setParam($key, $value, int $type = Parameter::String) {
		$this->statement->bindValue($key, $value, $this->translateParameterType($type));
		
		return $this;
	}
	
	public function setParams(array $params) {
		// PDO parameters are 1 based.
		if (Util::isNumericArray($params)) { 
			array_unshift($params, '');
			unset($params[0]);
		}
		
		foreach ($params as $key => $value) {
			$this->setParam($key, $value[0], $value[1]);
		}
		
		return $this;
	}

	public function execute() {
		if (!$this->statement->execute())
			throw new \Exception($this->statement->errorInfo());

		return new Source($this->statement, $this->getMetadata());
	}
	
	public function getMetadata() {
		$result = [];
		if ($this->statement->columnCount()) {
			foreach (range(0, $this->statement->columnCount() - 1) as $index) {
				$raw = $this->statement->getColumnMeta($index);
				$result[] = [
					'table' => $raw['table'],
					'name' => $raw['name'],
					'type' => $this->translateColumnType($raw['native_type'])
				];
			}
		}
		
		return $result;
	}
	
	protected function translateParameterType($type) {
		$translations = [
			Parameter::String => \PDO::PARAM_STR,
			Parameter::Integer => \PDO::PARAM_INT,
			Parameter::Double => \PDO::PARAM_STR,
			Parameter::Blob => \PDO::PARAM_STR,
			Parameter::Bool => \PDO::PARAM_BOOL,
			Parameter::Null => \PDO::PARAM_NULL
		];
		
		return $translations[$type];
	}
	
	protected function translateColumnType($type) {
		switch ($this->driverType) { 
			case 'mysql':
				if (in_array($type, ['LONG', 'TINY', 'SHORT', 'LONGLONG', 'INT24']))
					return 'integer';
				
				elseif (in_array($type, ['FLOAT', 'DOUBLE', 'NEWDECIMAL'])) 
					return 'float';
					
				elseif (in_array($type, ['DATE', 'TIME', 'DATETIME', 'TIMESTAMP', 'YEAR', 'ENUM', 
					'BLOB', 'LONGBLOB', 'MEDIUMBLOB', 'TINYBLOB', 'STRING', 'VAR_STRING', 'SET'])) 
					
					return 'string';
		
				else
					throw new \Exception('Unknown type ' . $type);	
			
			default:
				throw new \Exception('Unknown driver type ' . $driverType);	
		} 		
	}
}
	