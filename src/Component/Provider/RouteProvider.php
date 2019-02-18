<?php
namespace Bobby\Component\Provider;

use Bobby\{
	Contract\Provider\Provider,
	Component\Proxy\Config,
};

class RouteProvider extends Provider
{

	public function register()
	{
		$this->container->singleton('\\Bobby\\Contract\\Route\\Route', '\\Bobby\\Component\\Route\\Manager');
	}

	public function boot()
	{
		$router = $this->container->make('\\Bobby\\Contract\\Route\\Route');

		extract(Config::get('app.route.persistence'));

		$router->setCacheKey($key);

		if($persistent && $router->existCache()) {

			return $router->useCacheRoutes();

		}

		$namespace = Config::get('app.route.namespace');
		$router->controllernamespace($namespace);
		$file = Config::get('app.route.file');
		require $file;

		if($persistent) {
			$router->cacheRoutes();
		} else {
			$router->clearCache();
		}
	}


}