<?php
namespace Bobby\Component\Proxy;

use Bobby\Contract\Proxy\Proxy as ProxyContract;

/**
 * 路由器代理
 */
class Route extends Proxy implements ProxyContract
{

	public static function getProxySubject()
	{
		return '\\Bobby\\Contract\\Route\\Route';
	}

}