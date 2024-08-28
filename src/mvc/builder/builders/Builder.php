<?php

namespace ujb\mvc\builder\builders;

use ujb\mvc\builder\AbstractBuilder,
	ujb\mvc\builder\builders\handlers\AbstractHandler;

abstract class Builder extends AbstractBuilder {

	protected $handlers = [];
	protected $defaultHandler;

	public function setHandlers($handlers) { 
		foreach ($handlers as $file => $handler) {
			$this->handlers[$file] = $handler;
		}

		return $this;
	}
	
	public function setDefaultHandler($defaultHandler) { 
		$this->defaultHandler = $defaultHandler;
		
		return $this;
	}

	protected function getContent($file) {
		$file = $file instanceof \SplFileInfo ? $file->getPathname() : (string) $file;
		
		if (isset($this->handlers[$file])) {
			if (is_array($this->handlers[$file])) {
				// [$handler, $args] = $this->handlers[$file] + [1 => []];
				
				$h = $this->handlers[$file];			
				$handler = array_shift($h);
				if ($h)
					$args = array_shift($h);
			}
			else 
				$handler = $this->handlers[$file];
		}
		else if ($this->defaultHandler)
			$handler = $this->defaultHandler;
		
		$content = file_get_contents($file);
		if (isset($handler)) {
			if (!isset($args)) $args = [];
	
			if (!is_array($args)) $args = [$args]; 
			
			$args[] = $file;
			$args[] = $this;

			$content = $handler instanceof AbstractHandler ?
				$handler->handle($content, ...$args) : $handler($content, ...$args);
		}	
		
		return $content;	
	}
	
	protected function save($content) {
		$directory = pathinfo($this->to, PATHINFO_DIRNAME);
		
		if (!is_dir($directory))
			mkdir($directory, 0777, true);
			
		file_put_contents($this->to, $content, FILE_APPEND);
	}
}
