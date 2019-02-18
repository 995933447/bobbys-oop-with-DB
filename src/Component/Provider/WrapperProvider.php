<?php
namespace Bobby\Component\Provider;

use Bobby\Contract\Provider\Provider;

class WrapperProvider extends Provider
{
	public function register()
	{
		if($this->wrap) foreach ($this->wrap as $name => $wrapper) $this->container->bind($name, $wrapper);
	}

	public function boot()
	{

	}
}