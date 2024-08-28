<?php

namespace ujb\common;

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
		return ArrayUtil::isNumeric($array);
	}

	public static function gracefulRound($value, $min = 2, $max = 4) {
		$result = round($value, $min);
		
		return ($result == 0 && $min < $max) ? 
			self::gracefulRound($value, ++$min, $max) : $result;
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
	
	public static function isAbsolutePath($path) {
		return preg_match('/^[\/\\\]/', $path);
	}
	
	public static function isPath($path) {
		return preg_match('/[\/\\\]/', $path);
	}
	
	/**
	 * $base: a/b/file.js
	 * $path: 
	 * 	./file.js => a/b/file.js
	 *	../file.js => a/file.js
	 *	../../../file.js => ../file.js
	 */
	public static function resolvePath($path, $base) {
		if (strpos(basename($base), '.') !== false)
			$base = dirname($base);

		$path = $base . '/' . $path;
		$parts = [];
		foreach (explode('/', $path) as $part) {
			if ($part === '..') {
				if (!empty($parts) && end($parts) !== '..')
					array_pop($parts);
				else
					$parts[] = $part;	
			} 
			elseif ($part !== '.' && $part !== '') {
				$parts[] = $part;
			}
		}
		
		return implode('/', $parts);
	}
	
	public static function isDirectoryEmpty($directory) {
		if ($directory instanceof \SplFileInfo)
			$directory = $directory->getPathname();
		
		return is_dir($directory) && !(new \FilesystemIterator($directory))->valid();
	}
}
