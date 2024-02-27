<?php

namespace core\database\adapters;

use core\database\result\source\AbstractSource;

abstract class StatementSource extends AbstractSource {

	public function cast(array $values) {
		foreach ($values as $key => &$value) {
			if (is_null($value)) continue;
		
			switch ($this->metadata[$key]['type']) {
			  case 'integer': 
				$value = (int) $value;
				break;
				
			  case 'float': 
				$value = (float) $value;
				break;
			}
		}
		
		return $values;
	}
}
