<?php

namespace ujb\mvc\builder\builders;

use ujb\mvc\builder\builders\handlers\Js as Handler, 
	ujb\common\Util;

class Js extends Builder {

	protected $exporters = [];
	
	// Files that keep their order during sorting.
	//protected $keepOrderFiles = [];
	
	protected $files = [];
	
	public function __construct(array $values) {
		if (!array_key_exists('defaultHandler', $values))
			$values['defaultHandler'] = new Handler();
		
		$this->hydrate($values, ['source']);
	}
	
	public function setExporters($exporters) { 
		$this->exporters = (array) $exporters;

		return $this;
	}
	
	public function isExporter($file) { 
		return in_array($file, $this->exporters);
	}
	
	public function initFiles() { 		
		foreach ($this->source as $file) {
			$this->files[$file->getPathname()] = parent::getContent($file);
		}
		
		return $this;
	}
	
	public function getFiles() { 		
		if (!$this->files) {
			$this->initFiles();
		}
		
		return $this->files;
	}

	public function fileExists($file) {
		return array_key_exists($file, $this->files);
	}

	public function getContent($file) {
		return $this->fileExists($file) ? $this->files[$file] : null;
	}
	
	public function sortFiles() { 		
		$processed = [];
		$result = [];
		
		$do = function(string $file) use (&$do, &$processed, &$result) {
			$processed[] = $file;
		
			$content = $this->getContent($file);

			$dependencies = isset($content['dependencies']) ? 
				(array) $content['dependencies'] : [];
				
			foreach ($dependencies as $dependency) {
				if (!$dependency['file']) continue;
					
				if (in_array($dependency['file'], $processed)) continue;	

				if ($this->fileExists($dependency['file'])) 
					$do($dependency['file']);
			}
			
			$result[$file] = $content;
		};
		
		foreach (array_keys($this->files) as $file) {
			if (!in_array($file, $processed))
				$do($file);
		}
					
		return $result;
	}

	protected function resolveDefaults() {		
		$resolved = [];
		$resolve = function() use (&$resolved) {
			foreach ($this->files as $file => &$content) {
				if (isset($resolved[$file])) continue;
				
				// Must be implemented.
				/* export * from 'module';
				export * as name from 'module';
				export {default, default as alias, name as default, name, name as alias} from 'module';
				export {name as default, name, name as alias, name as 'string name'};
				export default name;
				import * as name from 'module';
				import {default as alias, name, name as alias, 'string name' as alias} From 'module';
				import defaultExport from 'module';
				import defaultExport, {name1, name2} from 'module';
				import defaultExport, * as name from 'module';
				import 'module';*/
	
				$resolved[$file] = $content['default'];
			}
		};		
		
		while (count($resolved) != count($this->files)) {
			$resolve();
		}
		
		return $this;
	}

	protected function insertAliases() {
		$getVar = function($name, $dependency) {
			if (is_array($name)) {
				if ($dependency['type'] == 'import') {
					if (!$this->fileExists($dependency['file'])) return null;

					// import {default as alias, name, name as alias, 'string name' as alias} from 'module';
					// import defaultExport from 'module';
					// import defaultExport, {name} from 'module';
					if ($name[0] == 'default') 
						return 'var ' . $name[1] . ' = ' . $this->getContent($dependency['file'])['default'] . ';';
					else 
						return 'var ' . $name[1] . ' = ' . $name[0] . ';';	
								
					// Must be implemented.
					// import * as name from 'module';	
					// import defaultExport, * as name from 'module';
					// import 'module';
				}
						
				else if ($dependency['type'] == 'export') {
					// Must be implemented.
					// export * from 'module';
					// export * as name from 'module';
					// export {default, default as alias, name as default, name, name as alias} from 'module';
					// export {name as default, name, name as alias, name as 'string name'};
					// export default name;
				}
						
				else 
					throw new \Exception('Unknown type ' . $$dependency['type']);	
				}
			
			else {
				// Must be implemented.
			}
		};
		
		foreach ($this->files as $file => &$content) {
			$vars = '';
			foreach ($content['dependencies'] as $dependency) {
				foreach ($dependency['names'] as $name) {
					$var = $getVar($name, $dependency);
					if ($var) $vars .= $var;
				}
			}

			if ($vars) 
				$content['content'] = $vars . $content['content'];
		}
	}

	public function build() {
		$this->initFiles()
			->resolveDefaults()
			->insertAliases(); 
			
		$files = $this->sortFiles();
		$result = '';
		$externalDependencies = [];
		foreach ($files as $file => $content) {
			$result .= '/** ' . $file . ' */' . $content['content'];
			
			if ($this->isExporter($file)) {
				$names = [];
				foreach ($content['dependencies'] as $dependency) {
					if ($dependency['type'] == 'export')
						$names = array_merge($names, $dependency['names']);
				}
				
				$result .= 'export {' . implode(',', $names) . '};';
			}

			foreach ($content['dependencies'] as $dependency) {
				if (!$dependency['file'] || array_key_exists($dependency['file'], $files)) continue;
				
				if (isset($externalDependencies[$dependency['file']]))
					$externalDependencies[$dependency['file']]['names'] = array_merge(
						$externalDependencies[$dependency['file']]['names'], $dependency['names']); 
									
				else
					$externalDependencies[$dependency['file']] = $dependency;
			}
		}
		
		$externalDependencies_ = '';
		foreach ($externalDependencies as $dependency) {
			$externalDependencies_ .= 'import {' . implode(',', $dependency['names']) . '} from \'' . $dependency['rawFile'].'\';
			';
		}
		
		$result = $externalDependencies_ . $result;
	
		if ($this->to) $this->save($result);
	
		return $result;
	}

	public function getConflicts() { 
		$conflicts = [];
		foreach ($this->getFiles() as $file => $content) {
			if (!isset($content['dependencies'])) continue;
			
			foreach ($content['dependencies'] as $dependency) {
				if (!$dependency['file']) continue;
				
				$conflict = $this->getConflict($file, $dependency['file']);
				
				if ($conflict)
					$conflicts[] = [$file, $dependency['file'], 'conflict' => $conflict];
			}
		}
		
		return $conflicts;
	}
	
	public function getConflict($file1, $file2, $list = []) { 
		if (empty($list)) $list = [$file1];

		$content = $this->getContent($file2);
		
		if (!$content) return null;
		
		$list[] = $file2;
		
		foreach ($content['dependencies'] as $dependency) {
			if (!$dependency['file']) continue;
			
			if ($dependency['file'] == $file1) {
				$list[] = $dependency['file'];
				
				return $list;	
			}
				
			if (in_array($dependency['file'], $list)) continue;
		
			$conflict = $this->getConflict($file1, $dependency['file'], $list);
		
			if ($conflict) return $conflict;
		}
		
		return null;
	}
}
