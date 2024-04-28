<?php

namespace ujb\database\statement;

class QueryInfo {

	public $query;
	public $executionTime;
	public $fromCache = false;

	public function __construct($query) {
		$this->query = $query;
	}
}
