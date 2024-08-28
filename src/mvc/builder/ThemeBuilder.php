<?php

namespace ujb\mvc\builder;

use ujb\mvc\builder\builders\BuilderFactory,
	ujb\mvc\builder\builders\Builder,
	ujb\fileIterator\FileIteratorFactory;

class ThemeBuilder extends AbstractBuilder {

	protected $builders = [];
	protected $deletionConstraint;
	
	public function __construct(array $values) {
		$this->hydrate($values, ['source', 'to']);
	}

	public function setBuilders(array $builders) { 
		foreach ($builders as $key => $builder) {	
			if (!($builder instanceof Builder))
				$builder = BuilderFactory::create($builder);
			
			if (is_int($key))
				$this->builders[] = $builder;
			else
				$this->builders[$key] = $builder;
		}
	
		return $this;
	}
	
	public function getBuilder($key) { 
		return $this->builders[$key];
	}
	
	public function getBuilders() { 
		return $this->builders;
	}
	
	public function buildIfNeeded() { 
		 if (!file_exists($this->to))
		 	$this->build();
		
		return $this;
	}
	
	public function build() {
		if (is_dir($this->to) && $this->canDelete($this->to))
			FileIteratorFactory::create($this->to)->delete();

		$this->source->copy($this->to);	
		
		foreach ($this->builders as $builder) {
			$builder->build();
			
			foreach ($builder->getSource() as $file) {							
				if ($this->canDelete($file) && (string) $file != $builder->to) 
					unlink($file);
			}	
		}
		
		if ($this->canDelete($this->to))
			FileIteratorFactory::create($this->to)->deleteEmptyDirectories();
			
		return $this;
	}

	public function canDelete($path) { 
		return $this->deletionConstraint && (is_callable($this->deletionConstraint) ? 
			($this->deletionConstraint)($path) : strpos($path, $this->deletionConstraint) === 0);
	}
}
