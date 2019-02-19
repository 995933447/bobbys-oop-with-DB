<?php
namespace Bobby\Component\Proxy;

use Bobby\Contract\Proxy\Proxy as ProxyContract;

class Event extends Proxy implements ProxyContract
{

	public static function getProxySubject()
	{
		return '\\Bobby\\Contract\\Event\\Handle';
	}

}