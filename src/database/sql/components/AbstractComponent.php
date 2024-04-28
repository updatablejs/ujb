<?php

namespace ujb\database\sql\components;

abstract class AbstractComponent {

	protected $sql;
	
	public function __construct($sql) {
		$this->sql = $sql;
	}
	
	abstract public function build();
	
	abstract public function isEmpty();
	
	public function setParam($value) {
		return $this->sql->setParam($value);
	}
	
	public function setParams(array $values) {
		return $this->sql->setParams($values);
	}
	
	public function __toString() {
		return $this->build();
	}
}
