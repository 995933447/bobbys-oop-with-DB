<?php
namespace Bobby\Component\Wrapper;

use Bobby\Contract\Container\Container;

class Wrapper
{

	public static function handle($wrappers, $destination, Container $container)
	{
		if(!$wrappers)
			return $destination();
		else {
			$wrappers = array_reverse($wrappers);

			return call_user_func(array_reduce($wrappers, static::initWrap($container), $destination));
		}
	}

	private static function initWrap($container)
	{

		return function($next, $wrapper) use($container) {

			return function() use($next, $wrapper, $container) {

				if(strpos($wrapper, ':')) {

					list($name, $parameters) = explode($wrapper, ':', 2);
					$paramters = explode($paramters, ',');
					$paramters = array_merge([$next], $parameters);

				} else {

					$name = $wrapper;
					$parameters = [$next];

				}

				return $container->make($name)->handle(...$parameters);

			};

		};
	}

}