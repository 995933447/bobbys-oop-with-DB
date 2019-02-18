<?php
/**
 * @Author: Bobby
 * @Date:   2019-01-24 10:56:37
 * @Last Modified by:   Bobby
 * @Last Modified time: 2019-01-24 11:19:20
 */
namespace Bobby\Component\Http\Request;

class InstanceFactory
{
    public static function make($mode = null, $request = null)
    {
        if(!$mode) return new \Bobby\Component\Http\Request\Instance\Normal;

        switch ($mode) {
            case 'swoole':
                    return new Bobby\Component\Http\Request\Instance\Swoole($request);
                break;

            default:
               throw new InvalidArgumentException("Unsupport Request instance mode:{$mode}.");
        }
    }
}