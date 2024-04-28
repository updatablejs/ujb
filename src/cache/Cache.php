<?php

namespace ujb\cache;

interface Cache {

	public function set($key, $data);
	
	public function get($key);
	
	public function contains($key);
	
	public function remove($key);
	
	public function needUpdate($key);
}
