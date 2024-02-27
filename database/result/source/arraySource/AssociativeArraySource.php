<?php

namespace core\database\result\source\arraySource;

use core\database\result\Result,
	core\database\result\FetchStyle;

class AssociativeArraySource extends ArraySource {

	public function __construct(array $source) {
		$this->setSource($source);
	}
	
	public function fetchPair($fetchStyle) {
		switch ($fetchStyle) {
    		case FetchStyle::Associative:
				return $this->_fetchPair();
				
    		case FetchStyle::Numeric:
				if ($pair = $this->_fetchPair())
					$pair[1] = array_values($pair[1]);
				
				return $pair;
			
			case FetchStyle::Both:	
				if ($pair = $this->_fetchPair())
					$pair[1] = [array_values($pair[1]), $pair[1]];
				
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
		switch ($fetchStyle) {
    		case FetchStyle::Associative:
				$result = $this->source;
				
				break;
				
    		case FetchStyle::Numeric:
				foreach ($this->source as $key => $values)
					$result[$key] = array_values($values);
					
				break;
			
			case FetchStyle::Both:
				foreach ($this->source as $key => $values)
					$result[$key] = [array_values($values), $values];

				break;
			
    		case FetchStyle::Object:
				foreach ($this->source as $key => $values)
					$result[$key] = $this->getObjectFactory()
						->create([array_values($values), $values]);

				break;
			
			default:
				throw new \Exception('Unknown fetchStyle.');
		}
		
		$this->clear();
		
		return $result;
	}
}
