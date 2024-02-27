<?php

namespace core\database\orm\entity;

class JoinFieldSyncObservable {

	protected $entities = [];

	public function attach($entity, $internalFields, $externalFields) {
		$internalFields = (array) $internalFields;
		$externalFields = (array) $externalFields;

		while ($internalFields && $externalFields)
			$this->_attach($entity, array_shift($internalFields), array_shift($externalFields));
		
		return $this;
	}
	
	private function _attach($entity, $internalField, $externalField) {
		if (!isset($this->entities[$internalField]))
			$this->entities[$internalField] = new \SplObjectStorage();
		
		$storage = $this->entities[$internalField];
		
		$externalFields = (array) $externalField;
		if ($storage->contains($entity)) {
			$externalFields = array_unique(array_merge(
				$storage->offsetGet($entity), $externalFields));
		}
	
		$storage->attach($entity, $externalFields);
	}

	public function detach($entity, $internalFields, $externalFields) {
		$internalFields = (array) $internalFields; 
		$externalFields = (array) $externalFields;
		
		while ($internalFields && $externalFields)
			$this->_detach($entity, array_shift($internalFields), array_shift($externalFields));
		
		return $this;
	}

	private function _detach($entity, $internalField, $externalField) {
		if (!isset($this->entities[$internalField])) return;
		
		$storage = $this->entities[$internalField];
		
		if (!$storage->contains($entity)) return;
			
		$externalFields = array_diff($storage->offsetGet($entity), (array) $externalField);
		
		if ($externalFields)
			$storage->attach($entity, $externalFields);
		else
			$storage->detach($entity);
	}
	
	public function notify($field, $value) {
		if (isset($this->entities[$field])) {
			$storage = $this->entities[$field];
			
			foreach ($storage as $entity) {
				foreach ($storage->offsetGet($entity) as $field)
					$entity->setRaw($field, $value);
			}
		}
		
		return $this;
	}
}
