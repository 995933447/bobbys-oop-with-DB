<?php
namespace Bobby\Contract\Config;

interface Config
{
    /**
     * [get 获取配置]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
	public function get($key = null, $defaultValue = null);

    /**
     * [set 设置配置]
     * @param [type] $key   [description]
     * @param [type] $value [description]
     */
	public function set($key, $value);

}