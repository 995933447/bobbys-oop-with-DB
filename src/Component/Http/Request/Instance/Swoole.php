<?php
namespace Bobby\Component\Http\Request\Instance;

use Bobby\{
    Contract\Http\Request as RequestContract,
    Component\Http\Request\Instance\InstanceTrait
};

/**
 * http(swoole模式)请求封装组件
 */
class Swoole implements RequestContract
{

    use InstanceTrait;

    private function setParamters($paramterName)
    {

        if(!$this->$paramterName) {

            $this->$paramterName = $this->request->$paramterName;
            if($paramterName === 'server') {
                foreach ($_SERVER as $key => $value) {
                    $_SERVER[strtolower($key)] = $value;
                }
                $this->paramterName = array_merge($_SERVER, $this->$paramterName);
            }

        }

    }

    public function getPHPInput($paramterName = null, $defaultValue = null)
    {
        if(!$this->PHPInput) $this->PHPInput = $this->request->rawContent();

        if(!$paramterName) return $this->PHPInput;
        return $this->PHPInput[$paramterName]?? $defaultValue;
    }

    private function setAllHttpData()
    {
        if(!$this->allData) {

            $httpData = $this->request->getData();
            $this->allHttpData = explode("\r\n", $httpData);


            foreach ($this->allHttpData as $value) {

                if($value !== "") {
                    if(strpos($value, ': ')) {
                        list($header, $item) = explode(': ', $value, 2);
                        $this->header[$header] = $item;
                    }
                } else {
                    break;
                }

            }

        }
    }


}