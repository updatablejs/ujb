<?php

namespace ujb\database\assembler;

use ujb\database\result\Result;

abstract class Assembler {

	abstract public function assemble(Result $result);
}
