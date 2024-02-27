<?php

namespace core\database\adapters\pdo;

use core\database\adapters\StatementSource,
	core\database\result\FetchStyle;

class Source extends StatementSource {

	protected $source;
	protected $position = -1;

	public function __construct(\PDOStatement $source, array $metadata) {
		$this->source = $source;
		$this->metadata = $metadata;
	}
	
	public function fetchPair($fetchStyle) {
		switch ($fetchStyle) {
    		case FetchStyle::Associative:
				$values = $this->source->fetch(
					$this->translateFetchStyle(FetchStyle::Numeric));

				return $values ? [++$this->position, array_combine($this->getFields(), $this->cast($values))] : null;
				
			case FetchStyle::Numeric:
				$values = $this->source->fetch(
					$this->translateFetchStyle(FetchStyle::Numeric));

				return $values ? [++$this->position, $this->cast($values)] : null;
				
			case FetchStyle::Both:	
				if ($pair = $this->fetchPair(FetchStyle::Numeric))
					$pair[1] = [$pair[1], array_combine($this->getFields(), $pair[1])];
				
				return $pair;
			
    		case FetchStyle::Object:
				if ($pair = $this->fetchPair(FetchStyle::Both))
					$pair[1] = $this->getObjectFactory()->create($pair[1]);
				
				return $pair;
					
			default:
				throw new \Exception('Unknown fetchStyle.');
		}
	}
	
	public function fetchAll($fetchStyle) {
		$result = [];
		while ($pair = $this->fetchPair($fetchStyle))
			$result[] = $pair[1];
		
		$this->clear();
		
		return $result;	
	}

	public function count() {
		return $this->source->rowCount() - ($this->position + 1);
	}

	public function clear() {
		$this->source->closeCursor();
		
		return $this;
	}
	
	protected function translateFetchStyle($fetchStyle) {
		$translations = [
			FetchStyle::Associative => \PDO::FETCH_ASSOC,
			FetchStyle::Numeric => \PDO::FETCH_NUM,
			FetchStyle::Both => \PDO::FETCH_BOTH,
			FetchStyle::Object => \PDO::FETCH_OBJ
		];
		
		return $translations[$fetchStyle];
	}
}
