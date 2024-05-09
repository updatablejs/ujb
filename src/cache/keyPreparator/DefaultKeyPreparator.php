<?php

namespace ujb\cache\keyPreparator;

class DefaultKeyPreparator implements KeyPreparator {

	public function prepare($key) {
		return md5($key);
	}
}
