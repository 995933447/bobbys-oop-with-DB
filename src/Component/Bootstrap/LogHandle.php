<?php
 namespace Bobby\Component\Bootstrap;

 use Bobby\Contract\AppEngine\Engine;

 class LogHandle
 {

 	private $app;

 	public function boot(Engine $app)
 	{
 		$app->instance('Log', $this);
 		$this->app = $app;
 	}

 	private function record($level, $msg, $file = '')
 	{
 		if(!$file) {
 			$file = $this->app->config('app.log.path') . '/' . $level .'/' . date('Y-m') . '/' .date('d') . '.php';
 		}
 		is_dir($dir = dirname($file))  || mkdir($dir, 0777, true);
 		error_log($msg, 3, $file);
 	}

 	public function __call($method, $parameters)
 	{
 		array_unshift($parameters, $method);
 		return $this->record(...$parameters);
 	}

 }