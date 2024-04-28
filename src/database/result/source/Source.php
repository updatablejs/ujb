<?php

namespace ujb\database\result\source;

interface Source extends \Countable {

	public function fetch($fetchStyle);
	
	public function fetchAll($fetchStyle);
	
	public function fetchPair($fetchStyle);
	
	public function clear();
	
	public function getMetadata();
}
