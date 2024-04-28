<?php

namespace ujb\database\adapters;

interface Driver {

	public function open();

	public function close();

	public function prepare($sql, array $options = null);

	public function quote($value);
	
	public function getLastInsertId();

	public function beginTransaction();
	
	public function rollBack();
	
	public function commit();
}
	