<?php

namespace ujb\database\orm\schema\joins;

class JoinFactory {

	static public function create($values) {
		return isset($values['refer']) ? 
			new Reference($values) : new Join($values);
	}
}
