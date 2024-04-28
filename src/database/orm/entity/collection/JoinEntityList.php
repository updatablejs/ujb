<?php

namespace ujb\database\orm\entity\collection;

use ujb\common\Util;

class JoinEntityList extends AbstractEntityList 
	implements \IteratorAggregate, \JsonSerializable {

	protected $entity;
	protected $joinSchema;
	
	public function __construct($entity, $joinSchema) {
		$this->entity = $entity;
		$this->joinSchema = $joinSchema;
		
		parent::__construct();
	}

	public function getIterator() {
		return $this->storage;
	}

	public function getOrm() {
		return $this->entity->getOrm();
	}
	
	public function toArray($arrayDepth = null) {
		$result = [];
		foreach ($this as $entity) {
			$result[] = $entity->toArray($arrayDepth);
		}
		
		return $result;
	}
	
	public function jsonSerialize() {
		return $this->toArray();
	}

	public function attach($entity) {	
		if (is_array($entity)) {
			$entities = Util::isNumericArray($entity) ? 
				$entity : [$entity];
		}
		else 
			$entities = [$entity];
		
		if (!$this->joinSchema->isCollection())
			$entities = array_slice($entities, 0, 1);
		
		foreach ($entities as $entity) {		
			$this->_attach($entity);
		}
		
		return $this;
	}

	protected function _attach($entity) {				
		$joinSchema = $this->joinSchema;
		
		$oppositeJoinSchema = $joinSchema->getOppositeJoin(); 
		
		if (is_array($entity)) {
			if (Util::isNumericArray($entity)) // Poate fi trimisa si jonctiunea folosita.
				list($entity, $junction) = $entity;
			
			if (is_array($entity))
				$entity = $this->getOrm()->getEntity($joinSchema->tableName, $entity);
		}
		
		if ($this->has($entity)) return $this;
		
		if ($joinSchema->tableName != $entity->getTableSchema()->getName()) throw new \Exception(
			'Entity must belong to ' . $joinSchema->tableName . ' table.');
		
		
		// Detach
		
		if ($joinSchema->type != 'mtm') {
			if ($joinSchema->type == 'oto') { // ex. $user->attach($salary)
				$this->detach();
				if ($oppositeJoinSchema)
					$entity->detach($oppositeJoinSchema->name);
			}
			elseif (!$joinSchema->isParent()) // ex. $photo->attach($album)
				$this->detach();
			elseif ($oppositeJoinSchema) // ex. $album->attach($photo)
				$entity->detach($oppositeJoinSchema->name);
		}
		
		
		// Attach
		
		if (!$joinSchema->hasJunction()) {	
			if ($joinSchema->isParent()) { // ex. $album->attach($photo)				
				$entity->setRaw($this->replaceKeys($this->entity->getRaw($joinSchema->internalFields), 
					$joinSchema->internalFields, $joinSchema->externalFields));
				
				$this->entity->joinFieldSyncObservable->attach(
					$entity, $joinSchema->internalFields, $joinSchema->externalFields);
			}
			else { // ex. $poza->attach($album)
				$this->entity->setRaw($this->replaceKeys($entity->getRaw($joinSchema->externalFields), 
					$joinSchema->externalFields, $joinSchema->internalFields));
	
				$entity->joinFieldSyncObservable->attach(
					$this->entity, $joinSchema->externalFields, $joinSchema->internalFields);
			}
			
			$this->set($entity);
			if ($oppositeJoinSchema)
				$entity->getJoinEntityList($oppositeJoinSchema->name)->set($this->entity);
		}
		else {
			$junctionSchema = $joinSchema->getJunction();
			
			$values = array_merge(
				$this->replaceKeys($this->entity->getRaw($joinSchema->internalFields), 
					$joinSchema->internalFields, $junctionSchema->internalFields),
				
				$this->replaceKeys($entity->getRaw($joinSchema->externalFields),
					$joinSchema->externalFields, $junctionSchema->externalFields)
			);
			
			// Junction

			if (isset($junction)) {
				if (is_array($junction)) {
					$junction = $this->getOrm()->getEntity($junctionSchema->getTable(), $junction)
						->setRaw($values);
				}
				else {
					if ($junctionSchema->getTableName() != $junction->getTableSchema()->getName()) throw new \Exception(
						'Junction must belong to ' . $junctionSchema->tableName . ' table.');
					
					$junction->setRaw($values);
				}
			}
			else {
				$junction = $this->getOrm()->getEntity($junctionSchema->getTable())
					->setRaw($values);
			}
			
			
			// Attach
			
			$this->entity->joinFieldSyncObservable->attach(
				$junction, $joinSchema->internalFields, $junctionSchema->internalFields);
			
			$entity->joinFieldSyncObservable->attach(
				$junction, $joinSchema->externalFields, $junctionSchema->externalFields);
			
			$this->set($entity, $junction);
			if ($oppositeJoinSchema)
				$entity->getJoinEntityList($oppositeJoinSchema->name)->set($this->entity, $junction);
		}
		
		return $this;
	}
	
	public function detach($entity = null) {
		if ($entity)
			$entities = is_array($entity) ? $entity : [$entity];
		else
			$entities = $this->storage;
	
		foreach ($entities as $entity) {
			$this->_detach($entity);
		}
		
		return $this;
	}
	
	protected function _detach($entity) {
		if (!$this->has($entity)) return;
		
		$joinSchema = $this->joinSchema;

		if (!$joinSchema->hasJunction()) {
			if ($joinSchema->isParent()) { // ex. $album->detach($photo) (otm)
				$this->entity->joinFieldSyncObservable->detach(
					$entity, $joinSchema->internalFields, $joinSchema->externalFields);
				
				$entity->setRaw(array_fill_keys(
					(array) $joinSchema->externalFields, null));
			}
			else { // ex. $photo->detach($album) (mto)
				$entity->joinFieldSyncObservable->detach(
					$this->entity, $joinSchema->externalFields, $joinSchema->internalFields);
	
				$this->entity->setRaw(array_fill_keys(
					(array) $joinSchema->internalFields, null));
			}
		}
		else {
			$junction = $this->storage->offsetGet($entity);
			$junctionSchema = $joinSchema->getJunction();
			$handler = function($junction) {
				$junction->delete();
			};
			
			$this->entity->setEvent('afterSave', $handler, $junction, true);
			$entity->setEvent('afterSave', $handler, $junction, true);
			
			$this->entity->joinFieldSyncObservable->detach(
				$junction, $joinSchema->internalFields, $junctionSchema->internalFields);
		
			$entity->joinFieldSyncObservable->detach(
				$junction, $joinSchema->externalFields, $junctionSchema->externalFields);		
		}

		$this->remove($entity);
		$oppositeJoinSchema = $joinSchema->getOppositeJoin();
		if ($oppositeJoinSchema && $entity->has($oppositeJoinSchema->name)) {
			$entity->getJoinEntityList($oppositeJoinSchema->name)->remove($this->entity);
		}
		
		return $this;
	}
	
	protected function replaceKeys(array $array, $from, $to) {
		$keys = array_combine((array) $from, (array) $to);
		
		$result = [];
		foreach ($array as $key => $value)
			$result[$keys[$key]] = $value;
		
		return $result;
	}
}
