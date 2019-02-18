<?php
namespace Bobby\Component\Route;

/**
 * 路由器
 */
class Manager extends Base
{

	/**
	 * [wrap 添加路由包装器]
	 * @param  [type] $wrappers [description]
	 * @return [type]           [description]
	 */
	public function wrappers($wrappers)
	{
		$wrappers = is_string($wrappers) ? [$wrappers] : $wrappers;
		$uri = $this->currentRoute['uri'];
		if($this->currentRoute['type'] == 'simple') {
			foreach ($this->currentRoute['methods'] as $method) {
				if(!isset($this->simpleRoutes[$method][$uri]['wrappers']))
					$this->simpleRoutes[$method][$uri]['wrappers'] = $wrappers;
				else
					$this->simpleRoutes[$method][$uri]['wrappers'] = array_merge($wrappers, $this->simpleRoutes[$method][$uri]['wrappers']);
			}
		} else if($this->currentRoute['type'] == 'regex') {
			foreach ($this->currentRoute['methods'] as $method) {
				if(!isset($this->regexRoutes[$method][$uri]['wrappers']))
					$this->regexRoutes[$method][$uri]['wrappers'] = $wrappers;
				else
					$this->regexRoutes[$method][$uri]['wrappers'] = array_merge($wrappers, $this->regexRoutes[$method][$uri]['wrappers']);
			}
		}
		return $this;
	}

	public function get($uri, $action)
	{
		$this->setRoute(['GET', 'HEAD'], $uri, $action);
		return $this;
	}

	public function post($uri, $action)
	{
		$this->setRoute(['POST'], $uri, $action);
		return $this;
	}

	public function put($uri, $action)
	{
		$this->setRoute(['PUT'], $uri, $action);
		return $this;
	}

	public function delete($uri, $action)
	{
		$this->setRoute(['DELETE'], $uri, $action);
		return $this;
	}

	public function any($uri, $action)
	{
		$this->setRoute(['GET', 'HEAD', 'POST', 'PUT', 'DELETE'], $uri, $action);
		return $this;
	}

}