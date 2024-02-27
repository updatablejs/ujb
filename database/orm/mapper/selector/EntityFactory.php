<?php

namespace core\database\orm\mapper\selector;

use core\database\result\ObjectFactory,
	core\database\result\source\Source,
	core\database\result\Result;

class EntityFactory implements ObjectFactory {

	protected $orm;
	protected $tableSchema;
	
	public function __construct($orm, $tableSchema) {
		$this->orm = $orm;
		$this->tableSchema = $tableSchema;
	}
	
	public function create($values) {
		return $values ? $this->getEntity($this->tableSchema, $values[1]) : null;
	}
	
	protected function getEntity($tableSchema, array $values) {
		$entities = [];
		foreach ($values as $key => $value) {
			if (!$joinSchema = $tableSchema->getJoin($key)) continue;
			
			unset($values[$key]);
				
			if (empty($value)) continue;
				
			if (!$joinSchema->isCollection()) {
				$entities[$key] = $this->getEntity($joinSchema->getExternalTable(), $value);
				
				continue;
			}
				
			foreach ($value as $v) {
				if ($joinSchema->hasJunction()) {
					$junction = $this->_getEntity($joinSchema->getJunction()->getTable(), $v['junction']);
						
					unset($v['junction']);
						
					$entities[$key][] = [$this->getEntity($joinSchema->getExternalTable(), $v), $junction];
				}
				else
					$entities[$key][] = $this->getEntity($joinSchema->getExternalTable(), $v);
			}
		}
		
		$entity = $this->_getEntity($tableSchema, $values);
		
		foreach ($entities as $key => $value) {
			$entity->attach($key, $value);
		}
		
		return $entity;
	}

	protected function _getEntity($tableSchema, $values) {
		return $this->orm->getEntity($tableSchema)
			->setRaw($values)
			->markAsSaved(array_keys($values));
	}
}
