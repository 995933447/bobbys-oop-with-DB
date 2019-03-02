<?php 
namespace Bobby\Component\Http\Request\Instance;

use Bobby\Component\Http\Request\FilterTrait;

trait InstanceTrait
{

	use FilterTrait;

	private $server;

	private $post;

	private $get;

	private $files;

	private $coockie;

	private $PHPInput;

	private $allHttpData;

	public function get($name = null, $defaultValue = null) {
	    return $this->call('get', $name ? [$name, $defaultValue] : null);
	}

	public function post($name = null, $defaultValue = null) {
	    return $this->call('post', $name ? [$name, $defaultValue] : null);
	}

	public function files($name = null, $defaultValue = null) {
	    return $this->call('files', $name ? [$name, $defaultValue] : null);
	}

	public function coockie($name = null, $defaultValue = null) {
	    return $this->call('cookie', $name ? [$name, $defaultValue] : null);
	}

	public function server($name = null, $defaultValue = null) {
	    return $this->call('server', $name ? [$name, $defaultValue] : null);
	}

	private function call($method, $parameters = null)
	{
	    $this->setParamters($method);

	    if (!$parameters) return $this->$method;

	    if (isset($parameters[1]))
	        list($parameterName, $defaultValue) = $parameters;
	    else
	        list($parameterName, $defaultValue) = [$parameters[0], null];

	    if (is_array($parameterName)) return $this->$method[key($parameterName)] = current($parameterName);

	    return $this->$method[$parameterName]?? $defaultValue;
	}

	public function http()
	{
	    $this->setAllHttpData();
	    return $this->allHttpData;
	}

	public function header($name = null)
	{
	    $this->setAllHttpData();
	    return $name ? $this->header[$name] : $this->header;
	}

	public function uri()
	{   
	    if ($pathinfo = $this->server('path_info')) { 
	        return $pathinfo;
	    }

	    if(strpos($uri = $this->server('request_uri'), $script = $this->server('php_self')) === 0) {
	    	$uri = mb_substr($uri, mb_strlen($script)); 
	    }

	    $uri = ($uri === '' || $uri{0} === '?') ? '/' : $uri;
	    
	    if($position = strpos($uri, '?') !== false) {
	    	return substr($uri, 0, $position);
	    }
	    return $uri;
	}

	public function uriWithQuery()
	{
	    return $this->uri() . '?' . $this->server('query_string');
	}

	public function method()
	{
	    return $this->server('request_method');
	}

	public function time()
	{
	    return $this->server('request_time');
	}

}