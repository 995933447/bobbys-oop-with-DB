<?php
namespace Bobby\Component\Bootstrap;

use Bobby\Contract\AppEngine\Engine;

class ExceptionHandle
{
	private $app;

	private $isReport;

	public function boot(Engine $app)
	{
		error_reporting(E_ALL);
		set_error_handler([$this, 'errorTrigger']);
		set_exception_handler([$this, 'exceptionTrigger']);
		if(!$app->isDevelopment()) ini_set('display_errors', 'off');
		if($app->config('app.log.record_exception')) $this->isReport = true;
		$this->app = $app;
	}

	public function errorTrigger($level, $error, $file = '', $line = 0, $context = [])
	{
		throw new \ErrorException($error, 0, $level, $file, $line);
	}

	public function exceptionTrigger($e)
	{
		$this->report($e);
		try {
			$whoops = new \Whoops\Run;
			$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
			$whoops->register();
			$whoops->handleException($e);
		} catch(\Exception $e) {
			echo '['.date('Y-m-d h:i:s').']  ,error code: ' . $e->getCode() . ' error message:' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine() . "\n" . $e->getTraceAsString();
		}
	}

	private function report($e)
	{
		if($this->isReport) {
			$msg = '['.date('Y-m-d h:i:s').']  ,error code: ' . $e->getCode() . ' error message:' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine() . "\n" . $e->getTraceAsString() . "\n\n";
			$this->app->make('Log')->error($msg);
		}
	}
}