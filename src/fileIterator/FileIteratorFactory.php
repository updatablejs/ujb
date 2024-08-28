<?php

namespace ujb\fileIterator;

use ujb\common\Util;
	
class FileIteratorFactory {
	
	static public function create($values) {
		if (!is_array($values) || Util::isNumericArray($values)) 
			$values = ['source' => $values];
		
		if (!isset($values['internalUseFileIteratorFactory']))
			$values['internalUseFileIteratorFactory'] = self::getInternalUseFileIteratorFactory();
		
		if (isset($values['order'])) {
			if (!($values['source'] instanceof FileIteratorInterface))
				$values['source'] = new FileIterator($values);
			
			return new OrderedFileIterator($values);
		}
		
		return new FileIterator($values);
	}
	
	static public function getInternalUseFileIteratorFactory() {
		return new class() implements InternalUseFileIteratorFactory {
		
			public function create($source) {
				if ($source instanceof \SplFileInfo)
					$source = $source->getPathname();
				
				return is_array($source) ? 
					new \ArrayIterator($source) : new \FilesystemIterator($source, \FilesystemIterator::UNIX_PATHS);
			}
		};
	}
}
