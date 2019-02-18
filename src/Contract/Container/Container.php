<?php
/**
 * @author  杨周洪 <en-name:Bobby>
 */
namespace Bobby\Contract\Container;

use Psr\Container\ContainerInterface;

/**
 * IOC服务容器契约
 */
interface Container extends ContainerInterface
{

	/**
	 * [instance 将一个已存在的对象绑定到服务容器里，随后通过名称解析该服务时，容器将总返回这个绑定的实例]
	 * @return [type] [description]
	 */
	public function instance($abstract, $concrete);

	/**
	 * [bind 绑定服务到服务容器]
	 * @return [type] [description]
	 */
	public function bind($abstract, $concrete = null, $isShared = null);

	/**
	 * [singleton singleton 方法是bind方法的变种，绑定一个只需要解析一次的类或接口到容器，然后接下来对于容器的调用该服务将会返回同一个实例]
	 * @return [type] [description]
	 */
	public function singleton($abstract, $concrete);

	/**
	 * [alias 把服务和服务别名注册到容器]
	 * @return [type] [description]
	 */
	public function alias($abstract, $alias);

	/**
	 * [make 从服务容器中解析出服务对象，该方法接收你想要解析的类名或接口名作为参数]
	 * @return [type] [description]
	 */
	public function make($abstracts, array $parameters);

}