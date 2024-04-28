<?php

namespace ujb\database\orm\entity;

trait TraitArrayDepth {

	protected $arrayDepth = [];
	
	abstract public function toArray();

	// albums.photos.comments
	// ['albums' => ['photos' => ['comments' => []]]];
	public function setArrayDepth($arrayDepth) {
		$this->arrayDepth = array_merge_recursive($this->arrayDepth, 
			$this->prepareArrayDepth($arrayDepth));
		
		return $this;
	}
	
	public function getArrayDepth($arrayDepth = null) {
		if (is_null($arrayDepth)) 
			return $this->arrayDepth;
		
		return is_array($arrayDepth) ? 
			$arrayDepth : $this->prepareArrayDepth($arrayDepth);
	}
	
	protected function prepareArrayDepth($arrayDepth) {
		$result = [];
		$current =& $result;
		foreach (explode('.', $arrayDepth) as $value) {
			$current[$value] = [];
			$current =& $current[$value];
		}
		
		return $result;
	}
	
	public function resetArrayDepth() {
		$this->arrayDepth = [];
		
		return $this;
	}
}
