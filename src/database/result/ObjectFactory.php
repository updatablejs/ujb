<?php

namespace ujb\database\result;

use ujb\database\result\source\Source;

interface ObjectFactory {

	public function create($values);
}
