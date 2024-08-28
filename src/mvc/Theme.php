<?php

namespace ujb\mvc;

use ujb\collection\ArrayMap,
	ujb\common\Util;

class Theme {
	
	protected $themePath;
	protected $filePath;
	protected $themeUrl;
	protected $siteUrl;
	protected $builder;

	public function __construct() {}

	public function setThemePath($themePath) {
		$this->themePath = rtrim($themePath, '/');
		
		return $this;
	}
	
	public function getThemePath($append = null) {
		return $append ? 
			$this->themePath . '/' . ltrim($append, '/') : $this->themePath;
	}
	
	public function setFilePath($filePath) {	
		$this->filePath = $filePath;
		
		return $this;
	}
	
	public function getFilePath() {
		return $this->filePath;
	}
	
	public function setThemeUrl($themeUrl) {
		$this->themeUrl = rtrim($themeUrl, '/');
		
		return $this;
	}
	
	public function getThemeUrl($append = null) {
		return $append ? 
			$this->themeUrl . '/' . ltrim($append, '/') : $this->themeUrl;
	}
	
	public function setSiteUrl($siteUrl) {
		$this->siteUrl = rtrim($siteUrl, '/');
		
		return $this;
	}
	
	public function getUrl($append = null) {
		return $append ? 
			$this->siteUrl . '/' . ltrim($append, '/') : $this->siteUrl;
	}
	
	public function setBuilder(builder\ThemeBuilder $builder) {
		$this->builder = $builder;
		
		return $this;
	}
	
	public function getBuilder() {
		return $this->builder;
	}
	
	public function display($content = null) {
		if ($content instanceof ArrayMap)
			 $content = $content->toArray();
		
		if (is_array($content))
			extract($content);

		include($this->themePath . '/' . $this->filePath);
	}
}
