<?php

namespace ujb\cache\contentPreparator;

class DefaultContentPreparator implements ContentPreparator {

	public function encode($content) {
		return serialize($content);
	}
	
	public function decode($content) {
		return unserialize($content);
	}   
}
