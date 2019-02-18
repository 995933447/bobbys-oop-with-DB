<?php
 namespace Bobby\Component\Bootstrap;

 use Bobby\Contract\AppEngine\Engine;

 class RegisterProviderHandle
 {

 	private $providers;

 	private $app;

 	private $cacheFile;

 	public function boot(Engine $app)
 	{
 		$this->app = $app;
 		$this->cacheFile = $this->app->config('app.providers_cache.file');

 		if($this->app->config('app.providers_cache.is_open') && file_exists($this->cacheFile)) {

 			$this->providers = require $this->cacheFile;

 		} else {

 			$providers = $this->app->config('app.providers', []);
 			$cache = file_exists($this->cacheFile) ? require $this->cacheFile : null;
 			if($this->shouldCompile($providers, $cache)) {
 				$this->compile($providers);
 			} else {
 				$this->providers = $cache;
 			}

 		}

 		foreach($this->providers['eager'] as $provider) {
 			$this->app->register($provider);
 		}

 		if(isset($this->providers['events'])) {
 			foreach($this->providers['events'] as $provider => $event) {
 				$this->app->make('Event')->listen($event, function() use($provider) {
 					$this->app->register($provider);
 				});
 			}
 		}

 		$this->app->defferRegister($this->providers['deffer']);

 	}

 	private function shouldCompile($providers, $cache)
 	{
 		if(is_null($cache) || $providers != $cache['providers']) {
 			return true;
 		}
 		return false;
 	}

 	private function compile($providers)
 	{
 		$cache = [
 			'providers' => $providers,
 			'eager'	=> [],
 			'deffer' => []
 		];

 		foreach ($providers as $provider) {
 			$instance = new $provider($this->app);
 			if($instance->isDeffer && $instance->provide) {
 				foreach ($instance->provide as $service) {
 					$cache['deffer'][$service] = $provider;
 				}
 				if($instance->events) $cache['events'][$provider] = $instance->events;
 			} else {
 				$cache['eager'][] = $provider;
 			}
 		}
 		$this->providers = $cache;

 		$this->cacheFile($cache);
 	}

 	private function cacheFile($cache)
 	{
 		$cacheDir = dirname($this->cacheFile);
 		is_dir($cacheDir) || mkdir($cacheDir, 0777, true);
 		$cache = var_export($cache, true);
 		$cache = <<<STR
<?php
	return {$cache};
STR;
 		file_put_contents($this->cacheFile, $cache);
 	}



 }