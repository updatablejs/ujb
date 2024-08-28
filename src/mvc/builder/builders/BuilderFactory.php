<?php

namespace ujb\mvc\builder\builders;

use ujb\fileIterator\FileIteratorFactory, 
	ujb\fileIterator\FileIteratorInterface;

class BuilderFactory {
	
	static public function create(array $values) {
		$type = isset($values['type']) ? $values['type'] : 'js';
		
		if (!($values['source'] instanceof FileIteratorInterface)) {
			$regex = ['js' => '/\.js$/i',
				'css' => '/\.css$/i'];
			
			$values['source'] = FileIteratorFactory::create(
				array_merge($values, ['fileConstraint' => $regex[$type]])
			);
		}
				
		$class = __NAMESPACE__ . '\\' . ucfirst($type);

		return new $class($values);	
	}
}
