<?php

namespace core\database\result;

use core\database\result\source\Source;

interface ObjectFactory {

	public function create($values);
}
