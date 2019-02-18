<?php
namespace Bobby\Contract\Provider;

use Bobby\Contract\Container\Container;

abstract Class Provider
{

	/**
	 * [$container DI容器]
	 * @var [type]
	 */
	protected $container;

	/**
	 * [__construct 构造函数]
	 * @param [type] $container [description]
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * [$isDeffer 是否延迟加载]
	 * @var boolean
	 */
	public $isDeffer = false;

	/**
	 * [$provide 绑定延迟加载的接口名称数组]
	 * @var [type]
	 */
	public $provide;

	/**
	 * [$events 触发延迟加载的事件]
	 * @var [type]
	 */
	public $events;

	/**
	 * [register 注册服务提供者]
	 * @return [type] [description]
	 */
	abstract public function register();

	/**
	 * [boot 启动服务提供者]
	 * @return [type] [description]
	 */
	abstract public function boot();

}