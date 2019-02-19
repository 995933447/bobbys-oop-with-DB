<?php
namespace Bobby\Component\Database\Mysql\Connection;

use PDO;

class SingleConnection
 {
 	private $connector;

    private $connectorLazyGenarator;

 	public function __construct(array $config)
 	{
        //懒加载机制 连接实例用闭包实现懒加载
 		$this->connectorLazyGenarator = function() use($config) {

 			$driverOptions = [
 				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$config['charset']}'",
 				PDO::ATTR_ERRMODE => $config['error_mode'],
 				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
 			];
 			if ($config['timeout'] !== false) $driverOptions[PDO::ATTR_TIMEOUT] = $config['timeout'];
 			if ($config['pconnect']) $driverOptions[PDO::ATTR_PERSISTENT] = true;

 			return $this->connector = new PDO("mysql:host={$config['host']};dbname={$config['database']}", $config['user'], $config['password'], $driverOptions);

 		};
 	}

 	public function getReadConnector()
 	{
 		return $this->connector?: $this->connectorLazyGenarator;
 	}

 	public function getWriteConnector()
 	{
 		return $this->connector?: $this->connectorLazyGenarator;
 	}

 	public function resetConnector()
 	{
 		return call_user_func($this->connectorLazyGenarator);
 	}

 }