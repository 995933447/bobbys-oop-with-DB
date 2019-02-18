<?php
namespace Bobby\Component\Http\Request\Instance;

use Bobby\{
    Contract\Http\Request as RequestContract,
    Component\Http\Request\Instance\InstanceTrait
};


/**
 * 一般模式(cli or fpm or apache etc)http请求封装组件
 */
class Normal implements RequestContract
{

    use InstanceTrait;

    private function setParamters($paramterName)
    {

        if(!$this->$paramterName) {

            switch ($paramterName) {
                case 'server':
                    $paramters = $_SERVER;
                    if('cli-server' === PHP_SAPI) {
                        if(isset($_SERVER['HTTP_CONTENT_LENGTH'])) {
                            $_SERVER['CONTENT_LENGTH'] = $_SERVER['HTTP_CONTENT_LENGTH'];
                            unset($_SERVER['HTTP_CONTENT_LENGTH']);
                        }
                        if(isset($_SERVER['HTTP_CONTENT_TYPE'])) {
                            $_SERVER['CONTENT_TYPE'] = $_SERVER['HTTP_CONTENT_TYPE'];
                            unset($_SERVER['HTTP_CONTENT_TYPE']);
                        }
                    }
                    break;
                case 'get':
                    $paramters = $_GET;
                    break;
                case 'post':
                    $paramters = $_POST;
                    break;
                case 'files':
                    $paramters = $_FILES;
                    break;
                case 'coockie':
                    $paramters = $_COOKIE;
            }

            array_walk($paramters, function($value, $key) use($paramterName) {
                if($paramterName === 'server') $key = strtolower($key);
                $this->$paramterName[$key] = $value;
            });

        }

    }

    public function getPHPInput($paramterName = null, $defaultValue = null)
    {
        if(!$this->PHPInput) $this->PHPInput = file_get_contents('php://input');

        if(!$paramterName) return $this->PHPInput;
        return $this->PHPInput[$paramterName]?? $defaultValue;
    }

    private function setAllHttpData()
    {

        throw new \Exception("This method only just support swoole extension mode", 500);

    }

}