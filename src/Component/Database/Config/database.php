<?php
/**
 * @Author: Bobby
 * @Date:   2019-01-18 14:59:50
 * @Last Modified by:   Bobby
 * @Last Modified time: 2019-01-18 15:24:17
 * @Decribe 示例配置文件
 */

return [
    
    'default' => 'mysql', //默认使用的数据库连接

    //数据库连接列表
    'connections' => [

        'mysql' => [

            'driver' => 'mysql',        //数据库驱动
            'host' => '127.0.0.1:3306', //连接主机
            'user' => 'root',           //用户名
            'password' => '',           //密码
            'database' => 'test',       //数据库
            'charset' => 'utf8mb4',     //字符集
            'prefix' => '',             //表前缀
            'timeout' => 1,             //连接超时时间，false表示无限制
            'error_mode' => 2,          //0 静默模式,默认的出错了不管;1 警告模式,如果出错了就会报出警告;2 异常模式,如果出错会采用异常来处理（PDOException） 
            'pconnect' => true,         //是否开启长连接

            //读写分离配置
            // 'read' => [

            //     [
            //         'host' => '127.0.0.2',
            //     ],
            //     [
            //         'host' => '127.0.0.3',
            //     ]

            // ],

            // 'write' => [

            // ]

        ]

    ]

];