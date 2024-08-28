<?php

namespace ujb\http;

class RequestFactory {
	
	static public function create(array $values = null) {
		if (!$values) {
			$files = self::getFiles();
			$values = [
				'query' => $_GET,
				'post' => self::arrayMergeRecursive($_POST, $files),
				'files' => $files,
				'cookies' => $_COOKIE,
				'server' => $_SERVER
			];
		}
		
		return new Request($values);
	}
	
	static protected function getFiles() {
		$files = [];
		foreach ($_FILES as $key => $value) {
			$files[$key] = self::rearrangeFilesArray($value);
		}
		
		return $files;
	}
	
	static protected function rearrangeFilesArray($array, $key = null, &$result = null) {
		if (is_null($key)) {
			if (!is_array($array['name'])) return $array;
		
			$result = [];
			foreach ($array as $key => $value)
				self::rearrangeFilesArray($value, $key, $result);
			
			return $result;
		}

		foreach ($array as $k => $v) {
			if (is_array($v)) {
				if (!isset($result[$k])) $result[$k] = [];
	
				self::rearrangeFilesArray($v, $key, $result[$k]);
			}
			else
				$result[$k][$key] = $v;
		}
	}
	
	static protected function arrayMergeRecursive(...$arrays) {
		$base = array_shift($arrays);
		
		foreach ($arrays as $array) {
			foreach ($array as $key => $value) {
				if (is_array($value) && isset($base[$key]) && is_array($base[$key]))
					$base[$key] = self::arrayMergeRecursive($base[$key], $value);
				else
					$base[$key] = $value;
			}
		}
	
		return $base;
	}
}
