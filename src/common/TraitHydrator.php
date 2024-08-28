<?php

namespace ujb\common;

trait TraitHydrator {

	public function hydrate(array $values, array $required = []) {
		if (array_diff($required, array_keys($values)))
			throw new \Exception('One or more of the required values are missing.');
		
		foreach ($values as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method))
				$this->$method($value);
			else if (property_exists($this, $key))
				$this->$key = $value;
		}
	}
}
