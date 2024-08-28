<?php

namespace ujb\http;

use ujb\collection\ArrayMap;

class Request {
	
	protected $query;	
	protected $post;
	protected $files;
	protected $cookies;
	protected $server;
	protected $sitePath;

	public function __construct(array $values = null) {
		foreach ($this->getProperties() as $property)
			$this->$property = new ArrayMap();

		if ($values) $this->setValues($values);
	}
	
	public function setValues(array $values) {
		foreach ($this->getProperties() as $property) {
			if (isset($values[$property]))
				$this->$property->set($values[$property]);
		}
		
		return $this;
	}
	
	protected function getProperties() {
		return ['query', 'post', 'files', 'cookies', 'server'];
	}
	
	public function setSitePath(string $sitePath) {
		$this->sitePath = $sitePath;
		
		return $this;
	}
	
	public function getUri($query = true) {
		$uri = preg_replace('/^' . preg_quote($this->sitePath, '/')  . '/', '', 
			$this->server->get('REQUEST_URI'));
		
		if (!$query)
			$uri = strtok($uri, '?');

		$uri = rtrim($uri, '/');

		if (!preg_match('/^\//', $uri))
			$uri = '/' . $uri;
		
		return $uri;
	}
	
	public function getPath() {
		return $this->getUri(false);
	}
	
	public function __get($name) {
		if (!property_exists($this, $name)) throw new \Exception(
			'Undefined property via __get(): ' . $name);
		
		return $this->$name;
    }
	
	
	// Shortcuts 

	public function getPost($keys = null, $default = null) {
		return $this->post->get($keys, $default);
	}

	public function hasPost(...$keys) {	
		return $keys ? $this->post->hasKeys(...$keys) : !$this->post->isEmpty();	
	}
	
	public function getQuery($keys = null, $default = null) {
		return $this->query->get($keys, $default);
	}

	public function hasQuery(...$keys) {
		return $keys ? $this->query->hasKeys(...$keys) : !$this->query->isEmpty();	
	}

	public function get($key, $default = null) {
		return $this->query->get($key, $default);
	}
		
	public function getCookie($key, $default = null) {
		return $this->cookies->get($key, $default);
	}	
}
