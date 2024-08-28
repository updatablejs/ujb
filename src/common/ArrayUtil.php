<?php

namespace ujb\common;

class ArrayUtil {

	public static function isNumeric($array) {
    	if (!is_array($array)) return false;
		
		foreach (array_keys($array) as $key) {
			if (!is_int($key)) return false;
		}
		
		return true;
	}

	public static function moveValuesUp($array, $values) {	
		return static::moveValues($array, $values, 'up');
	}
	
	public static function moveValuesDown($array, $values) {	
		return static::moveValues($array, $values, 'down');
	}
	
	protected static function moveValues($array, $values, string $location) {	
		if ($array instanceof Arrayable)
			$array = $array->toArray();
			
		if ($values instanceof Arrayable)
			$values = $values->toArray();
		
		$move = [];
		foreach ($values as $value) {
			foreach ($array as $k => $v) {
				if ($v === $value) {
					$move[$k] = $v;
					unset($array[$k]); 
				}
			}
		}
	
		switch ($location) { 
			case 'up':
				return $move + $array;
			
			case 'down':
				return $array + $move;
			
			default:
				throw new \Exception('Unknown location ' . $location);	
		}
	}
}
