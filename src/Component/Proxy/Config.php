<?php
namespace Bobby\Component\Proxy;

use Bobby\Contract\Proxy\Proxy as ProxyContract;

/**
 * 配置类代理
 */
class Config extends Proxy implements ProxyContract
{

	public static function getProxySubject()
	{
		return '\\Bobby\\Contract\\Config\\Config';
	}

}