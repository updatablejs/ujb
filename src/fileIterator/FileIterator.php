<?php

namespace ujb\fileIterator;

use ujb\common\TraitHydrator, 
	ujb\common\Util;

class FileIterator extends AbstractFileIterator {
	
	use TraitHydrator;
	
	protected $source;
	protected $iterators = [];
	protected $key = 0;
	
	protected $relativePathResolver;
	
	// First time the directory is returned, then it is accessed. If it is true then the next() method 
	// will not move to the next value, and the searchCurrent() method will create a new iterator.
	protected $directoryAccessIntention;
	
	// If $this->listDirectoryFirstEnabled = false.
	protected $listDirectoryIntention;
	
	public function __construct($values) {
		if (!is_array($values) || Util::isNumericArray($values)) 
			$values = ['source' => $values];
				
		$this->hydrate($values, ['source']);
	}
	
	protected function searchCurrent() {
		while ($this->valid()) {	
			if ($this->isDir($this->current()) && $this->isDirectoryAccessAllowed($this->current())) {
				if ($this->isListDirectoryFirstEnabled()) {
					if ($this->directoryAccessIntention) {
						$this->directoryAccessIntention = false;
						$this->iterators[] = $this->getInternalUseFileIteratorFactory()->create($this->current());
						$this->getCurrentIterator()->rewind();
						
						return $this->searchCurrent();
					}
						
					$this->directoryAccessIntention = true;
				}
				else {
					if (!$this->listDirectoryIntention) {
						$this->iterators[] = $this->getInternalUseFileIteratorFactory()->create($this->current());
						$this->getCurrentIterator()->rewind();
						
						return $this->searchCurrent();
					}
					
					$this->listDirectoryIntention = false;
				}
			}

			if ($this->isFileAllowed($this->current()))
				return $this->current();
					
			$this->_next();
		}
	
		array_pop($this->iterators);
		
		if ($this->iterators) {
			if (!$this->isListDirectoryFirstEnabled()) 
				$this->listDirectoryIntention = true;
				
			$this->_next();
			$this->searchCurrent();
		}
	}
	
	protected function _next() {
		if (!$this->directoryAccessIntention && !$this->listDirectoryIntention) 
			$this->getCurrentIterator()->next();
	}
	
	protected function getCurrentIterator() {
		return $this->iterators ? $this->iterators[count($this->iterators) - 1] : null;
	}
	
	public function rewind() {
		$this->iterators = [$this->getInternalUseFileIteratorFactory()->create($this->source)];
		$this->directoryAccessIntention = false;
		$this->listDirectoryIntention = false;
		$this->key = 0;
		$this->searchCurrent();
	}

	public function next() {
		if ($this->iterators) {
			$this->_next();
			$this->key++;
			$this->searchCurrent();
		}
	}
	
    public function valid() {	
		return $this->iterators ? $this->getCurrentIterator()->valid() : false;
    }

	public function current() {	
		$current = $this->getCurrentIterator()->current();
		
		return $current instanceof \SplFileInfo ? 
			$current : new \SplFileInfo($current);
	}

	public function key() {
		return $this->key;
	}
	
	public function getRelativePath($file) {
		if ($this->relativePathResolver)
			return ($this->relativePathResolver)($file);
			
		return is_string($this->source) ?
			preg_replace('/^' . preg_quote($this->source, '/') . '(\/|$)/', '', $file) : (string) $file;
	}
	
	public function copy($to) { 
		$listDirectoriesEnabled = $this->listDirectoriesEnabled;
		$this->setListDirectoriesEnabled(true);
		foreach ($this as $file) {
			$directory = $this->copyDirectory(
				$file->isDir() ? $file->getPathname() : $file->getPath(), $to);
			
			if (!$file->isDir()) {
				if (!copy($file->getPathname(), $directory . '/' . $file->getFilename()))
					throw new \Exception('The file cannot be copied ' . $file->getPathname());
			}
		}
		
		$this->setListDirectoriesEnabled($listDirectoriesEnabled);
	}
	
	protected function copyDirectory($directory, $to) {
		$directory = rtrim($to . '/' . $this->getRelativePath($directory), '/');
	
		if (!is_dir($directory))
			mkdir($directory, 0777, true);
			
		return $directory;
	}
	
	public function copyDirectories($to) { 
		$listDirectoriesEnabled = $this->listDirectoriesEnabled;
		$this->setListDirectoriesEnabled(true);
		foreach ($this as $file) {
			if ($file->isDir())
				$this->copyDirectory($file->getPathname(), $to);
		}
		
		$this->setListDirectoriesEnabled($listDirectoriesEnabled);
	}
	
	public function deleteEmptyDirectories() {
		$listDirectoriesEnabled = $this->listDirectoriesEnabled;
		$listDirectoryFirstEnabled = $this->listDirectoryFirstEnabled;
		$this->setListDirectoriesEnabled(true)
			->setListDirectoryFirstEnabled(false);
		
		foreach ($this as $file) {
			if (Util::isDirectoryEmpty($file))
				rmdir($file->getPathname());
		}
		
		$this->setListDirectoriesEnabled($listDirectoriesEnabled)
			->setListDirectoryFirstEnabled($listDirectoryFirstEnabled);	
	}

	public function delete() { 	
		$listDirectoriesEnabled = $this->listDirectoriesEnabled;
		$listDirectoryFirstEnabled = $this->listDirectoryFirstEnabled;
		$this->setListDirectoriesEnabled(true)
			->setListDirectoryFirstEnabled(false);
		
		foreach ($this as $file) {
			if ($file->isDir()) {
				if (Util::isDirectoryEmpty($file))
					rmdir($file->getPathname());
			}
			else
				unlink($file->getPathname());	
		}
		
		if (is_string($this->source) && Util::isDirectoryEmpty($this->source))
			rmdir($this->source);
		
		$this->setListDirectoriesEnabled($listDirectoriesEnabled)
			->setListDirectoryFirstEnabled($listDirectoryFirstEnabled);	
	}
}
