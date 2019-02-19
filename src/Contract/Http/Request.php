<?php
namespace Bobby\Contract\Http;

/**
 * http请求接口类
 */
interface Request
{
    /**
     * [get 获取GET数据]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function get($name = null, $defaultValue = null);

    /**
     * [post 获取POST数据]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function post($name = null, $defaultValue = null);

    /**
     * [files 获取$_FILES参数]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function files($name = null, $defaultValue = null);

    /**
     * [coockie 获取cookie数据]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function coockie($name = null, $defaultValue = null);

    /**
     * [server 获取环境变量$_SERVER数据]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function server($name = null, $defaultValue = null);

    /**
     * [getPHPInput 获取原始的POST包体]
     * @param  [type] $paramterName [description]
     * @param  [type] $defaultValue [description]
     * @return [type]               [description]
     */
    public function getPHPInput($paramterName = null, $defaultValue = null);

    /**
     * [http 获得所有http请求参数]
     * @return [type] [description]
     */
    public function http();

    /**
     * [header 获得请求头]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function header($name = null);

    /**
     * [uri 获得当前请求URI，不包括GET参数]
     * @return [type] [description]
     */
    public function uri();

    /**
     * [uriWithQuery 获得当前请求URI，包括GET参数]
     * @return [type] [description]
     */
    public function uriWithQuery();

    /**
     * [method 获得当前请求方法]
     * @return [type] [description]
     */
    public function method();

    /**
     * [time 获得当前请求时间]
     * @return [type] [description]
     */
    public function time();

    /**
     * [setGlobalFilter 设置全局的过滤方法]
     * @param array $callbacks [description]
     */
    public function setGlobalFilter(array $callbacks);

    /**
     * [filter 返回并过滤相应数据]
     * @param  string     $name      [description]
     * @param  array|null $callbacks [description]
     * @param  boolean    $append    [description]
     * @return [type]                [description]
     */
    public function filter(string $name, array $callbacks = null, $append = true);

}