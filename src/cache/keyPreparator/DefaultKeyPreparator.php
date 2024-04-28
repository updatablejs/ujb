<?php

namespace ujb\cache\keyPreparator;

class DefaultKeyPreparator implements keyPreparator {

	public function prepare($key) {
		return md5($key);
	}
}
