<?php

namespace ujb\common;

class Benchmark {

	protected $chronometer;
	protected $memoryAtStart;
	protected $memoryAtStop;

	public function __construct() {
		$this->memoryAtStart = memory_get_usage();
		
		$this->chronometer = new Chronometer();
	}
	
	public function stop() {
		$this->memoryAtStop = memory_get_usage();
		
		return $this->chronometer->stop();
	}
	
	public function reset() {
		$this->chronometer->reset();
		$this->memoryAtStart = memory_get_usage();
		$this->memoryAtStop = null;
		
		return $this;
	}
	
	public function mark($key = null, $extra = null) {		
		$extra = [
			'memory' => memory_get_usage(),
			'extra' => $extra
		];
		
		return $this->chronometer->mark($key, $extra);
	}
	
	public function getMarks() {
		$marks = [];
		foreach ($this->chronometer->getMarks() as $key => $mark) {
			$mark['memory'] = $mark['extra']['memory'];
			
			unset($mark['extra']['memory']);
			
			$mark['extra'] = $mark['extra']['extra'];
			
			$marks[$key] = $mark;
		}
		
		return $marks;
	}
	
	public function getMemoryAtStart() {
		return $this->memoryAtStart;
	}
	
	public function getMemoryAtStop() {
		return $this->memoryAtStop;
	}	
	
	public function getDuration() {
		return $this->chronometer->getDuration();
    }
	
	public function getStartTime() {
		return $this->chronometer->getStartTime();
    }
	
	public function getStopTime() {
		return $this->chronometer->getStopTime();
    }
	
	public function getDetails() {
		return array_merge($this->chronometer->getDetails(), [
			'memoryAtStart' => $this->memoryAtStart,
			'memoryAtStop' => $this->memoryAtStop
		]);
	}
	
	public static function do($callback, $iterations) {
		$benchmark = new self();
		$result = [];
		for ($i = 0; $i < $iterations; $i++) {
			$callbackResult = $callback($i);
			if ($callbackResult)
				$result[] = $callbackResult;
		}
		
		$benchmark->stop();
		
		return $benchmark;
	}
}
