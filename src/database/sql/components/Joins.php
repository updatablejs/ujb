<?php

namespace ujb\database\sql\components;

class Joins extends AbstractComponent {

	public $joins = [];
	
	public function set(...$values) {
		if (is_array($values[0]))
			$this->setJoins($values[0]);
		else
			$this->setJoin(...$values);
	}
	
	public function setJoins(array $joins) {
		foreach ($joins as $join) {
			if (!is_array($join) || count($join) < 2) 
				throw new \Exception('Join type and table can not miss.');
			
			$this->setJoin(...$join);
		}
	}

	public function setJoin($type, $table, $conditions = null) {
		$type = strtoupper($type);
		
		if (is_array($table)) // ['table', 'alias']
			$table = (new Tables($this->sql))->set([$table]);
		
		if ($conditions)
			$conditions = new Conditions($this->sql, (array) $conditions);
			
		$this->joins[] = compact('type', 'table', 'conditions');
	}
	
	public function build() {
		$result = [];
		foreach ($this->joins as $join) {
			$result[] = !empty($join['conditions']) ?
				$join['type'] . ' ' . $join['table'] . ' ON ' . $join['conditions'] : 
				$join['type'] . ' ' . $join['table'];
		}
		
		return implode(' ', $result);
	}
	
	public function isEmpty() {
		return empty($this->joins);
	}
}
