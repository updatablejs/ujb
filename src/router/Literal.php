<?php

namespace ujb\router;

use ujb\http\Request;

class Literal extends AbstractRoute {
	
	public function match(Request $request) {
		if (!$this->routable)
			return parent::match($request);
		
		return $request->getPath() == $this->getPath() ?
			$this->getValues() : parent::match($request);
	}
	
	public function build() {
		return $this->getPath();
	}
}
