<?php
namespace Bobby\Component\Route;

use Bobby\{
	Exception\Container\BindContainerException,
	Contract\Container\Container
};

class Resolver
{

	private static $container;

	public static function runRoute($route, $separator, Container $container)
	{
		static::$container = $container;
		$action = $route['action'];
		$routeParameters = $route['params'] ?? [];

		if($action instanceof \Closure) {
			$reflection = new \ReflectionFunction($action);
			$dependencies = static::resolveDependencies($reflection, $routeParameters);
			if(is_null($dependencies))
				return function() use($action) {
					return $action();
				};
			else
				return function() use($dependencies, $action) {
					return call_user_func_array($action, $dependencies);
				};
		}


		list($controller, $method) = explode($separator, $action, 2);
		$controller = $container->make($controller);
		if(!method_exists($controller, $method)) {
			http_response_code(404);
			throw new \Exception('404 Not Found');
		}

		$reflection = new \ReflectionMethod($controller, $method);
		if(!$reflection->isPublic()) throw new \BadMethodCallException('Has not access to call method!');

		$dependencies = static::resolveDependencies($reflection, $routeParameters);
		if(is_null($dependencies))
			return function() use($controller, $method) {
				return $controller->$method();
			};

		return function() use($controller, $method, $dependencies) {
				return $controller->$method(...$dependencies);
		};
	}

	private static function resolveDependencies($reflection, $routeParameters)
	{
		if(!$parameters = $reflection->getParameters()) return null;

		$dependencies = [];
		foreach ($parameters as $parameter) {

			if($class = $parameter->getClass()) {

				try {
					$instance = static::$container->make($class->name);
				} catch(BindContanierException $e) {
					if($parameter->isDefaultValueAvailable())
						$instance = $parameter->getDefaultValue();
					else
						throw $e;
				}

				$dependencies[] = $instance;

			} else {

				if(array_key_exists($parameter->name, $routeParameters))
					$dependencies[] = $routeParameters[$parameter->getName()];
				else
					if($parameter->isDefaultValueAvailable())
						$dependencies[] = $parameter->getDefaultValue();
					else
						throw BindContanierException('Too few parameters');

			}

		}
		return $dependencies;
	}

}