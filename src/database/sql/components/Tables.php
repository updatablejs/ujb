<?php

namespace ujb\database\sql\components;

class Tables extends AbstractComponent {
	
	public $tables = [];

	public function set($table, $alias = null) {
		return is_array($table) ? 
			$this->setTables($table) : $this->setTable($table, $alias);
	}
	
	public function setTable($table, $alias = null) {
		$this->tables[] = !is_null($alias) ? 
			[$table, $alias] : [$table];
			
		return $this;
	}
	
	// setTables([['table', 'alias'], 'table' => 'alias', 'table']);
	public function setTables(array $tables) {
		foreach ($tables as $key => $value) {
			$this->tables[] = is_string($key) ?
				[$key, $value] : (array) $value;
		}
		
		return $this;
	}

	public function build() {
		$result = [];
		foreach ($this->tables as $table) {
			$result[] = (count($table) > 1) ? 
				$table[0].' AS '.$table[1] : $table[0];
		}
		
		return implode(', ', $result);
	}
	
	public function isEmpty() {
		return empty($this->tables);
	}
}
