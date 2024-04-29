<?php

use ujb\common\Chronometer;

function printr($array) {
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}

function getExecutionTime($callback, $iterations) {
	$chronometer = new Chronometer();
	for ($i = 0; $i < $iterations; $i++) {
		$callback();
	}
	
	$time = $chronometer->stop();
	
	return ['total' => round($time, 8), 
		'iteration' => round($time / $iterations, 8)];
}

if (!function_exists('is_iterable')) {
	function is_iterable($obj) {
        return is_array($obj) || $obj instanceof \Traversable;
    }
}

if (!function_exists('str_starts_with')) {
	function str_starts_with($haystack, $needle) {
    	 return substr($haystack, 0, strlen($needle)) === $needle;
	}
}

if (!function_exists('str_ends_with')) {
	function str_ends_with($haystack, $needle) {
   		$length = strlen($needle);
    
		return $length ? substr($haystack, -$length) === $needle : true;
	}
}
