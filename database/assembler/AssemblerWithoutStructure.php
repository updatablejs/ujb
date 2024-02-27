<?php

namespace core\database\assembler;

use core\database\result\Result, 
	core\database\result\FetchStyle,
	core\database\result\source\arraySource\AssociativeArraySource;

class AssemblerWithoutStructure extends Assembler {

	public $groupingSpec;
	public $interceptor;
	
	public function __construct($groupingSpec = null, $interceptor = null) {
		if ($groupingSpec)
			$this->setGroupingSpec($groupingSpec);
		
		if ($interceptor)
			$this->setInterceptor($interceptor);
	}
	
	public function setGroupingSpec($groupingSpec) {
		$this->groupingSpec = is_array($groupingSpec) ? 
			$groupingSpec : [$groupingSpec, false];					
		
		return $this;
	}
	
	public function setInterceptor($interceptor) {
		$this->interceptor = $interceptor;					
		
		return $this;
	}
	
	public function assemble(Result $result) {
		$rows = [];
		while ($row = $result->fetch(FetchStyle::Associative)) {
			if ($this->groupingSpec) {	
				$key = is_string($this->groupingSpec[0]) ? $row[$this->groupingSpec[0]] : $this->groupingSpec[0]($row);
				
				if ($this->interceptor)
					$row = ($this->interceptor)($row);
				
				if ($this->groupingSpec[1])
					$rows[$key][] = $row;
				else
					$rows[$key] = $row;
			}
			else
				$rows[] = $this->interceptor ? ($this->interceptor)($row) : $row;
		}
		
		return new Result(new AssociativeArraySource($rows));
	}	
}
