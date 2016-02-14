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
];