<?php

namespace core\database\sql;

class Select extends Sql {

	protected function _build() {
		$query = 'SELECT ';
		
		$query .= !$this->isEmpty('fields') ? $this->get('fields') : '*';
		
		if (!$this->isEmpty('tables'))
			$query .= ' FROM ' . $this->get('tables');
		
		if (!$this->isEmpty('joins'))
			$query .= ' ' . $this->get('joins');
		
		if (!$this->isEmpty('where'))
			$query .= ' WHERE ' . $this->get('where');
		
		if (!$this->isEmpty('order'))
			$query .= ' ORDER BY ' . $this->get('order');
			
		if (!$this->isEmpty('limit'))
			$query .= ' LIMIT ' . $this->get('limit');

		return $query;
	}
}
