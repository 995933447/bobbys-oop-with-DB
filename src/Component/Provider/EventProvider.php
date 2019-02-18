<?php
 namespace Bobby\Component\Provider;

 use Bobby\Contract\Provider\Provider;

 class EventProvider extends Provider
 {

 	public function register()
 	{
 		$this->container->instance('\\Bobby\\Contract\\Event\\Handle', new \Bobby\Component\Event\Handle($this->container));
 	}

 	public function boot()
 	{
 		if($this->listen) {
 			$eventHandle = $this->container->make('\\Bobby\\Contract\\Event\\Handle');
 			foreach ($this->listen as $event => $listener) {
 				$eventHandle->listen($event, $listener);
 			}
 		}
 	}

 }