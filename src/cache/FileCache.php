<?php

namespace ujb\cache;

use ujb\cache\contentPreparator\ContentPreparator,
	ujb\cache\contentPreparator\DefaultContentPreparator,
	ujb\cache\keyPreparator\KeyPreparator,
	ujb\cache\keyPreparator\DefaultKeyPreparator;

class FileCache implements Cache {

	public $cacheDir;
	public $validity;
	public $keyPreparator;
	public $contentPreparator;

    public function __construct($cacheDir, $validity = null, 
		KeyPreparator $keyPreparator = null, ContentPreparator $contentPreparator = null) {
		
        $this->cacheDir = $cacheDir;
		$this->validity = $validity;
		
		$this->setKeyPreparator($keyPreparator ? $keyPreparator : new DefaultKeyPreparator());
		
		$this->setContentPreparator($contentPreparator ? $contentPreparator : new DefaultContentPreparator());
    }

	public function setKeyPreparator(KeyPreparator $keyPreparator) { 
		$this->keyPreparator = $keyPreparator;
		
		return $this;
	}
	
	public function getKeyPreparator() { 
		return $this->keyPreparator;
	}
	
	public function setContentPreparator(ContentPreparator $contentPreparator) { 
		$this->contentPreparator = $contentPreparator;
		
		return $this;
	}
	
	public function getContentPreparator() { 
		return $this->contentPreparator;
	}

	public function set($key, $content) { 
		file_put_contents($this->getFilePath($key), $this->contentPreparator->encode($content));
		
		return $this;
	}

    public function get($key) {
        return $this->contains($key) ? 
			$this->contentPreparator->decode(file_get_contents($this->getFilePath($key))) : null;
    }
	
    public function needUpdate($key, $validity = null) {
        if (is_null($validity))
			$validity = $this->validity;
		
		$filePath = $this->getFilePath($key);
		
		return !file_exists($filePath) || filemtime($filePath) < time() - $validity;
    }

	public function contains($key) {
		return file_exists($this->getFilePath($key));
	}
	
	public function remove($key) {
		$filePath = $this->getFilePath($key);
		if (file_exists($filePath)) unlink($filePath);
        
		return $this;
	}
	
    public function removeAll() {
		foreach (new \DirectoryIterator($this->cacheDir) as $file) {
    		if ($file->isFile())
        		 unlink($file->getPathName());
		}
		
		return $this;
    }
		
    public function getLastModified($key) {
        $filePath = $this->getFilePath($key);
		
		return file_exists($filePath) ? 
			filemtime($filePath) : null;
    }
		
	public function getFilePath($key) {
		return $this->cacheDir . '/' . $this->keyPreparator->prepare($key);
	}
}
