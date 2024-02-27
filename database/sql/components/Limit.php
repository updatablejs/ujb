<?php

namespace core\database\sql\components;

class Limit extends AbstractComponent {
	
	public $limit;
	public $offset;
	
	public function set($limit, $offset = null) {	
		$this->limit = (int) $limit;
		
		if (!is_null($offset))
			$this->offset = (int) $offset;
	}
	
	public function build() {
		return !is_null($this->offset)  ? 
			$this->offset . ', ' . $this->limit : (string) $this->limit;
	}
	
	public function isEmpty() {
		return is_null($this->limit);
	}
}
