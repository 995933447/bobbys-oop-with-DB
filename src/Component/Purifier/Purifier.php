<?php 
namespace Bobby\Component\Purifier;

use Bobby\Contract\Purifier\Purifier as PurifierContract;

class Purifier implements PurifierContract
{
	public static function filterByCallBacks($value, array $callbacks)
	{
		if (is_array($value)) foreach ($value as $k => $v) $value[$k] = static::filterByCallBacks($v, $callbacks);

		else return array_reduce($callbacks, function($value, $callback) {
		        return call_user_func($callback, $value);
		    }, $value);

		return $value;
	} 
}