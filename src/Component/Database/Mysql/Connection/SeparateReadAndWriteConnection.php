<?php
namespace Bobby\Component\Database\Mysql\Connection;

use PDO;

class SeparateReadAndWriteConnection
{

	private $readConnectors = [];

	private $writeConnectors = [];

	public function __construct(array $config)
	{
		$this->readyConnector($config, 'read');
		$this->readyConnector($config, 'write');
	}

	/**
	 * [readyConnector 初始化延迟连接器]
	 * @param  [type] $config        [description]
	 * @param  [type] $connectorType [description]
	 * @return [type]                [description]
	 */
	private function readyConnector($config, $connectorType)
	{
		$connectors = "{$connectorType}Connectors";
		foreach ($config[$connectorType] as $index => $typeConfig) {

			$host = $typeConfig['host'] ?? $config['host'];
			$user = $typeConfig['user'] ?? $config['user'];
			$password = $typeConfig['password'] ?? $config['password'];
			$database = $typeConfig['database'] ?? $config['database'];
			$driverOptions = [
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '" . ($typeConfig['charset'] ?? $config['charset']) . "'",
				PDO::ATTR_ERRMODE => $typeConfig['error_mode'] ?? $config['error_mode'],
				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
			];

			if (!isset($typeConfig['timeout']) && $config['timeout'] !== false) $driverOptions[PDO::ATTR_TIMEOUT] = $config['timeout'];
			else if (isset($typeConfig['timeout']) && $typeConfig['timeout'] !== false) $driverOptions[PDO::ATTR_TIMEOUT] = $typeConfig['timeout'];

			if ((!isset($typeConfig['pconnect']) && $config['pconnect']) || (isset($typeConfig['pconnect']) && $typeConfig['pconnect']))	
				$driverOptions[PDO::ATTR_PERSISTENT] = true;

			$this->$connectors[$index]['genarator'] = function () use($index, $host, $database, $user, $password, $driverOptions) {

				return $this->$connectors[$index]['instance'] = new PDO("mysql:host={$host};dbname={$database}", $user, $password, $driverOptions);

			};

		}
	}

	/**
	 * [getReadConnector 获得读相关连接]
	 * @return [type] [description]
	 */
	public function getReadConnector()
	{
		$connector = array_rand($this->readConnectors, 1);
		return $connector['instance'] ?? $connector['genarator'];
	}

	/**
	 * [getWriteConnector 获得写相关连接]
	 * @return [type] [description]
	 */
	public function getWriteConnector()
	{
		$connector = array_rand($this->writeConnectors, 1);
		return $connector['instance'] ?? $connector['genarator'];
	}

	/**
	 * [resetConnector 重连]
	 * @param  PDO    $connector     [description]
	 * @param  [type] $connectorType [description]
	 * @return [type]                [description]
	 */
	public function resetConnector(PDO $connector, $connectorType)
	{
		$connectors = "{$connectorType}Connectors";

		foreach ($this->connectors as $index => $connectArray)
			if($connectArray[$index]['instance'] === $connector)
				return call_user_func($connectArray[$index]['genarator']);
	}



}