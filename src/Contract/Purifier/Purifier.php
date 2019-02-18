<?php 
namespace Bobby\Contract\Purifier;
 
interface Purifier
{

	/**
	 * [filterByCallBacks 通过回调过滤变量]
	 * @param  [type] $var       [description]
	 * @param  array  $callbacks [description]
	 * @return [type]            [description]
	 */
	public static function filterByCallBacks($var, array $callbacks);

}