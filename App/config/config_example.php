<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 16:00
 */

//Config file example

return [
    //Application config
    'app'=>'Flight2wwu',
    'version'=>'0.1.0',
    'author'=>'wwu',
    'description'=>'',
    //Timezone and language
    'timezone'=>'Asia/Shanghai',
    'language'=>'zh_CN',
    //Debug
    'debug'=>1,
    //Config files
    'config'=>[
        'prefix'=>CONFIG,  // relative path for config files
        'register_loader'=>'register_loader.php',  // register class
        'class_register'=>'class_register.php',  // class register in Flight
        'route_controller_register'=>'route_controller_register.php',  // route controller register file
        'route_register'=>'route_register.php',  // route register file
        'route'=>'route.php',  // route definition file
        'handler'=>'handlers.php', //other handler functions
    ],
    //Plugin
    'plugin'=>[
        'config'=>CONFIG . 'plugins.json'
    ],
    //Schedule
    'schedule'=>[
        'support'=>'crontab',  // support program, only support for crontab now
        'time'=>'0 0 */1 * *',  // interval time
        'config'=>CONFIG . 'schedule.json',  // schedule config file
        'engine'=>'schedule_engine.php'  // run script in bin
    ],
    //Log
    'log'=>[
        'main'=>[
            'title'=>'main.log',
            'max_logfile'=>10,
            'level'=>'debug'
        ],
        'access'=>[
            'title'=>'access.log',
            'max_logfile'=>30,
            'level'=>'info'
        ],
        'database'=>[
            'title'=>'database.log',
            'max_logfile'=>5,
            'level'=>'debug'
        ]
    ],
    //Database
    'database'=>[
        'main'=>[
            'driver'=>'pgsql',
            'host'=>'localhost',
            'dbname'=>'db',
            'user'=>'user',
            'password'=>'1',
            'port'=>5432
        ]
    ],
    //View
    'assets'=>[
        'lib_conf'=>CONFIG . 'ui_libs.php'
    ],
    //Auth
    'auth'=>[
        'rbac'=>CONFIG . 'rbac.php', //role based access control config file
        'session'=>true, //use session to store user info
        'cookie'=>true, //use cookie to store token
    ],
    //Storage
    'storage'=>[
        'cache'=>[
            'adapter'=>'File', //Adapter for Desarrolla2\Cache: File, Apcu
            'params'=>[
                'cacheDir'=>TMP . 'cache',
                'ttl'=>3600
            ]
        ],
        'session'=>true, //enable session
        'cookie'=>true, //enable cookie
        'old_value'=>'cache', //storage method for old_value: cache, session
    ],
    //Mail
    'mail'=>[
        'method'=>'mail', //method to send mail: mail, sendmail, smtp
        'params'=>[
            'command'=>'/usr/sbin/sendmail -bs', //used for sendmail
            'host'=>'localhost', //host for smtp
            'port'=>25, //port for smtp
            'security'=>null, //security for smtp
            'username'=>'', //username for smtp
            'password'=>'', //password for smtp
        ],
    ],
    //Express
    'express'=>[
        //[name, label, request url (<no> will be replaced), format function name]
        ['yto', '圆通', 'http://www.kiees.cn/yto.php?wen=<no>&action=ajax', 'extractKieesTable'],
        ['sto', '申通', 'http://www.kiees.cn/sto.php?wen=<no>&ajax=1', 'extractKieesTable'],
        ['ems', 'EMS', 'http://www.kiees.cn/ems.php?wen=<no>&action=ajax', 'extractKieesTable'],
        ['sf', '顺风', 'http://www.kiees.cn/sf.php?wen=<no>&action=ajax', 'extractKieesTable'],
        ['zto', '中通', 'http://www.kiees.cn/zto.php?wen=<no>&action=ajax', 'extractKieesTable'],
        ['yd', '韵达', 'http://www.kiees.cn/yd.php?wen=<no>&channel', 'extractKieesTable'],
    ],
];