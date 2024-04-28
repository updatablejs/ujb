<?php

namespace ujb\database\assembler;

use ujb\database\result\Result, 
	ujb\database\result\FetchStyle,
	ujb\database\result\source\arraySource\AssociativeArraySource,
	ujb\database\assembler\structure\EntityStructure;

class AssemblerWithStructure extends Assembler {

	public $structure;
	public $groups = [];
	public $interceptors = [];
	
	public function __construct($structure, $groups = null, $interceptors = null) {
		$this->structure = ($structure instanceof EntityStructure) ? 
			$structure : new EntityStructure($structure);
		
		if ($groups) {
			if (!is_array($groups)) 
				$groups = ['main*' => $groups];
			
			$this->setGroups($groups);
		}
		
		if ($interceptors) {
			if (!is_array($interceptors))
				$interceptors = ['main' => $interceptors];
			
			$this->setInterceptors($interceptors);
		}
	}
	
	public function getStructure() {
		return $this->structure;
	}
	
	public function setGroups(array $groups) {
		foreach ($groups as $key => $group) {
			$isCollection = false;
			if (str_ends_with($key, '*')) {
				$key = rtrim($key, '*');
				$isCollection = true;
			}
			
			$this->groups[$key] = [$group, $isCollection];					
		}
	
		return $this;
	}
	
	public function getGroup($key) {
		return $this->groups[$key] ?? null;
	}
	
	public function setInterceptors(array $interceptors) {
		$this->interceptors = $interceptors;
		
		return $this;
	}
	
	public function getInterceptor($key) {
		return  $this->interceptors[$key] ?? null;
	}
	
	
	// Assemble
	
	public function assemble(Result $result) {
		$metadata = $result->getMetadata();
		$entities = [];
		while ($row = $result->fetch(FetchStyle::Numeric)) {	
			$entity = $this->addIdentifiers(
				$this->structure->assemble($this->rowToEntities($row, $metadata)));
				
			if ($entity) $this->mergeEntity($entities, $entity);
		}
		
		$mainTable = $this->structure->getMainTable();
		
		return new Result(new AssociativeArraySource(
		 	$this->removeIdentifiers($entities, 
				$this->getGroup($mainTable) ?? $this->getGroup('main'), 
				$this->getInterceptor($mainTable) ?? $this->getInterceptor('main'))
		));
	}
	
	protected function rowToEntities($row, $metadata) {	
		$result = [];
		foreach ($row as $key => $value) {
			$table = $metadata[$key]['table'];
			$column = $metadata[$key]['name'];
		
			$result[$table][$column] = $value;
		}
		
		return $result;
	}

	/*
	[
		'name' => '',
		'actors' => [
			'name' => '',
			'junction' => [...]
			'photos' => [
				'src' => ''
			]
		]
	]
	['identifier' => [...]]
	*/
	protected function addIdentifiers($entity) { 		
		$values = [];
		foreach ($entity as $key => $value) {	
			if (is_array($value)) {
				if ($key != 'junction')
					$entity[$key] = $this->addIdentifiers($value);
				
				continue;
			}
			
			if (!is_null($value))
				 $values[$key] = $value;
		}
	
		return $values ? [md5(json_encode($values)) => $entity] : [];
	}
	
	protected function mergeEntity(&$entities, $entity) {
		$key = key($entity);
		$entity = current($entity);
		
		if (isset($entities[$key])) {
			foreach ($entity as $k => $v) {
				if (is_array($v) && !empty($v) && $k != 'junction') 
					$this->mergeEntity($entities[$key][$k], $v);
			}
		}
		else
			$entities[$key] = $entity;
	}
	
	protected function removeIdentifiers($entities, $group, $interceptor) {
		$result = [];
		foreach ($entities as $entity) {
			foreach ($entity as $key => $value) {
				if (is_array($value) && $key != 'junction') {
					if (!$entityInfo = $this->structure->getEntityInfo($key))
						throw new \Exception('Incorrect structure.');
					
					unset($entity[$key]);
					
					$location = $entityInfo->location[1];
					
					$entity[$location] = $this->removeIdentifiers(
						$value, $this->getGroup($key), $this->getInterceptor($key));
						
					if (!$entityInfo->isCollection())
						$entity[$location] = array_shift($entity[$location]);
				}
			}
			
			if ($group) {	
				$key = is_string($group[0]) ? $entity[$group[0]] : $group[0]($entity);
				
				if ($interceptor)
					$entity = $interceptor($entity);
				
				if ($group[1])
					$result[$key][] = $entity;
				else
					$result[$key] = $entity;
			}
			else
				$result[] = $interceptor ? $interceptor($entity) : $entity;
		}
		
		return $result;
	}
}
