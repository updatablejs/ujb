<?php

namespace ujb\http\response;

use ujb\http\response\cookies\Cookies, 
	ujb\collection\ArrayMap, 
	ujb\mvc\Theme;

class Response {

	public $success;
	public $message;
	public $content;

	public $headers;
	public $cookies;
	
	public $theme;

	public function __construct(Headers $headers = null, Cookies $cookies = null) {
		$this->headers = $headers ? $headers : new Headers();
		$this->cookies = $cookies ? $cookies : new Cookies();
	}

	public function setCookie(string $name, string $value, $options = []) {
		$this->cookies->set($name, $value, $options);
		
		return $this;
	}

	public function setSuccess(bool $success) {
		$this->success = $success;
		
		return $this;
	}
	
	public function setMessage(string $message) {
		$this->message = $message;
		
		return $this;
	}
	
	public function set($key, $value = null) {
		$this->getContent()->set($key, $value);
		
		return $this;
	}
	
	public function get($key = null) {
		return $this->getContent()->get($key);
	}
	
	public function clear() {
		if ($this->content instanceof ArrayMap)
			$this->content->clear();
		else
			$this->content = null;

		return $this;
	}
	
	public function setContent($content) {
		$this->content = $content;
		
		return $this;
	}
	
	public function setContentType($value) {
		$this->headers->setContentType($value);
		
		return $this;
	}

	protected function getContent() {
		if (!$this->content)
			$this->content = new ArrayMap();
		
		return $this->content;
	}

	public function setTheme(Theme $theme) {
		$this->theme = $theme;
		
		return $this;
	}

	public function hasTheme() {
		return !empty($this->theme);
	}

	public function getResponse() {
		$response = ['success' => (bool) $this->success];
		
		if ($this->message) 
			$response['message'] = $this->message;
		
		if ($this->content) 
			$response['content'] = $this->content instanceof ArrayMap ? $this->content->toArray() : $this->content;
		
		return $response;	
	}

	public function send() {
		if (!$this->headers->hasContentType() && !$this->hasTheme())
			$this->headers->setContentType('application/json');
		
		$this->headers->send();
		$this->cookies->send();
		
		if ($this->hasTheme())
			$this->theme->display($this->content instanceof ArrayMap ? $this->getContent()->toArray() : $this->content);
		
		else if ($this->headers->getContentType() == 'application/json')
			echo json_encode($this->getResponse());
		
		else if (is_file($this->content)) 
			readfile($this->content);
			
		else
			echo $this->content;	
	}
}
