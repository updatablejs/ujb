<?php

namespace core\database\assembler;

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
