<?php
/**
 * @author  杨周洪 <en-name:Bobby>
 */
namespace Bobby\Component\Container;
use Bobby\{
	Contract\Container\Container as ContainerContract,
	Exception\Container\BindContainerException,
	Exception\Container\ServiceNotFoundException,
};

/**
 * IOC服务容器实现类
 */
class Container implements ContainerContract
{
	protected $instances;
	protected $aliases;
	protected $bindings;

	/**
	 * [instance 绑定已实例化的对象，随后通过名称解析该服务时，容器将总返回这个绑定的实例]
	 * @param  [type] $abstract [description]
	 * @param  [type] $concrete  [description]
	 * @return [type]           [description]
	 */
	public function instance($abstract, $concrete)
	{
		if(is_array($abstract)) {
			list($abstract, $alias) = $abstract;
			$this->alias($abstract, $alias);
		}

		if(!is_object($concrete)) {
			throw new BindContainerException('The second parameter mast be a instance');
		}

		$this->instances[$abstract] = $concrete;
	}

	/**
	 * [alias 为服务绑定别名]
	 * @param  [type] $abstract [description]
	 * @param  [type] $alias    [description]
	 * @return [type]           [description]
	 */
	public function alias($abstract, $alias)
	{
		$this->aliases[$alias] = $abstract;
	}

	/**
	 * [dropAlias 删除服务别名]
	 * @param  [type] $alias [description]
	 * @return [type]        [description]
	 */
	public function dropAlias($alias)
	{
		if(isset($this->aliases[$alias])) unset($this->aliases[$alias]);
	}

	/**
	 * [bind 绑定服务到服务容器]
	 * @param  [type] $abstract [description]
	 * @param  [type] $concrete [description]
	 * @param  [type] $isshared [description]
	 * @return [type]           [description]
	 */
	public function bind($abstract, $concrete = null, $isshared = null)
	{
		if(is_array($abstract)) {
			list($abstract, $alias) = $abstract;
			$this->alias($abstract, $alias);
		}

		$this->dropIfExistInstance($abstract);

		if(is_null($concrete)) {
			$concrete = $abstract;
		}

		if(!$concrete instanceof Closure) {
			$concrete = $this->getDefaultClosure($abstract, $concrete);
		}

		$this->bindings[$abstract] = compact('concrete', 'isshared');

	}

	/**
	 * [dropIfExistInstance 删除instance已存在指定实例]
	 * @param  [type] $abstract [description]
	 * @return [type]           [description]
	 */
	protected function dropIfExistInstance($abstract) {
		if(isset($this->instances[$abstract]))
			 unset($this->instances[$abstract]);
		if(isset($this->aliases[$abstract]))
			throw new BindContainerException('IOC container has bound a alias of service as same with ' . $abstract);
	}

	/**
	 * [getDefaultClosure 获取默认闭包]
	 * @param  [type] $abstract [description]
	 * @param  [type] $concrete [description]
	 * @return [type]           [description]
	 */
	protected function getDefaultClosure($abstract, $concrete)
	{
		return function(Container $container, array $parameters = []) use ($abstract, $concrete) {
			$method = $abstract == $concrete ? 'build' : 'make';
			return $container->{$method}($concrete, $parameters);
		};
	}

	/**
	 * [singleton 绑定单例]
	 * @param  [type] $abstract [description]
	 * @param  [type] $concrete [description]
	 * @return [type]           [description]
	 */
	public function singleton($abstract, $concrete)
	{
		$this->bind($abstract, $concrete, true);
	}

	/**
	 * [make 解析服务]
	 * @param  string $abstract  [description]
	 * @param  array  $parameter [description]
	 * @return [type]            [description]
	 */
	public function make($abstract, array $parameter = [])
	{
		$abstract = $this->getAlias($abstract);

		if(isset($this->instances[$abstract])) return $this->instances[$abstract];

		$concrete = $this->getConcrete($abstract);

		if($this->isBuildable($abstract, $concrete)) {
			$dispatch = $this->build($concrete, $parameter);
		} else {
			$dispatch = $this->make($concrete, $parameter);
		}

		if(isset($this->bindings[$abstract]) && $this->bindings[$abstract]['isshared'])
			$this->instances[$abstract] = $dispatch;

		return $dispatch;

	}

	/**
	 * [getAlias 构建服务对象]
	 * @param  [type] $abstract [description]
	 * @return [type]           [description]
	 */
	protected function getAlias($abstract)
	{
	   	return $this->aliases[$abstract] ?? $abstract;
	}

	/**
	 * [getConcrete 获取服务实现]
	 * @param  [type] $abstract [description]
	 * @return [type]           [description]
	 */
	protected function getConcrete($abstract)
	{
		return $this->bindings[$abstract]['concrete'] ?? $abstract;
	}

	/**
	 * [isBuildable 判断是否满足构建实例条件]
	 * @param  [type]  $abstract [description]
	 * @param  [type]  $concrete [description]
	 * @return boolean           [description]
	 */
	protected function isBuildable($abstract, $concrete)
	{
		return $abstract == $concrete || $concrete instanceof \Closure;
	}

	/**
	 * [build 创建构造实例对象]
	 * @param  [type] $concrete  [description]
	 * @param  [type] $parameter [description]
	 * @return [type]            [description]
	 */
	protected function build($concrete, $parameters)
	{
		if($concrete instanceof \Closure) {
			return $concrete($this, $parameters);
		}

		$reflection = new \ReflectionClass($concrete);

		if(!$reflection->isInstantiable()) {
			throw new BindContainerException('Class ' . $concrete . ' is not instantiable');
		}

		$constructor = $reflection->getConstructor();

		if(is_null($constructor) || !$dependencies = $constructor->getParameters()) {
			return new $concrete();
		}

		$dependencies = $this->resolveDependencies($dependencies, $parameters);

		return $reflection->newInstanceArgs($dependencies);
	}

	/**
	 * [resolveDependencies 自动解决依赖]
	 * @param  [type] $dependency [description]
	 * @param  [type] $parameter  [description]
	 * @return [type]             [description]
	 */
	protected function resolveDependencies($dependencies, $parameters)
	{
		$resolve = [];

		foreach ($dependencies as $argument) {

			if(isset($parameters[$argument->name])) {

				$resolve[$argument->name] = $parameters[$argument->name];

			} else {

				if($getClass = $argument->getClass()) {

					try {
						$instance = $this->make($getClass->name);
					} catch(BindContainerException $e) {

						if($argument->isDefaultValueAvailable()) {
							$instance = $argument->getDefaultValue();
						} else {
							throw $e;
						}

					}

					$resolve[$argument->name] = $instance;

				} else {

					if($argument->isDefaultValueAvailable()) {
						$resolve[$argument->name] = $argument->getDefaultValue();
					} else {
						throw new BindContainerException($argument->name . ' has no default value');

					}

				}

			}

		}

		return $resolve;
	}

	public function get($abstract)
	{
		return $this->make($abstract);
	}

	public function has($abstract)
	{
		return isset($this->bindings['abstract']) || isset($this->instances[$abstract]) || isset($this->aliases[$abstract]);
	}

}