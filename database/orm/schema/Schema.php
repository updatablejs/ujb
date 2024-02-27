<?php

namespace core\database\orm\schema;

class Schema {

	protected $tables = [];
	
	public function __construct(array $tables = null) {
		if ($tables) $this->setTables($tables);
	}
	
	public function setTables(array $tables) {
		foreach ($tables as $name => $values) {
			$values['name'] = $name;
			$this->tables[$name] = (new Table($values))->setParent($this);
		} 
		
		return $this;
	}
	
	public function getTable($name) {
		return isset($this->tables[$name]) ? 
			 $this->tables[$name] : null;
	}
	
	public function getTableByMapper(string $mapper) {
		foreach ($this->tables as $table) {
			if ($mapper === $table->mapper)
				return $table;
		}
		
		return null;
	}
	
	public function getTableByEntity(string $entity) {		
		foreach ($this->tables as $table) {
			if ($entity === $table->entity)
				return $table;
		} 
		
		return null;
	}
	
	public function verify() {
		foreach ($this->tables as $table) {
			if (!$table->hasJoins()) continue;
			
			$error = 'Incorrect database schema:';

			foreach ($table->joins as $join) {				
				if (!$externalTable = $this->getTable($join->tableName))
					throw new \Exception($error . ' table ' . $join->tableName . ' is missing.');
				
				if ($join instanceof joins\Reference) {
					if (!$externalTable->hasJoin($join->joinName))
						throw new \Exception($error . ' join ' . $join->joinName . ' is missing.');
					
					continue;
				}
				
				if (!$table->hasFields($join->internalFields)) 
					throw new \Exception($error . ' fields $join->internalFields are missing from ' 
						. $table->getName() . 'table.');
				
				if (!$externalTable->hasFields($join->externalFields)) 
					throw new \Exception($error . ' fields $join->externalFields are missing from '
						. $externalTable->getName() . 'table.');
	
				if (!$join->hasJunction()) {
					if (count((array) $join->internalFields) != count((array) $join->externalFields))
						throw new \Exception($error . ' the number of internal and external fields must be equal.');
				}
				
				
				// junction
				
				if (!$join->hasJunction()) continue;
					
				$junction = $join->getJunction();
				
				if (!$junctionTable = $this->getTable($junction->tableName))
					throw new \Exception($error . ' table ' . $junction->table . ' is missing.');
				
				if (!$junctionTable->hasFields($junction->internalFields)) 
					throw new \Exception($error . ' fields $junction->internalFields are missing from '
						. $junctionTable->getName() . 'table.');
						
				if (count((array) $join->internalFields) != count((array) $junction->internalFields))
					throw new \Exception($error . ' the number of internal fields in the table and junction must be equal.');
				
				if (!$junctionTable->hasFields($junction->externalFields)) 
					throw new \Exception($error . ' fields $junction->externalFields are missing from '
						. $junctionTable->getName() . 'table.');
				
				if (count((array) $junction->externalFields) != count((array) $join->externalFields))
					throw new \Exception($error . ' the number of external fields in junction and external table must be equal.');
			}
		}
	}
}
