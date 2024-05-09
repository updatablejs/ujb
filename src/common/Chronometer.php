<?php

namespace ujb\common;

class Chronometer {

	public $startTime;
	public $stopTime;
	public $marks = [];

	public function __construct() {
		$this->start();
	}
   
	public function start() {
		$this->startTime = microtime(true);
	}
	
	public function stop() {
		$this->stopTime = microtime(true);
		
		return $this->getDuration();
	}
	
	public function getDuration() {
		return $this->getDifference($this->startTime, $this->stopTime);
    }
	
	public function getStartTime() {
		return $this->startTime;
    }
	
	public function getStopTime() {
		return $this->stopTime;
    }
	
	public function reset() {
		$this->stopTime = null;
		$this->marks = [];
		$this->start();
		
		return $this;
	}
	
	public function mark($key = null, $extra = null) {		
		$time = microtime(true);
		$duration = $this->getDifference($this->startTime, $time);
	
		if ($key) 
			$this->marks[$key] = compact('time', 'duration', 'extra');
		else
			$this->marks[] = compact('time', 'duration', 'extra');
		
		return $duration;
	}

	public function getMarks() {
		return $this->marks;
	}

	public function getDetails() {
		return [
			'startTime' => $this->startTime,
			'stopTime' => $this->stopTime,
			'duration' => $this->getDuration()
		];
	}

	protected function getDifference($time1, $time2) {
		return $time2 - $time1 > 0 ?  $time2 - $time1 : 0; 
	}
}
