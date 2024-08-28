<?php

namespace ujb\fileIterator;

use ujb\common\TraitHydrator, 
	ujb\common\ArrayUtil;

class OrderedFileIterator implements FileIteratorInterface {

	use TraitHydrator;

	protected $source;
	protected $files = [];
	protected $order = [];
	
	public function __construct($values) {
		if ($values instanceof FileIteratorInterface)
			$values = ['source' => $values];
	
		$this->hydrate($values, ['source']);
	}
	
	public function getSource() { 
		return $this->source;
	}
	
	public function setOrder($order) { 
		$this->order = (array) $order;
		
		return $this;
	}
	
	public function toArray() {
		if (!$this->files) {
			$this->files = $this->source->toArray();
	
			if ($this->order) 
				$this->sortFiles();
		}

		return $this->files;	
	}
	
	protected function sortFiles() { 					
		if (isset($this->order['up']))
			$this->files = ArrayUtil::moveValuesUp($this->files, $this->order['up']);
		
		if (isset($this->order['down']))
			$this->files = ArrayUtil::moveValuesDown($this->files, $this->order['down']);
			
		if (isset($this->order['custom']))
			$this->files = ($this->order['custom'])($this->files);
		
		$this->files = array_values($this->files);	
	}
	
	public function rewind() {
		$this->toArray();
		reset($this->files);
	}

	public function next() {
		next($this->files);
	}
	
    public function valid() {	
		return key($this->files) !== null;
    }

	public function current() {	
		$current = current($this->files);
		
		return $current instanceof \SplFileInfo ? 
			$current : new \SplFileInfo($current);
	}

	public function key() {
		return key($this->files);
	}

	public function getRelativePath($file) {
		return $this->source->getRelativePath($file);
	}
	
	public function copy($to) {
		return $this->source->copy($to);
	}
	
	public function copyDirectories($to) {
		return $this->source->copyDirectories($to);
	}
	
	public function deleteEmptyDirectories() {
		return $this->source->deleteEmptyDirectories();
	}
	
	public function delete() {
		return $this->source->delete();
	}
}
