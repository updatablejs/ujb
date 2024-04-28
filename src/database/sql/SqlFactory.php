<?php

namespace ujb\database\sql;

class SqlFactory {	

	public static function create($type, $database = null) {
		if (strpos($type, '{'))
			return (new SqlWithPlaceholders($database))->setQuery($type);
		elseif (strpos($type, '?'))
			return (new PresetSql($database))->setQuery($type);
		
		$class = __NAMESPACE__ . '\\' . ucfirst(strtolower($type));
		
		return new $class($database);
	}
	
    public static function __callStatic($name, $args) {
		return self::create($name, array_shift($args));
    }
}
