<?php

namespace ujb\http\response\cookies;

class Cookie {

	protected $name;
	protected $value;
	protected $expires = 0;
	protected $path = '/';
	protected $domain = '';
	protected $secure = false;
	protected $httpOnly = false;

	public function __construct(string $name, string $value, array $options = []) {
		$this->name = $name;
		$this->setValue($value)
			->setOptions($options);
	}
	
	public function setOptions(array $options) {
		foreach ($options as $key => $value) {			
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method))
				$this->$method($value);
			elseif (property_exists($this, $key))
				$this->$key = $value;
			else
				throw new \Exception('Unknown property ' . $key);
		}
		
		return $this;
	}

	public function send() {
		setcookie($this->name, $this->value, 
			$this->expires, $this->path, $this->domain, $this->secure, $this->httpOnly);

		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setValue($value) {
		$this->value = $value;
	
		return $this;
	}

	public function getValue() {
		return $this->value;
	}

	public function setExpires(int $expires) {
		$this->expires = $expires;
		
		return $this;
	}

	public function getExpires() {
		return $this->expires;
	}

	public function setPath(string $path) {
		$this->path = $path;
		
		return $this;
	}

	public function getPath() {
		return $this->path;
	}
	
	public function setDomain(string $domain) {
		$this->domain = $domain;
		
		return $this;
	}

	public function getDomain() {
		return $this->domain;
	}

	public function setSecure(bool $secure) {
		$this->secure = $secure;
		
		return $this;
	}

	public function getSecure() {
		return $this->secure;
	}

	public function setHttpOnly(bool $httpOnly) {
		$this->httpOnly = $httpOnly;
		
		return $this;
	}

	public function getHttpOnly() {
		return $this->httpOnly;
	}

	public function __toString() {
		return (string) $this->getValue();
	}
}
