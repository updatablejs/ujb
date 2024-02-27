<?php 

namespace core\database\sql\components;

class Fields extends AbstractComponent {

	public $fields = [];
	
	public function set($field, $alias = null) {
		if (is_array($field)) 
			$this->setFields($field) 
		else
			$this->setField($field, $alias);		
	}
	
	public function setField($field, $alias = null) {
		$this->fields[] = !is_null($alias) ? [$field, $alias] : [$field];
	}
	
	// setFields([['field', 'alias'], 'field' => 'alias', 'field']);
	public function setFields(array $fields) {
		foreach ($fields as $key => $value) {
			$this->fields[] = is_string($key) ?
				[$key, $value] : (array) $value;
		}
	}
	
	public function build() {
		$result = [];
		foreach ($this->fields as $field) {
			$result[] = count($field) > 1 ? 
				$field[0].' AS '.$field[1] : $field[0];
		}
		
		return implode(', ', $result);
	}
	
	public function isEmpty() {
		return empty($this->fields);
	}
}
