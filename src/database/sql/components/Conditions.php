<?php

namespace ujb\database\sql\components;

class Conditions extends AbstractComponent {

	public $conditions = [];

	public $logicalOperators = ['AND', 'OR'];

	public function __construct($sql, array $conditions = null) {
		$this->sql = $sql;
		if ($conditions) 
			$this->setConditions($conditions);
	}

	// condition
	// condition, value 
	// logicalOperator, condition, value
	// logicalOperator, conditions 
    // conditions
	public function set(...$values) {
		if (is_array($values[0]))
			$this->setConditions($values[0]);
		else
			$this->setCondition(...$values);
	}

	public function setConditions(array $conditions) {
		foreach ($conditions as $key => $value) {
			$condition = is_int($key) ? (array) $value : [$key, $value];
 			
			$this->conditions[] = $this->prepareCondition($condition);
		}
	}
	
	public function setCondition(...$values) {
		$this->conditions[] = $this->prepareCondition($values);
	}
	
	public function prepareCondition(array $condition) {
		if (!$condition) 
			throw new \Exception('Condition cannot be empty.');
		
		if (!$this->isLogicalOperator($condition[0]))
			array_unshift($condition, 'AND');
		
		// setCondition('logicalOperator', conditions);  
		if (count($condition) == 2 && is_array($condition[1])) {
			$class = get_class($this);
			
			return [$condition[0], new $class($this->sql, $condition[1])];
		}
		
		if (count($condition) == 3 && strpos($condition[1], '?') === false) {
			$condition[1] = $condition[1] . ' = ?';
		}
		
		// setCondition('logicalOperator', 'condition', 'value'); 
		// setCondition('logicalOperator', 'condition'); 

		return $condition;
	}
	
	public function build() {
		$result = [];
		
		foreach ($this->conditions as $array) {
			$logicalOperator = array_shift($array);
			$condition = array_shift($array);
			if ($array)
				$value = array_shift($array);
	
			if ($condition instanceof Conditions) {
				$condition = count($this->conditions) > 1 ? 
					'(' . $condition->build() . ')' : $condition->build();
			}
			elseif (isset($value)) {
				if (is_array($value)) {
					$placeholders = implode(',', array_fill(0, count($value), '?'));
					$condition = str_replace('?', $placeholders, $condition);
						
					$this->setParams($value); 
				}
				else
					$this->setParam($value);
			}
			
			$result[] = $result ? 
				strtoupper($logicalOperator) . ' ' . $condition : $condition;
		}
		
		return implode(' ', $result);
	}
	
	protected function isLogicalOperator($value) {
		return is_string($value) && in_array(strtoupper($value), $this->logicalOperators);
	}
	
	public function isEmpty() {
		return empty($this->conditions);
	}
}
