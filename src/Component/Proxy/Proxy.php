<?php
namespace Bobby\Component\Proxy;

use Bobby\Contract\Container\Container;

/**
 * 代理基类
 */
class Proxy
 {

 	protected static $container;

 	protected static $resolvedInstance;

 	public static function setContanier(Container $container)
 	{
 		static::$container = $container;
 	}

 	public static function __callStatic($method, $arguments)
 	{
 		$service = static::getProxySubject();
 		if(isset(static::$resolvedInstance[$service])) {
 			$instance = static::$resolvedInstance[$service];
 		} else {
 			$instance = static::$container->make($service);
 			static::$resolvedInstance[$service] = $instance;
 		}
 		return $instance->$method(...$arguments);
 	}

}