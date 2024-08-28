<?php

namespace ujb\router\valuesContainer\valuesContainerFactory;

use ujb\router\valuesContainer\ValuesContainer;

class ValuesContainerFactory extends AbstractValuesContainerFactory {
	
	public function create(array $values = null) {
		return new ValuesContainer($values);
	}
}
