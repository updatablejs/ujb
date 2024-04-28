<?php

namespace ujb\database\result\source;

use ujb\database\result\ObjectFactory;

abstract class AbstractSource implements Source {
	
	protected $metadata = [];
	protected $fields = [];
	
	protected $objectFactory;
	
	public function setObjectFactory(ObjectFactory $objectFactory) {
		$this->objectFactory = $objectFactory;
		
		return $this;
	}
	
	public function getObjectFactory() {
		return $this->objectFactory;
	}
	
	public function getFields() {
		if (!$this->fields) {
			foreach ($this->metadata as $metadata)
				$this->fields[] = $metadata['name'];
		}

		return $this->fields;
	}

	public function setMetadata(array $metadata) {
		$this->metadata = $metadata;
		
		return $this;
	}
	
	public function getMetadata() {
		return $this->metadata;
	}
	
	public function fetch($fetchStyle) {
		$pair = $this->fetchPair($fetchStyle);
				
		return $pair ? $pair[1] : null;		
	}
}
