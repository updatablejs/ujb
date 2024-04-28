<?php

namespace ujb\database\assembler;

/*
	$values = [
		The entities from table 2 will be added to the entity from table 1 in the field named "table2". If it is desired 
		for these entities (from table 2) to be added into a different field, then a new location can be added as follows: 'table1.table2*(location)'.
		
		The * character indicates that there are multiple entities from table 2. These entities will be added to the entity from table 1 
		in an array with entities. Without the * character, only a single entity from table 2 will be added 
		to the entity from table 1 and not an array with entities.
		
		If a junction is used in the union between table 1 and table 2, then the junction table name 
		will be added as follows: 'table1.table2*(location)|junction'

		Multiple structures can be added: ['user.salaries(salary)', 'user.photos(avatar)']

		'structure' => 'table1.table2',
		'structure' => 'table1.table2*',
		'structure' => 'table1.table2*|junction',
		'structure' => 'table1.table2*(location)|junction',
		'structure' => 'table1.table2*(location)|junction.table3',
		'structure' => ['table1.table2*(location)|junction', ...]
		
		
		'groups' => string field,
		
		'groups' => [string field, bool isColection],
			
		'groups' => function($values) {
			return value;
		},
			
		'groups' => [function($values) {
			return value;
		}, bool isColection]
		
		'groups' => [
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
	];
*/

class AssemblerFactory {
	
	static public function create(array $values = null) {
		if (isset($values['groups'])) 
			$groups = $values['groups'];
		elseif (isset($values['group'])) 
			$groups = $values['group'];
		else
			$groups = null;
			
		if (isset($values['interceptors'])) 
			$interceptors = $values['interceptors'];
		elseif (isset($values['interceptor'])) 
			$interceptors = $values['interceptor'];
		else
			$interceptors = null;
		
		return isset($values['structure']) ?
			new AssemblerWithStructure($values['structure'], $groups, $interceptors) : 
			new AssemblerWithoutStructure($groups, $interceptors);
	}
}
	