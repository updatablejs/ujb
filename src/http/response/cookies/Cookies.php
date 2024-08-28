<?php

namespace ujb\http\response\cookies;

class Cookies {

    protected $cookies = [];

	public function set(string $name, string $value, $options = []) {
		if (!is_array($options))
			$options = ['expires' => $options];
		
		$cookie = $this->get($name);
		if ($cookie) {
			$cookie->setValue($value)
				->setOptions($options); 
		}
		else {
			$this->cookies[$name] = new Cookie($name, $value, $options);
		}
		
		return $this;
	}

	public function get(string $name) {
		return $this->has($name) ? $this->cookies[$name] : null;
	}

	public function has(string $name) {
		return isset($this->cookies[$name]) ? true : false;
	}

	public function remove(string $name) {
		unset($this->cookies[$name]);

		return $this;
	}

	public function send() {
		foreach ($this->cookies as $cookie){
			$cookie->send();
		}
		
		return $this;
	}

	public function clear() {
		$this->cookies = [];
		
		return $this;
	}
}
