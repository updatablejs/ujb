<?php

namespace core\database\assembler;

/*
	$values = [
		'groupingSpecs' => string field,
		
		'groupingSpecs' => [string field, bool isColection],
			
		'groupingSpecs' => function($values) {
			return value;
		},
			
		'groupingSpecs' => [function($values) {
			return value;
		}, bool isColection]
		
		'groupingSpecs' => [
			table* => string field, // * isColection 
				
			table => string field,
				
			table => function($values) {
				return value;
			}
		],
		
		
		'interceptors' => function($values) {				
			return $values;
		},
		
		'interceptors' => [
			table => function($values) {				
				return $values;
			}
		]
	]
*/

class AssemblerFactory {
	
	static public function create(array $values = null) {
		if (isset($values['groupingSpecs'])) 
			$groupingSpecs = $values['groupingSpecs'];
		elseif (isset($values['groupingSpec'])) 
			$groupingSpecs = $values['groupingSpec'];
		else
			$groupingSpecs = null;
			
		if (isset($values['interceptors'])) 
			$interceptors = $values['interceptors'];
		elseif (isset($values['interceptor'])) 
			$interceptors = $values['interceptor'];
		else
			$interceptors = null;
		
		return isset($values['structure']) ?
			new AssemblerWithStructure($values['structure'], $groupingSpecs, $interceptors) : 
			new AssemblerWithoutStructure($groupingSpecs, $interceptors);
	}
}
