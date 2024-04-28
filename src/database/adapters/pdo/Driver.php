<?php

namespace ujb\database\adapters\pdo;

use ujb\database\adapters\Driver as DriverInterface;

class Driver implements DriverInterface {

	private $config;
	private $connection;
	
	public function __construct($config) {		
		if (!isset($config['driver'])) $config['driver'] = 'mysql';
		if (!isset($config['charset'])) $config['charset'] = 'utf8';
		if (!isset($config['options'])) $config['options'] = [];

		$this->config = $config;
	}
	
	public function open() {		
		if ($this->connection) 
			return $this->connection;
		
		try { // https://stackoverflow.com/questions/26085193/password-leak-from-php-pdo-object-in-error-log
			extract($this->config);
			
			$dsn = $driver . ':dbname=' . $name.';'
				. 'host=' . $host.';'
				. 'charset=' . $charset;
			
			$this->connection = new \PDO($dsn, $username, $password, $options);
			$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			
			return $this->connection;
		} 
		catch (\PDOException $e) {
			throw new \Exception('Connection failed.');
		}
	}
	
	public function close() {
		if ($this->connection) {
			unset($this->connection);
			$this->connection = null;
		}
		
		return $this;
	}
	
	public function getConnection() {
		return $this->open();
	}

	public function prepare($sql, array $options = null) {
		return new Statement($this->getConnection()->prepare($sql, $options), $this->config['driver']);
	}

	public function quote($value) {
		return $this->getConnection()->quote($value);
	}
	
	public function getLastInsertId() {
		return $this->getConnection()->lastInsertId();
	}
	
	public function beginTransaction() {
		return $this->getConnection()->beginTransaction();
	}
	
	public function rollBack() {
		return $this->getConnection()->rollBack();
	}
	
	public function commit() {
		return $this->getConnection()->commit();
	}
}
