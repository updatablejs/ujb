<?php

namespace ujb\fileIterator;

use ujb\common\Arrayable;

abstract class AbstractFileIterator implements FileIteratorInterface, Arrayable {
	
	protected $internalUseFileIteratorFactory;	
	protected $fileConstraint;
	protected $directoryAccessConstraint;
	protected $listDirectoryFirstEnabled = true;
	protected $listDirectoriesEnabled = false;
	protected $skipDotsEnabled = true; 

	public function setInternalUseFileIteratorFactory(InternalUseFileIteratorFactory $internalUseFileIteratorFactory) { 
		$this->internalUseFileIteratorFactory = $internalUseFileIteratorFactory;
		
		return $this;
	}
	
	public function getInternalUseFileIteratorFactory() { 
		if (!$this->internalUseFileIteratorFactory)
			$this->internalUseFileIteratorFactory = FileIteratorFactory::getInternalUseFileIteratorFactory();
		
		return $this->internalUseFileIteratorFactory;
	}
	
	public function setFileConstraint($fileConstraint) { 
		$this->fileConstraint = $fileConstraint;
		
		return $this;
	}
	
	public function setDirectoryAccessConstraint($directoryAccessConstraint) { 
		$this->directoryAccessConstraint = $directoryAccessConstraint;
		
		return $this;
	}
	
	public function isFileAllowed($file) { 
		if (!$this->listDirectoriesEnabled && $this->isDir($file)) return false;
		
		if ($this->skipDotsEnabled && $this->isDot($file)) return false;
		
		return !$this->fileConstraint || 
			(is_callable($this->fileConstraint) ? 
				($this->fileConstraint)($file) : preg_match($this->fileConstraint, $file));
	}
	
	public function isDirectoryAccessAllowed($directory) { 
		if ($this->isDot($directory)) return false;
		
		return !$this->directoryAccessConstraint || 
			(is_callable($this->directoryAccessConstraint) ? 
				($this->directoryAccessConstraint)($directory) : preg_match($this->directoryAccessConstraint, $directory));
	}
	
	public function isDot($file) { 
		return in_array((string) $file, ['.', '..']);
	}
	
	public function isDir($file) { 
		return $file instanceof \SplFileInfo ? $file->isDir() : is_dir($file);
	}
	
    public function setListDirectoryFirstEnabled($listDirectoryFirstEnabled) {
        $this->listDirectoryFirstEnabled = $listDirectoryFirstEnabled;
		
		return $this;
    }

    public function isListDirectoryFirstEnabled() {
        return $this->listDirectoryFirstEnabled;
    }
	
    public function setListDirectoriesEnabled($listDirectoriesEnabled) {
        $this->listDirectoriesEnabled = $listDirectoriesEnabled;
		
		return $this;
    }

    public function isListDirectoriesEnabled() {
        return $this->listDirectoriesEnabled;
    }

    public function setSkipDotsEnabled($skipDotsEnabled) {
        $this->skipDotsEnabled = $skipDotsEnabled;
		
		return $this;
    }

    public function isSkipDotsEnabled() {
        return $this->skipDotsEnabled;
    }
	
	public function toArray() {
		$files = [];
		foreach ($this as $file) {
			$files[] = $file->getPathname();
		}
		
		return $files;
	}	
}
