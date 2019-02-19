<?php
 namespace Bobby\Contract\Event;

 interface Handle
 {

 	/**
 	 * [listen 注册监听事件，两种方式:1.事件类一对多监听器类的数组;2.事件名称字符串绑定监听器闭包，可多次绑定]
 	 * @param  [type] $event    [description]
 	 * @param  [type] $listener [description]
 	 * @return [type]           [description]
 	 */
 	public function listen($event, $listener);

 	/**
 	 * [listen 解析事件]
 	 * @param  [type] $event    [description]
 	 * @param  [type] $payload  [description]
 	 * @param  [type] $halt  	[description]
 	 * @return [type]           [description]
 	 */
 	public function fire($event, array $payload = [], $halt = false);

 }