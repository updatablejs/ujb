<?php

namespace ujb\mvc\builder\builders\handlers;

use ujb\common\Util;

class Js extends AbstractHandler {

	public function handle($content, $file = null, $builder = null) {
		$dependencies = Helper::getPointerDeclarations($content);
		foreach ($dependencies as &$dependency) {	
			if ($dependency['file']) {
				$dependency['rawFile'] = $dependency['file'];
				$dependency['file'] = Util::resolvePath($dependency['file'], $file);
			}
		}

		if (Helper::hasDefault($content)) {
			$default = '_' . Util::getRandom();
			$content = Helper::replaceDefaultWithVariable($content, $default);
		}

		$content = Helper::removePointerDeclarations($content);
		if (!$builder->isExporter($file)) 
			$content = Helper::cleanActualDeclarations($content);
			
		return ['content' => $content,
			'dependencies' => $dependencies,
			'default' => $default ?? null];
	}
}
