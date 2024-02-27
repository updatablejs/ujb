<?php

namespace core\database\orm;

use core\database\orm\mapper\Mapper,
	core\database\orm\entity\Entity,
	core\database\orm\schema\Schema,
	core\database\orm\schema\Table,
	core\collection\ArrayMap;
	
class Orm {

	protected $database;
	protected $schema;
	
	public function __construct($database, $schema) {
		$this->setDatabase($database)
			->setSchema($schema);
	}
	
	public function setSchema($schema) {
		if (is_array($schema))
			$schema = new Schema($schema);
		
		if (!($schema instanceof Schema)) 
			throw new \Exception('Schema must be array or object of class Schema.');
		
		$this->schema = $schema;
		
		return $this;
	}
	
	public function getSchema() {
		return $this->schema;
	}
	
	public function setDatabase($database) {
		$this->database = $database;
		
		return $this;
	}
	
	public function getDatabase() {
		return $this->database;
	}
	
	public function getMapper($table) {
		$tableSchema = $table instanceof Table ? $table : $this->schema->getTable($table);
		
		if (!$tableSchema) 
			throw new \Exception('Unknown table ' . $table);
		
		if (!$class = $tableSchema->getMapper())
			$class = 'core\database\orm\mapper\Mapper';
		
		return new $class($this, $tableSchema);
	}

	public function getEntity($table, $values = null) {
		return $this->getMapper($table)->getEntity($values);
	}
}
