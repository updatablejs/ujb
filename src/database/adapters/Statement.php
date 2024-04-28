<?php

namespace ujb\database\adapters;

interface Statement {

	public function setParam($key, $value, int $dataType);
	
	public function setParams(array $params);

	public function execute();
}
	