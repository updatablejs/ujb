<?php

namespace ujb\http\response;

class Headers {

    protected $headers = [];

	public function set(string $name, string $value) {
		$this->headers[$name] = $value;
		
		return $this;
	}

	public function setRaw(string $header) {
		$this->headers[$header] = null;
		
		return $this;
	}

	public function get(string $name) {
		return $this->has($name) ? $this->headers[$name] : null;
	}

	public function remove(string $name) {
		unset($this->headers[$name]);
		
		return $this;
	}
	
    public function has(string $name) {
        return array_key_exists($name, $this->headers);
    }
	
	public function isEmpty() {
		return empty($this->headers);
	}
	
	public function clear() {
		$this->headers = [];
		
		return $this;
	}
	
	public function send() {
		foreach ($this->headers as $header => $value) {
			if (!is_null($value))
				header($header . ': ' . $value);
			else
				header($header);
		}
		
		return $this;
	}
	
	public function toArray() {
		return $this->headers;
	}
	
	
	// Helpers 
	
	public function getContentType() {
		return $this->get('Content-Type');
	}
	
	public function setContentType($value) {
		$this->set('Content-Type', $value);
		
		return $this;
	}
	
	public function hasContentType() {
		return $this->has('Content-Type');
	}
}
