<?php
namespace Bobby\Component\Bootstrap;

use Bobby\{Contract\AppEngine\Engine, Component\Proxy\Proxy};

class ProxyHandle
{

	public function boot(Engine $app)
	{
		Proxy::setContanier($app);
	}

}