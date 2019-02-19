<?php
namespace Bobby\Contract\Route;

/**
 * 路由器
 */
interface Route
{

	/**
	 * [controllernamespace 设置控制器默认命名空间]
	 * @param [type] $namespace [description]
	 */
	public function controllernamespace($namespace);

	/**
	 * [setRoute 设置路由方法]
	 * @param [type] $method [description]
	 * @param [type] $uri    [description]
	 * @param [type] $action [description]
	 */
	public function setRoute(array $methods, $uri, $action);

	/**
	 * [group 路由组]
	 * @param  [type] $wrappers [description]
	 * @return [type] $callable [description]
	 */
    public function group(array $options = [], \Closure $callable);


	/**
	 * [as 为路由定义别名]
	 * @param  [type] $alias [description]
	 * @return [type]        [description]
	 */
	public function as($alias);

	/**
	 * [findRoute 路由寻址]
	 * @param  [type] $method [description]
	 * @param  [type] $uri    [description]
	 * @return [type]         [description]
	 */
	public function findRoute($method, $uri);

	/**
	 * [cacheRoute 缓存路由]
	 * @return [type] [description]
	 */
	public function cacheRoutes();

	/**
	 * [existCache 检查是否使用路由缓存]
	 * @return [type] [description]
	 */
	public function existCache();

	/**
	 * [useCacheRoutes 使用路由缓存]
	 * @return [type] [description]
	 */
	public function useCacheRoutes();

	/**
	 * [clearCache 清理路由缓存]
	 * @return [type] [description]
	 */
	public function clearCache();

	/**
	 * [setCacheKey 设置缓存key]
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function setCacheKey($key);

}