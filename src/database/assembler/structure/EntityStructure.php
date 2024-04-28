<?php

namespace ujb\database\assembler\structure;

class EntityStructure {

	public $structure = [];

	public function __construct($structure = null) {
		if ($structure)
			$this->setStructure($structure);
	}

	public function setStructure($structure) {
		foreach ((array) $structure as $value) {
			$this->_setStructure($value);
		}
	}

	/**
 	 * @param string $structure 'movies.actors*(location)|movies_actors.photos' 
 	 */
	protected function _setStructure(string $structure) {
		$structure = explode('.', $structure); 
		
		$location = [array_shift($structure)];
	
		foreach ($structure as $value) {
			if (!preg_match($this->getRegex(), $value, $matches)) 
				throw new \Exception('Incorrect structure ' . $value);
			
			$location[] = !empty($matches['location']) ? $matches['location'] : $matches['table'];
			
			$this->structure[$matches['table']] = new EntityInfo($location, 
				!empty($matches['isCollection']) ? true : false,
				!empty($matches['junction']) ? $matches['junction'] : null
			);
			
			$location = [$matches['table']];
		}
	}

	protected function getRegex() {
		// table*(location)|junction
		return '/^'
			. '(?<table>[\w]+)'
			
			. '(?<isCollection>\*)?'
			
			. '(\('
				. '(?<location>[\w]+)'
			. '\))?'
			
			. '(\|'
				. '(?<junction>[\w]+)'
			. ')?'
			. '$/';
	}

	public function contains($key) {
		return isset($this->structure[$key]);
	}
	
	public function getEntityInfo($key) {
		return isset($this->structure[$key]) ? $this->structure[$key] : null;
	}
	
	public function getMainTable() {
		foreach ($this->structure as $entityInfo) {
			if (!$this->contains($entityInfo->location[0]))
				return $entityInfo->location[0];
		}
		
		throw new \Exception('Incorrect structure.');
	}
	
	/*
	[
		'name' => '',
		'actors' => [
			'name' => '',
			'junction' => [...]
			'photos' => [
				'file' => ''
			]
		]
	]
	*/
	public function assemble(array $entities) {			
		$processed = [];
		foreach ($entities as $key => &$entity) {	
			if (!$entityInfo = $this->getEntityInfo($key)) continue; 
			
			if ($entityInfo->hasJunction()) {
				$entity['junction'] = $entities[$entityInfo->junction];
				$processed[] = $entityInfo->junction;
			}
			
			if (!isset($entities[$entityInfo->location[0]]))
				throw new \Exception('Incorrect structure.');
				
			$entities[$entityInfo->location[0]][$key] =& $entity;
					
			$processed[] = $key;
		}
		
		$temp = array_diff(array_keys($entities), $processed);
		if (count($temp) != 1) 
			throw new \Exception('Incorrect structure.');
				
		return $entities[array_shift($temp)];
	}
}
