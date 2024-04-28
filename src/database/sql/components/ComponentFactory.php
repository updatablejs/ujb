<?php

namespace ujb\database\sql\components;

use ujb\common\Util;

class ComponentFactory {

	static public $translations = [
		'set' => 'values\Set',
		'replacementValues' => 'values\ReplacementValues'];

	static public function create($type, $values, $sql = null) {
		if ($type == 'values') {
			$class = Util::isNumericArray($values[0]) ? 
				__NAMESPACE__ . '\values\MultiRowsValues' : __NAMESPACE__ . '\values\Values';		
		}
		elseif (isset(self::$translations[$type]))
			$class = __NAMESPACE__ . '\\' . self::$translations[$type];
		else
			$class = __NAMESPACE__ . '\\' . ucfirst($type);
		
		$component = new $class($sql);
		$component->set(...$values);
		
		return $component;
	}
}
