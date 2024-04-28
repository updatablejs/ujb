<?php

namespace ujb\database\orm\schema\joins;

use ujb\database\orm\schema\Schema;

class Reference extends AbstractJoin { 

	protected $refer;
	
	protected function hydrate(array $values) {
		if (!isset($values['table']) || !isset($values['refer'])) 
			throw new \Exception('Table or refer is missing from $values.');
		
		$this->tableName = $values['table'];
		$this->refer = $values['refer'];
	}
	
	public function getOppositeJoin() {
		return $this->getExternalTable()
			->getJoin($this->refer);
	}
	
	public function getType() {
		$translations = [
			'oto' => 'oto', 
			'otm' => 'mto',
			'mto' => 'otm', 
			'mtm' => 'mtm'];
		
		return $translations[$this->getOppositeJoin()->getType()];
	}
	
	public function isParent() {
		return !$this->getOppositeJoin()->isParent();
	}
	
	public function getInternalFields() {
		return $this->getOppositeJoin()->getExternalFields();
	}
	
	public function getExternalFields() {
		return $this->getOppositeJoin()->getInternalFields();
	}
	
	public function getJunction() {		
		if (!$this->junction) {
			$junction = $this->getOppositeJoin()->getJunction();
			
			$values = ['table' => $junction->tableName];
			
			if ($junction->hasInternalFields()) 			
				$values['externalFields'] = $junction->getInternalFields();
				
			if ($junction->hasExternalFields()) 			
				$values['internalFields'] = $junction->getExternalFields();
			
			$this->junction = (new Junction($values))->setParent($this);
		} 
		
		return $this->junction;
	}
	
	public function hasJunction() {
        return $this->getOppositeJoin()->hasJunction();
	}
}
