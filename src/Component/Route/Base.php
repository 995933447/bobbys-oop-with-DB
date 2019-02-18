<?php
namespace Bobby\Component\Route;

use Bobby\Contract\Route\Route as BaseRouteContract;

/**
 * 路由器
 */
class Base implements BaseRouteContract
{

	protected $simpleRoutes;
	protected $regexRoutes;
	protected $routeAliases;
	protected $controllerNamespace;
	protected $countGroupStack = 0;
	protected $groupQueue = [];
	protected $currentRoute;
	protected $cacheKey;

	/**
	 * [controllernamespace 设置控制器默认命名空间]
	 * @param [type] $namespace [description]
	 */
	public function controllernamespace($namespace)
	{
		$this->controllerNamespace = $namespace;
	}

	/**
	 * [setRoute 设置路由方法]
	 * @param [type] $method [description]
	 * @param [type] $uri    [description]
	 * @param [type] $action [description]
	 */
	public function setRoute(array $methods, $uri, $action)
	{
		if(is_string($action)) $action = $this->controllerNamespace . $action;
		else if(!$action instanceof \Closure) throw new \BadMethodCallException("Route must be name of Contrller's action or Closure");

		if(strpos($uri, '{') !== false && strpos($uri, '}') !== false) {

			$uriPreg = '/{.*?}/';
			preg_match_all($uriPreg, $uri, $matches);
			$matches = $matches[0];

			foreach ($matches as $match) {

				if($position = strpos($match, ':')) {
				    $params[] = substr($match, 1, $position - 1);
            		$match = substr($match, $position + 1, -1);
            		$regexs[] =  "({$match})";
        		} else {
        			$params[] = substr($match, 1, -1);
            		$regexs[] = '(.+?)';
        		}

			}
			$matches[] = '/';
			$regexs[] = '\/';

			$uri = str_replace($matches, $regexs, $uri);
			$uri = "/^{$uri}$/";

			foreach ($methods as $method) {
				$this->regexRoutes[$method][$uri]['action'] = $action;
				$this->regexRoutes[$method][$uri]['params'] = $params;
			}

			$this->currentRoute = [
				'type' => 'regex',
				'methods' => $methods,
				'uri' => $uri
			];

		} else {

			if($uri[0] !== '/') $uri = '/' . $uri;

			foreach ($methods as $method) {
				$this->simpleRoutes[$method][$uri]['action'] = $action;
			}

			$this->currentRoute = [
				'type' => 'simple',
				'methods' => $methods,
				'uri' => $uri
			];

		}

		$this->pushGroupQueue();

		return $this;
	}

	/**
	 * [group 路由组]
	 * @param  [type] $wrappers [description]
	 * @return [type] $callable [description]
	 */
    public function group(array $options = [], \Closure $callable)
    {
    	$this->countGroupStack++;

    	$callable($this);
    	if($options) {
    		foreach ($this->groupQueue as  $route) {
    			$this->currentRoute = $route;
    			foreach ($options as $option => $value) {
    				if(method_exists($this, $option))
    					$this->$option($value);
    			}
    		}
    	}

    	$this->countGroupStack--;
    	if(!$this->countGroupStack) $this->groupQueue = [];
    	return $this;
    }

	/**
	 * [pushGroupQueue 将goup回调里定义的路由放到group列队]
	 * @return [type] [description]
	 */
	private function pushGroupQueue()
	{
		if($this->countGroupStack) {
			$this->groupQueue[] = $this->currentRoute;
		}
	}

	/**
	 * [as 为路由定义别名]
	 * @param  [type] $alias [description]
	 * @return [type]        [description]
	 */
	public function as($alias)
	{
	   $this->routeAliases[$alias] = $this->currentRoute;
	   return $this;
	}

	/**
	 * [findRoute 路由寻址]
	 * @param  [type] $method [description]
	 * @param  [type] $uri    [description]
	 * @return [type]         [description]
	 */
	public function findRoute($method, $uri)
	{
		if(isset($this->simpleRoutes[$method][$uri])) return $this->simpleRoutes[$method][$uri];

		foreach ($this->regexRoutes[$method] as $uriRegex => $route)
			if(preg_match_all($uriRegex, $uri, $uriMatch)) {

				if($uriMatch) {
					foreach ($route['params'] as $index => $param) {
						if(isset($uriMatch[$index + 1]) && $uriMatch[$index + 1])
							$route['params'][$param] = $uriMatch[$index + 1][0];
						unset($route['params'][$index]);
					}
					return $route;
				}

			}

		throw new \BadMethodCallException("Route 404 Not Found", 404);

	}

	/**
	 * [cacheRoute 缓存路由]
	 * @return [type] [description]
	 */
	public function cacheRoutes()
	{
		$cacheRoutes = [
			'simple_routes' => $this->simpleRoutes,
			'regex_routes' => $this->regexRoutes,
			'route_aliases' => $this->routeAliases
		];

		apcu_add($this->cacheKey, $cacheRoutes);
	}

	/**
	 * [existCache 检查是否使用路由缓存]
	 * @return [type] [description]
	 */
	public function existCache()
	{
		return apcu_exists($this->cacheKey);
	}

	/**
	 * [useCacheRoutes 使用路由缓存]
	 * @return [type] [description]
	 */
	public function useCacheRoutes()
	{
		$cacheRoutes = apcu_fetch($this->cacheKey);
		$this->simpleRoutes = $cacheRoutes['simple_routes'];
		$this->regexRoutes = $cacheRoutes['regex_routes'];
		$this->routeAliases = $cacheRoutes['route_aliases'];
	}

	/**
	 * [clearCache 清理路由缓存]
	 * @return [type] [description]
	 */
	public function clearCache()
	{
		apcu_delete($this->cacheKey);
	}

	/**
	 * [setCacheKey 设置缓存key]
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function setCacheKey($key)
	{
		$this->cacheKey = $key;
	}

}