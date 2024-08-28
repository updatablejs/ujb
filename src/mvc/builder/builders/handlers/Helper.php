<?php

namespace ujb\mvc\builder\builders\handlers;

use ujb\common\Util;

// https://github.com/mck89/peast?tab=readme-ov-file
class Helper {

	public static function getPointerDeclarationRegexes($type = null) {
		if (is_null($type)) 
			$type = ['import', 'export'];
	
		$regexes = [
			// 'export * from "module";'
			// 'export * as name from "module";'
			// 'export {default, default as alias, name, name as alias} from "module";'
			[
				'regex' => 'export\s+'
					. '(?<names>'
						. '\*'  
						. '|\*\s+as\s+\w+'
						. '|\{[^}]+\}'
					. ')\s+'	
					. '{from};',
				
				'type' => 'export'
			],
			
			// 'export {name as default, name, name as alias, name as "string name"};'
			[
				'regex' => 'export\s+\{'
						. '(?<names>[^}]+)'
					. '\};',
				
				'type' => 'export'
			],
			
			// 'export default name;'
			[
				'regex' => 'export\s+default\s+(?<default>[\w]+);',
				
				'type' => 'export',
				
				'handler' => function($values) {
					return [
						'names' => [[$values['default'], 'default']],
						'file' => null
					];
				}
			],
	
			// 'import * as name from "module";'
			// 'import {default as alias, name, name as alias, "string name" as alias} From "module";'
			[
				'regex' => 'import\s+'
					. '(?<names>'
						. '\*\s+as\s+\w+'	
						. '|\{[^}]+\}' 
					. ')\s+'
					. '{from};',
				
				'type' => 'import'
			],
		
			// 'import defaultExport from "module";'
			[
				'regex' => 'import\s+' 
					. '(?<default>[\w]+)\s+' 
					. '{from};',
				
				'type' => 'import',
				
				'handler' => function($values) {
					return [
						'names' => [['default', $values['default']]],
						'file' => $values['file']
					];
				}
			],
	
			// 'import defaultExport, {name1, name2} from "module";'
			// 'import defaultExport, * as name from "module";'
			[
				'regex' => 'import\s+' 
					. '(?<default>\w+)\s*,\s*' 
					. '(?<names>'
						. '\{[^}]+\}'
						. '|\*\s+as\s+\w+'
					. ')\s+'
					. '{from};',
				
				'type' => 'import',
				
				'handler' => function($values) {
					return [
						'names' => static::parseNames(
							'default as ' . $values['default'] . ',' . trim($values['names']. '{}')),
						'file' => $values['file']
					];
				}
			],
			
			// 'import "module";'
			[
				'regex' => 'import\s+{from};',
				'type' => 'import'
			]
		];
		
		$from = 'from\s+["\'](?<file>[^"\']+)["\']';
		$result = [];
		foreach ($regexes as $regex) {
			if (in_array($regex['type'], $type)) {
				$regex['regex'] = '/(\/\/\s*)?' . str_replace('{from}', $from, $regex['regex']) . '/i';
			
				$result[] = $regex;
			}
		}
		
		return $result; 
	}

	public static function parseNames($string) {
		$string = preg_replace('/^[\s{}]+|[\s{}]+$/u', '', $string);
		
		return array_map(function($value) {
			return preg_match('/\bas\b/i', $value) ? 
				array_map('trim', preg_split('/\bas\b/i', $value, 2)) : trim($value);
		}, explode(',', $string));
	}

	public static function getPointerDeclarations($content, $type = null) {
		$results = [];
		foreach (static::getPointerDeclarationRegexes($type) as $regex) {
			if (!preg_match_all($regex['regex'], $content, $matches, PREG_OFFSET_CAPTURE|PREG_SET_ORDER)) continue;
			
			foreach ($matches as $match) {
				if (preg_match('/^\/\//', $match[0][0])) continue;
				
				$result = [
					'position' => $match[0][1],
					'match' => array_map(function($value) {
						return $value[0];
					}, $match)
				];
					
				if (isset($regex['handler'])) {
					$result['match'] = $regex['handler']($result['match']);
				}
				else {
					$result['match'] = [
						'names' => static::parseNames($result['match']['names'] ?? ''),
						'file' => $result['match']['file'] ?? null
					];
				}
			
				$result['match']['type'] = $regex['type'];

				$results[] = $result;
			}
		}
		
		usort($results, function($a, $b) {
			return $a['position'] - $b['position'];
		});
		
		return array_map(function($result) {
			return $result['match'];
		}, $results);
	}
	
    public static function removePointerDeclarations($content, $type = null) {
        foreach (static::getPointerDeclarationRegexes($type) as $regex) {
            $content = preg_replace($regex['regex'], '', $content);
        }
		
        return $content;
    }
	
	public static function getPointerDeclarationNames($content, $type = null) {
		$result = [];
		foreach (static::getPointerDeclarations($content, $type) as $declaration) {
			$result = array_merge($result, $declaration['names']);
		}
		
		return array_unique($result);
	}

	/**
	 * Actual declaration.
	 * Ex: export function foo() {};
	 */
	public static function cleanActualDeclarations($content) {
		return preg_replace('/\bexport(\s+default)?\b/i', '', $content);
	}

	public static function replaceDefaultWithVariable($content, $variable) {	
		return preg_replace('/export default/', 'var ' . $variable . ' = ', $content);
	}
	
	public static function hasDefault($content) {	
		return preg_match('/\bexport default\b/', $content);
	}
}
