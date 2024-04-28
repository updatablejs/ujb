<?php

namespace ujb\database\result\source\arraySource;

use ujb\database\result\Result,
	ujb\database\result\FetchStyle;
	
class NumericArraySource extends ArraySource {

	public function __construct(array $source, array $metadata) {
		$this->setSource($source);
		$this->metadata = $metadata;
	}
	
	public function fetchPair($fetchStyle) {
		switch ($fetchStyle) {
    		case FetchStyle::Associative:
				if ($pair = $this->_fetchPair())
					$pair[1] = array_combine($this->getFields(), $pair[1]);
				
				return $pair;
		
    		case FetchStyle::Numeric:
				return $this->_fetchPair();
				
			case FetchStyle::Both:	
				if ($pair = $this->_fetchPair())
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
		switch ($fetchStyle) {
    		case FetchStyle::Associative:
				foreach ($this->source as $key => $values)
					$result[$key] = array_combine($this->getFields(), $values);
				
				break;
				
    		case FetchStyle::Numeric:
        		$result = $this->source;
				
				break;
			
			case FetchStyle::Both:
				foreach ($this->source as $key => $values)
					$result[$key] = [$values, array_combine($this->getFields(), $values)];
				
				break;
			
    		case FetchStyle::Object:
				foreach ($this->source as $key => $values)
					$result[$key] = $this->getObjectFactory()
						->create([$values, array_combine($this->getFields(), $values)]);
						
				break;
			
			default:
				throw new \Exception('Unknown fetchStyle.');
		}
		
		$this->clear();
		
		return $result;
	}
}
