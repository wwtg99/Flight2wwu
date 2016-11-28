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
    //Timezone
    'timezone'=>'Asia/Shanghai',
    //Debug
    'debug'=>1,
    //Maintenance
    'maintain'=>0,
    //Domain
    'domain'=>'http://domain.com',
    //Base url
    'base_url'=>'/',
    //Log
    'log'=>[
        'directory'=>STORAGE . 'log',
        'loggers'=>[
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
        ]
    ],
    //Database
    'database'=>[
        'database_type'=>'pgsql',
        'server'=>'localhost',
        'database_name'=>'db',
        'username'=>'user',
        'password'=>'1',
        'port'=>5432
    ],
    //Redis
    'redis'=>[
        'scheme' => 'tcp',
        'host'   => '192.168.0.21',
        'port'   => 6379,
    ],
    //DataPool
    'datapool'=>[
        'log_dir'=>'storage/log',
        'connections'=>[
            [
                'name'=>'dbconn',
                'class'=>'Wwtg99\\DataPool\\Connections\\DatabaseConnection',
                'mapper_path'=>'',
                'database'=>[
                    'driver'=>'pgsql',
                    'dbname'=>'db',
                    'host'=>'localhost',
                    'username'=>'user',
                    'password'=>'1',
                    'port'=>5432
                ],
                'logger'=>[
                    'level'=>'DEBUG',
                    'title'=>'dbconn.log',
                    'max_logfile'=>5
                ]
            ],
            [
                'name'=>'auth',
                'class'=>'Wwtg99\\DataPool\\Connections\\DatabaseConnection',
                'mapper_path'=>'Wwtg99\\PgAuth\\Mapper',
                'database'=>[
                    'driver'=>'pgsql',
                    'dbname'=>'auth_test',
                    'host'=>'192.168.0.21',
                    'username'=>'genobase',
                    'password'=>'genobase',
                    'port'=>5432
                ],
                'logger'=>[
                    'level'=>'DEBUG',
                    'title'=>'database.log',
                    'max_logfile'=>5
                ]
            ],
        ]
    ],
    //View
    'view'=>[
        'view_dir'=>APP . 'view',
    ],
    //Locale
    'locale'=>[
        'language'=>'zh_CN',
        'directory'=>CONFIG . 'lang'
    ],
    //Storage
    'storage'=>[
        'cache'=>[
            'adapter'=>'File', //Adapter for Desarrolla2\Cache: File, Apcu
            'params'=>[
                'cache_dir'=>TMP . 'cache',
                'ttl'=>3600
            ]
        ],
        'prefix'=>'fwwu', //prefix for session and cookie, auto add _ after prefix
        'session'=>true, //enable session
        'cookie'=>true, //enable cookie
        'cookie_path'=>'/', // path for cookies
        'cookie_domain'=>null, //domain for cookies
        'old_value'=>'session', //storage method for old_value: cache, session
    ],
];