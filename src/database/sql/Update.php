<?php

namespace ujb\database\sql;

class Update extends Sql {

	protected function _build() {
		$query = 'UPDATE ';
		
		if (!$this->isEmpty('tables'))
			$query .= $this->get('tables') . ' SET ';
		
		if (!$this->isEmpty('set'))
			$query .= $this->get('set');
		
		if (!$this->isEmpty('where'))
			$query .= ' WHERE ' . $this->get('where');
	
		if (!$this->isEmpty('limit'))
			$query .= ' LIMIT ' . $this->get('limit');

		return $query;
	}
}
