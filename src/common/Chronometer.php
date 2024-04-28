<?php

namespace ujb\common;

class Chronometer {

	public $startTime;
	public $stopTime;
	public $timeList = [];

	public function __construct() {
		$this->start();
	}
   
	public function start() {
		$this->startTime = microtime(true);
	}
	
	public function stop() {
		$this->stopTime = microtime(true);
		
		return $this->getTime();
	}
	
	public function getTime() {
		$result = $this->stopTime - $this->startTime;
        
		return $result > 0 ? $result : 0;
    }
	
	public function reset() {
		$this->startTime = null;
		$this->stopTime = null;
		$this->timeList = [];
	}
	
	public function mark($key = null) {
		$time = microtime(true);
		
		if ($key) $this->timeList[$key] = $time;
	
		return $time - $this->startTime;
	}

	public function display() {
		foreach ($this->timeList as $key => $time) {
			echo $key . ' ' . ($time - $this->startTime) . '<br>';
		}
	}
}
