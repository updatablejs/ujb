<?php

namespace ujb\cache\contentPreparator;

interface ContentPreparator {

	public function encode($content);
	
	public function decode($content);
}
