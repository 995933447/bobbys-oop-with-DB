<?php 
namespace Bobby\Component\Proxy;

use Bobby\Contract\Proxy\Proxy as ProxyContract;

/**
 * 数据库Orm代理类
 */
class DB extends Proxy implements ProxyContract
{

	public static function getProxySubject()
	{
		return 'DB';
	}

}