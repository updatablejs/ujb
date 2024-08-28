<?php

namespace ujb\fileIterator;

interface FileIteratorInterface extends \Iterator {

	public function getRelativePath($file);

	public function copy($to);
	
	public function copyDirectories($to);
	
	public function deleteEmptyDirectories();
	
	public function delete();	
	
	public function toArray();	
}
