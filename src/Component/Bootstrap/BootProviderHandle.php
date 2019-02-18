<?php
namespace Bobby\Component\Bootstrap;

use Bobby\Contract\AppEngine\Engine;

class BootProviderHandle
{

	public function boot(Engine $app)
	{
		$app->completeBoot();
		foreach ($app->services as $provider) {
			$provider->boot();
		}
	}

}