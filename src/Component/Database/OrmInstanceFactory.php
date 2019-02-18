<?php 
namespace Bobby\Component\Database;

class OrmInstanceFctory
{
	private $config;

	private $suportDriver = ['mysql'];

	private $instances = [];

	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function __call($method, $parameters)
	{
		return $this->make()->{$method}(...$parameters);
	}

	public function make($name = null)
	{
		$name = $name?: $this->config['default'];

		if(isset($this->instances[$name])) return $this->instances[$name];

		if(!in_array($name, $this->suportDriver)) throw new InvalidArgumentException("Unsport database dirver {$name}");

		$config = $this->config['connections'][$name];
		$className = '\\Bobby\\Component\\Database\\' . ucfirst($config['driver']) . '\\Instance';
		$instance = new $className($config);
		
		return $this->instances[$name] = $instance;
	}

}