<?php

namespace core\database\assembler;

use core\database\result\Result;

abstract class Assembler {

	abstract public function assemble(Result $result);
}



?>
