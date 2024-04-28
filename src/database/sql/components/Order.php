<?php

namespace ujb\database\sql\components;

class Order extends AbstractComponent {

	public $orders = [];
	
	public function set($field, $order = null) {
		if (is_array($field)) 
			$this->setOrders($field);
		else
			$this->orders[$field] = $this->prepareOrderType($order);
	}

	// setOrders([[field, order], 'field' => 'order', 'field']);
	public function setOrders(array $orders) {
		foreach ($orders as $key => $value) {
			if (!is_string($key)) {
				$value = (array) $value;
				$key = array_shift($value);
				$value = array_shift($value);
			}
			
			$this->orders[$key] = $this->prepareOrderType($value);
		}
	}

	protected function prepareOrderType($orderType) {
		return (is_null($orderType) || strtoupper($orderType) == 'ASC') ? 
			'ASC' : 'DESC';
	}

	public function build() {
		$result = [];
	  	foreach ($this->orders as $field => $order)
			$result[] = $field . ' ' . $order;

		return implode(', ', $result);
	
	}
	
	public function isEmpty() {
		return empty($this->orders);
	}
}
