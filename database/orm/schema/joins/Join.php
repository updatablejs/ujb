<?php

namespace core\database\orm\schema\joins;

use core\database\orm\schema\Schema;

class Join extends AbstractJoin {

	protected $type;
	protected $internalFields;
	protected $externalFields;
	protected $isParent;
	
	protected function hydrate(array $values) {
		if (!isset($values['table']) || !isset($values['type'])) 
			throw new \Exception('Type or table is missing from $values.');
		
		if ($values['type'] == 'mtm' && !isset($values['junction'])) 
			throw new \Exception('Join type mtm need junction.');
		
		$this->tableName = $values['table'];
		$this->type = $values['type'];
		
		// Tabelul care contine uniunea este parent in relatia cu tabelul din uniune.
		if ($values['type'] == 'oto' && isset($values['parent'])) 
			$this->isParent = (bool) $values['parent'];
		
		if (isset($values['internalFields']) && isset($values['externalFields'])) {
			$this->internalFields = $values['internalFields'];
			$this->externalFields = $values['externalFields'];
		}
		elseif (isset($values['using'])) {
			$this->internalFields = $this->externalFields = $values['using'];
		}
		elseif (isset($values['on'])) {
			$this->internalFields = $values['on']['internal'];
			$this->externalFields = $values['on']['external'];
		}
		
		if (isset($values['junction']))
			$this->junction = (new Junction($values['junction']))->setParent($this);
	}
	
	public function setType(string $type) {
		$type = strtolower($type);
		
		$types = ['oto', 'otm', 'mto', 'mtm'];
		if (!in_array($type, $types)) 
			throw new \Exception('Incorrect join type.');
		
		$this->type = $type;
		
		return $this;
	}

	public function getType() {		
		return $this->type;
	}
	
	public function getInternalFields() {
		return $this->internalFields ? 
			$this->internalFields : $this->parent->primaryKey;
	}

	public function getExternalFields() {
		if ($this->externalFields) return $this->externalFields;
			
		return $this->hasJunction() ? 
			$this->getExternalTable()->primaryKey : $this->parent->primaryKey;	
	}
	
	public function hasInternalFields() {
		return !empty($this->internalFields);
	}
	
	public function hasExternalFields() {
		return !empty($this->externalFields);
	}
	
	public function getOppositeJoin() {
		foreach ($this->getExternalTable()->getJoins() as $join) {
			if ($join instanceof Reference && $join->getOppositeJoin() === $this)
				return $join;
		}
		
		return null;
	}
	
 	public function isParent() {
		switch ($this->type) {
			case 'otm':
				return true;
			case 'mto':
			case 'mtm':
				return false;
			case 'oto':
				return !is_null($this->isParent) ? 
					$this->isParent : true;
		}
	}
 
	public function getJunction() {
		if (!$this->junction) 
			throw new \Exception('This join does not use junction.');
		
		return $this->junction;
	}
	
	public function hasJunction() {
        return !empty($this->junction);
	}
}
