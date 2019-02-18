<?php 
namespace Bobby\Component\Http\Request;

use Bobby\Component\Purifier\Purifier;

trait FilterTrait
{
	private $filter;

	public function setGlobalFilter(array $callbacks)
	{
	    $this->filter = $callbacks;
	}

	public function filter(string $name, array $callbacks = null, $append = true)
	{
	    if($append && $this->filter) $callbacks = array_merge($this->filter, $callbacks);
	    $name = explode('.', $name, 2);

	    list($method, $parameter) = isset($name[1]) ? $name : [$name[0], null];
	    $value = is_null($parameter) ? $this->$method() : $this->$method($parameter);

	    return $callbacks ? Purifier::filterByCallBacks($value, $callbacks) : $value;
	}
}