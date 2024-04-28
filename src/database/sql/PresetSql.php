<?php

namespace ujb\database\sql;

class PresetSql extends Sql {

	public $query;
	
	public function setQuery($query) {	
		$this->query = $query;
		
		return $this;
	}
	
	public function getParams() {
		$params = [];
		foreach ($this->params as $param) {
			if (is_array($param))
				$params = array_merge($params, $param);
			else
				$params[] = $param;
		}
		
		return $params;
	}
	
	public function _build() {
		$i = -1;
		return preg_replace_callback('/\?/', function() use (&$i) {
			$i++;
			
			return isset($this->params[$i]) && is_array($this->params[$i]) ?
				implode(',', array_fill(0, count($this->params[$i]), '?')) : '?';
	
		}, $this->query);
	}
}
