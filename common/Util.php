<?php

namespace core\common;

class Util {

	public static function replaceSubstituents(array $pairs, $string) {
		if (!$pairs) return $string;
			
		$search = [];
		$replace = [];
		foreach ($pairs as $key => $value) {
			if (is_scalar($value)) {
				$search[] = '{' . $key . '}';
				$replace[] = $value;
			}
		}
		
		return str_replace($search, $replace, $string);	
	}
	
	public static function isNumericArray($array) {
    	if (!is_array($array)) return false;
		
		foreach (array_keys($array) as $key) {
			if (!is_int($key)) return false;
		}
		
		return true;
	}

	public static function gracefulRound($value, $min = 2, $max = 4) {
		$result = round($value, $min);
		
		return ($result == 0 && $min < $max) ? 
			self::gracefulRound($value, ++$min, $max) : $result;
	}

	public static function getDirectories($path) {
		$result = [];
		foreach (new \DirectoryIterator($path) as $file) {
			if ($file->isDir() && !$file->isDot())
				$result[] = $file->getFilename();
		}
			
		return $result;
	}

	public static function removeWhitespaces($string) {
		return trim(preg_replace('/\s+/', ' ', $string));
	}

	public static function getRandom($length = 16, $prefix = null) {
		if (is_string($length)) {
			$prefix = $length;
			$length = 16;
		}
		
		return substr($prefix . bin2hex(random_bytes($length)), 0, $length);
	}


	// Path
	
	public static function isFullyQualified($path) {
		return preg_match('/^[\/\\\]/', $path);
	}
	
	public static function isQualified($path) {
		return preg_match('/[\/\\\]/', $path);
	}
}
