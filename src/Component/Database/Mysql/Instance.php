<?php
namespace Bobby\Component\Database\Mysql;

use Bobby\Component\Database\{
	SqlBuilder,
	Mysql\connection\SingleConnection,
	Mysql\connection\SeparateReadAndWriteConnection
};

use Bobby\Contract\Database\Instance as InstanceContract;

class Instance implements InstanceContract
{
	private $connection;

	private $connector;

	private $prefix;

	public function __construct(array $config)
	{
		if((isset($config['read']) && $config['read']) || (isset($config['write']) && $config['write']))

			$this->connection = new SeparateReadAndWriteConnection($config);

		else

			$this->connection = new SingleConnection($config);

		$this->prefix = $config['prefix'];

	}

	public function connectRead()
	{
		return $this->initSqlBuidler($this->connection->getReadConnector(), $this->prefix);
	}

	public function connectWrite()
	{
		return $this->initSqlBuidler($this->connection->getWriteConnector(), $this->prefix);
	}

	public function table($name, $prefix = null)
	{
		return $this->initSqlBuidler(null, $this->prefix)->table($name, $prefix);
	}

	public function query($sql, array $parameters = null, $connector = null)
	{
		if(!$connector) $connector = $this->connection->getReadConnector();
		$connector = $this->makeConnector($connector);

		if(!$parameters)
			$res = $connector->query($sql);
		else {
			$res = $connector->prepare($sql);
			$res->execute($parameters);
		}

		$res->setFetchMode(\PDO::FETCH_ASSOC);
		return $res->fetchAll();

	}

	public function insert($sql, array $parameters = null, $connector = null)
	{
		if(!$connector) $connector = $this->connection->getWriteConnector();
		$connector = $this->makeConnector($connector);

		if(!$parameters) 
			$connector->exec($sql);
		else {
			$stmt = $connector->prepare($sql);
			$stmt->execute($parameters);
		}

		return $connector->lastInsertId();
	}

	public function update($sql, array $parameters = null, $connector = null)
	{
		if(!$connector) $connector = $this->connection->getWriteConnector();
		$connector = $this->makeConnector($connector);

		if(!$parameters)
			return $count = $connector->exec($sql);
		else {
			$stmt = $connector->prepare($sql);
			$stmt->execute($parameters);
			return  $stmt->rowCount();
		}
	}

	public function delete($sql, array $parameters = null, $connector = null)
	{
		return $this->update($sql, $parameters?: [], $connector);
	}

	protected function makeConnector($connector)
	{
		return $connector instanceof \Closure ? $connector() : $connector;
	}

	public function initSqlBuidler($connector = null, $prefix)
	{
		return (new SqlBuilder)->setConnectInstance($this, $connector, $prefix);
	}

}