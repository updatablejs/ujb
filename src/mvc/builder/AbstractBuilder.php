<?php

namespace ujb\mvc\builder;

use ujb\fileIterator\FileIteratorInterface,
	ujb\common\TraitHydrator;

abstract class AbstractBuilder {

	use TraitHydrator;

	/**
	 * FileIteratorInterface
	 */
	public $source;
	public $to;

	abstract public function build();
		
	public function getSource() {
		return $this->source;
	}
	
	public function toArray() {
		return $this->source->toArray();
	}
}
