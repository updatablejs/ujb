<?php

namespace ujb\common;

class Locale {

	protected $translations = [];
	
	public function __construct(array $translations = null) {		
		if ($translations)
			$this->setTranslations($translations);
	}
	
	public function setTranslations(array $translations) {
		$this->translations = array_merge($this->translations, $translations);
	
		return $this;
	}

	public function translate($value, ...$values) {
		return isset($this->translations[$value]) ?
			sprintf($this->translations[$value], ...$values) : sprintf($value, ...$values);
	}
	
	public function hasTranslation($value) {
   		return isset($this->translations[$value]);
	}
}
