<?php
/**
 * @Author: Bobby
 * @Date:   2019-01-18 10:59:39
 * @Last Modified by:   Bobby
 * @Last Modified time: 2019-01-22 13:38:43
 */
namespace  Bobby\Contract\AppEngine;

use Bobby\Contract\Container\Container;

interface Engine extends Container
{
    /**
     * [defferRegister 延迟注册的服务]
     * @param  array  $registerar [description]
     * @return [type]             [description]
     */
    public function defferRegister(array $registerar);

    /**
     * [bootstrap 框架启动]
     * @return [type] [description]
     */
    public function boot();

    /**
     * [config 获取配置项]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
     public function config($key = null);

    /**
     * [register 从服务提供器中注册服务到应用中]
     * @param  [type] $provider [description]
     * @return [type]           [description]
     */
    public function register($provider);

    /**
     * [isBooted 检测应用框架引擎是否已启动完成]
     * @return boolean [description]
     */
    public function isBooted();

    /**
     * [completeBoot 标识框架启动完成]
     */
    public function completeBoot();

}