<?php
 namespace Bobby\Component\Event;

 use Bobby\Contract\{Event\Handle as EventContract, Container\Container};

 class Handle implements EventContract
 {
 	private $container;

 	public function __construct(Container $Container)
 	{
 		$this->container = $Container;
 	}

 	/**
 	 * [listen 注册监听事件，两种方式:1.事件类一对多监听器类的数组;2.事件名称字符串绑定监听器闭包，可多次绑定]
 	 * @param  [type] $event    [description]
 	 * @param  [type] $listener [description]
 	 * @return [type]           [description]
 	 */
 	public function listen($event, $listener)
 	{
 		$this->listen[$event][] = $this->makeListener($listener);
 	}

 	private function makeListener($listener)
 	{

 		if(is_string($listener)) {
 			return function($event) use($listener) {
 				return $this->container->make($listener)->handle($event);
 			};
 		} else if($listener instanceof \Closure) {
 			return function($payload) use($listener) {
 				return $listener(...$payload);
 			};
 		} else {
 			throw new \Exception("Listener of event mast be class or closure");
 		}

 	}


 	public function fire($event, array $payload = [], $halt = false)
 	{
 		if(is_object($event)) {
 			list($event, $payload) = ['\\' . get_class($event), $event];
 		}

 		$listeners = $this->listen[$event];
 		$responses = [];

 		foreach($listeners as $listener) {
 			$response= $listener($payload);
 			if($response === false || (!$halt && $response != false)) {
 				return $response;
 			}
 			$responses[] = $response;
 		}

 		return $responses;
 	}

 }