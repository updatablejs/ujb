<?php

namespace ujb\database\sql;

class Delete extends Sql {
	
	protected function _build() {
		$query = 'DELETE ';
		
		if (!$this->isEmpty('tables'))
			$query .= 'FROM ' . $this->get('tables');
		
		if (!$this->isEmpty('where'))
			$query .= ' WHERE ' . $this->get('where');
		
		if (!$this->isEmpty('limit'))
			$query .= ' LIMIT ' . $this->get('limit');
		
		return $query;
	}
}
