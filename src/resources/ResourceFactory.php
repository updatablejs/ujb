<?php

namespace ujb\resources;

class ResourceFactory {
	
	static public function create($resource, bool $shared = true) {	
		if (is_callable($resource))
			return new Callback($resource, $shared);
			
		elseif (is_string($resource) && strpos($resource, '\\') !== false)
			return new Initializer($resource, $shared);
			
		else
			return new Fixed($resource);
	}
}
