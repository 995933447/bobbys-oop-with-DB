<?php
namespace Bobby\Component\Bootstrap;

use Bobby\Contract\{Config\Config, AppEngine\Engine};

class ConfigHandle implements Config, \ArrayAccess
{
	private $items;

	public function boot(Engine $app)
	{
		if(!$this->items) {
			$this->items = $app->config();
		}
		$app->instance('\\Bobby\\Contract\\Config\\Config', $this);
		date_default_timezone_set($this->items['app']['timezone']);
		$charset = $this->items['app']['charset'] ?? 'UTF-8';
		mb_internal_encoding($charset);
	}

	public function get($key = null, $defaultValue = null)
	{
		if(!$key) return $this->items;

		if(!strrpos($key, '.')) {
			return $this->items[$key] ?? $defaultValue;
		}

		$despatch = explode('.', $key);

		foreach($despatch as $segment) {
			if(!isset($item)) {
				if(!isset($this->items[$segment])) {
					return $defaultValue;
				}
				$item = $this->items[$segment];
			} else {
				if(!isset($item[$segment])) {
					return $defaultValue;
				}
				$item = $item[$segment];
			}
		}

		return $item;
	}

	public function set($key, $value)
	{
		if(!strrpos($key, '.')) {
			$this->items[$key] = $value;
		}

		$despatch = explode('.', $key);

		foreach($despatch as $segment) {
			if(!isset($item))
				$item = $this->items[$segment];
			else
				$item = $item[$segment];

		}

		return $item = $value;

	}

	public function offsetSet($key, $value) {
	    $this->items[$key] = $value;
	}

	public function offsetExists($key)
	{
		return isset($this->items[$key]);
	}

	public function offsetGet($key)
	{
		return $this->items[$key];
	}

	public function offsetUnset($key)
	{
		unset($this->items[$key]);
	}

}