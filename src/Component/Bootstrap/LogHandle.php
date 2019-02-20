<?php
 namespace Bobby\Component\Bootstrap;

 use Bobby\Contract\AppEngine\Engine;
 use Psr\Log\{LoggerInterface, LogLevel};

 class LogHandle implements LoggerInterface
 {

 	private $app;

 	public function boot(Engine $app)
 	{
 		$app->instance('Log', $this);
 		$this->app = $app;
 	}

 	private function record($level, $message, $file = '')
 	{
 		if (!$file) {
 			$file = $this->app->config('app.log.path') . '/' . $level .'/' . date('Y-m') . '/' .date('d') . '.php';
 		}
 		is_dir($dir = dirname($file))  || mkdir($dir, 0777, true);
 		error_log($message, 3, $file);
 	}

 	public function __call($method, $parameters)
 	{
 		array_unshift($parameters, $method);
 		return $this->log(...$parameters);
 	}

 	public function log($level, $message, array $context = [])
 	{		
 		if ($context) {
 			foreach ($context as $key => $value) {
 				if ($key === 'exception') 
 					if(!$value instanceof \Exception)
 						throw new \Psr\Log\InvalidArgumentException;
 					else
 						$value = $value->__toString();

 				$search[] = '{' .$key . '}';
 				$replace[] = $value;
 			}

 			$message = str_replace($search, $replace, $message);
 		}


 		$this->record($level, $message, func_num_args() > 3 ? func_get_args()[3] : '');
 	}

 	/**
 	 * System is unusable.
 	 *
 	 * @param string $message
 	 * @param array  $context
 	 *
 	 * @return void
 	 */
 	public function emergency($message, array $context = array())
 	{
 	    $this->log(LogLevel::EMERGENCY, $message, $context, func_num_args() > 2 ? func_get_args()[2] : '');
 	}

 	/**
 	 * Action must be taken immediately.
 	 *
 	 * Example: Entire website down, database unavailable, etc. This should
 	 * trigger the SMS alerts and wake you up.
 	 *
 	 * @param string $message
 	 * @param array  $context
 	 *
 	 * @return void
 	 */
 	public function alert($message, array $context = array())
 	{
 	    $this->log(LogLevel::ALERT, $message, $context, func_num_args() > 2 ? func_get_args()[2] : '');
 	}

 	/**
 	 * Critical conditions.
 	 *
 	 * Example: Application component unavailable, unexpected exception.
 	 *
 	 * @param string $message
 	 * @param array  $context
 	 *
 	 * @return void
 	 */
 	public function critical($message, array $context = array())
 	{
 	    $this->log(LogLevel::CRITICAL, $message, $context, func_num_args() > 2 ? func_get_args()[2] : '');
 	}

 	/**
 	 * Runtime errors that do not require immediate action but should typically
 	 * be logged and monitored.
 	 *
 	 * @param string $message
 	 * @param array  $context
 	 *
 	 * @return void
 	 */
 	public function error($message, array $context = array())
 	{
 	    $this->log(LogLevel::ERROR, $message, $context, func_num_args() > 2 ? func_get_args()[2] : '');
 	}

 	/**
 	 * Exceptional occurrences that are not errors.
 	 *
 	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
 	 * that are not necessarily wrong.
 	 *
 	 * @param string $message
 	 * @param array  $context
 	 *
 	 * @return void
 	 */
 	public function warning($message, array $context = array())
 	{
 	    $this->log(LogLevel::WARNING, $message, $context, func_num_args() > 2 ? func_get_args()[2] : '');
 	}

 	/**
 	 * Normal but significant events.
 	 *
 	 * @param string $message
 	 * @param array  $context
 	 *
 	 * @return void
 	 */
 	public function notice($message, array $context = array())
 	{
 	    $this->log(LogLevel::NOTICE, $message, $context, func_num_args() > 2 ? func_get_args()[2] : '');
 	}

 	/**
 	 * Interesting events.
 	 *
 	 * Example: User logs in, SQL logs.
 	 *
 	 * @param string $message
 	 * @param array  $context
 	 *
 	 * @return void
 	 */
 	public function info($message, array $context = array())
 	{
 	    $this->log(LogLevel::INFO, $message, $context, func_num_args() > 2 ? func_get_args()[2] : '');
 	}

 	/**
 	 * Detailed debug information.
 	 *
 	 * @param string $message
 	 * @param array  $context
 	 *
 	 * @return void
 	 */
 	public function debug($message, array $context = array())
 	{
 	    $this->log(LogLevel::DEBUG, $message, $context, func_num_args() > 2 ? func_get_args()[2] : '');
 	}

 }