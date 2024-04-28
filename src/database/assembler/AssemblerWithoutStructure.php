<?php

namespace ujb\database\assembler;

use ujb\database\result\Result, 
	ujb\database\result\FetchStyle,
	ujb\database\result\source\arraySource\AssociativeArraySource;

class AssemblerWithoutStructure extends Assembler {

	public $group;
	public $interceptor;
	
	public function __construct($group = null, $interceptor = null) {
		if ($group)
			$this->setGroup($group);
		
		if ($interceptor)
			$this->setInterceptor($interceptor);
	}
	
	public function setGroup($group) {
		$this->group = is_array($group) ? 
			$group : [$group, false];					
		
		return $this;
	}
	
	public function setInterceptor($interceptor) {
		$this->interceptor = $interceptor;					
		
		return $this;
	}
	
	public function assemble(Result $result) {
		$rows = [];
		while ($row = $result->fetch(FetchStyle::Associative)) {
			if ($this->group) {	
				$key = is_string($this->group[0]) ? $row[$this->group[0]] : $this->group[0]($row);
				
				if ($this->interceptor)
					$row = ($this->interceptor)($row);
				
				if ($this->group[1])
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
