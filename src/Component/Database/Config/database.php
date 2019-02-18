<?php
/**
 * @Author: Bobby
 * @Date:   2019-01-18 14:59:50
 * @Last Modified by:   Bobby
 * @Last Modified time: 2019-01-18 15:24:17
 */
return [
    //默认使用的数据库连接
    'default' => 'mysql',

    //数据库连接列表
    'connections' => [

        'mysql' => [

            'driver' => 'mysql',
            'host' => '127.0.0.1:3306',
            'user' => 'root',
            'password' => '',
            'database' => 'test',
            'charset' => 'utf8mb4',
            'prefix' => '',
            'timeout' => 1,
            'error_mode' => 2, //0 静默模式,默认的出错了不管;1 警告模式,如果出错了就会报出警告;2 异常模式,如果出错会采用异常来处理（PDOException） 
            'pconnect' => true, //是否开启长连接

            // 'read' => [
            //     [
            //         'host' => '127.0.0.2',
            //     ],
            //     [
            //         'host' => '127.0.0.3',
            //     ],
            // ],

            // 'write' => [

            // ]

        ]

    ]

];