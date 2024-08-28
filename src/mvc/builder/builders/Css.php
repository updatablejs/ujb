<?php

namespace ujb\mvc\builder\builders;

// Be careful with the background images.
class Css extends Builder {

	public function __construct(array $values) {
		$this->hydrate($values, ['source']);
	}
	
	public function build() { 
		$content = '';
		foreach ($this->source as $file) {
			$content .= '/** ' . $file->getPathname() . ' */' 
				. $this->getContent($file);
		}
		
		if ($this->to) $this->save($content);
		
		return $content;
	}
}
