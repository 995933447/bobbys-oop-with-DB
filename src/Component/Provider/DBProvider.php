<?php 
namespace Bobby\Component\Provider;

use Bobby\{Contract\Provider\Provider, Component\Proxy\Config};

class DBProvider extends Provider
{
	public $isDeffer = true;

	public $provide = ['DB'];

	public function register()
	{ 
		$this->container->singleton('DB', function ($container) {
			return new \Bobby\Component\Database\InstanceFactory(Config::get('database'));
		});
	}

	public function boot()
	{

	}
}