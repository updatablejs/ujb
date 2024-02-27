<?php

namespace core\database\sql;

use core\database\sql\components\ComponentFactory;

class SqlWithPlaceholders extends Sql {

	public $query;

	public function setQuery($query, $translations = null) {
		$regex = '/'
			. '{(?:'
				. '([^}]+)'
				. '\|'
				. '([^}]+)'
			. ')}'
			. '/';
			
		$this->query = preg_replace_callback($regex, function($matches) {
			$this->translations[$matches[1]] = $matches[2];
			
			return '{' . $matches[1] . '}';
		}, $query);
		
		
		if ($translations) 
			$this->setTranslations($translations);
		
		return $this;
	}

	public function setTranslations($translations) {
		if (is_array($translations))
			$this->translations = array_merge($this->translations, $translations);
		else
			$this->_setTranslations($translations);
		
		return $this;
	}
	
	protected function _setTranslations(string $translations) {
		foreach (explode(';', $translations) as $translation) {
			$translation = explode('|', $translation);
			if (count($translation) != 2) 
				throw new \Exception('Parse error: ' . $translations);
				
			$this->translations[trim($translation[0])] = trim($translation[1]); 
		}
	}

	public function _build() {
		preg_match_all('/\{([\w]+)\}/', $this->query, $matches);
		$query = $this->query;
		foreach ($matches[1] as $match) {
			if ($component = $this->getComponent($match))
				$query = str_replace('{' . $match . '}', $component->build(), $query);
		}
		
		return $query;
	}
	
	public function __call($name, $args) {
		if ($component = $this->getComponent($name))
			$component->set(...$args);	
		else
			$this->components[$name] = ComponentFactory::create($this->translate($name), $args, $this);
	
		return $this;
	}
}
