<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/8
 * Time: 11:01
 */

/**
 * Register asserts (js, css...) here.
 *
 * Format:
 * "name"=>["depends"=>["depends_lib_name"], "prefix"=>"", "css"=>["1.css", "2.css"], "js"=>["1.js", "2.js"]]
 *
 * or with attributes
 *
 * "name"=>["depends"=>[], "prefix"=>"", "css"=>[], "js"=>["1.js", ["file"=>"2.js", "attr"=>["async"=>true]]]]
 *
 * Use Flight()::Assets()->addLibrary($name) to add library.
 * Use Flight()::Assets()->getResource($name, $prefix) to get resource uri
 *
 * Libraries specified in global_before will always to loaded before user added.
 * Libraries specified in global_after will always to loaded after user added.
 * Path specified in resource_dir will be used for default prefix in getResource().
 */

return [
    'assets'=>[
        'resource_dir'=>'/assets/images',
        'global_before'=>['bootstrap'],
        'global_after'=>['custom'],
        'libs'=>[
            'custom'=>[
                'depends'=>[],
                'css'=>['common.css'],
                'js'=>['utils.js'],
                'prefix'=>'/assets/custom'
            ],
            'jquery'=>[
                'js'=>['jquery-1.12.3.min.js'],
                'prefix'=>'/assets/jquery'
            ],
//        'uploader'=>[
//            'js'=>['dmuploader.min.js'],
//            'prefix'=>'/asserts/uploader'
//        ],
//        'underscore'=>[
//            'js'=>['underscore-min.js'],
//            'prefix'=>'/asserts/underscore'
//        ],
//        'semantic'=>[
//            'css'=>'semantic.min.css',
//            'js'=>'semantic.min.js',
//            'prefix'=>'/asserts/semantic'
//        ],
            'bootstrap'=>[
                'depends'=>['jquery'],
                'css'=>['bootstrap.min.css'],
                'js'=>['bootstrap.min.js'],
                'prefix'=>'/assets/bootstrap'
            ],
            'bootstrap-table'=>[
                'depends'=>['bootstrap'],
                'css'=>['bootstrap-table.min.css'],
                'js'=>['bootstrap-table.min.js', 'bootstrap-table-zh-CN.min.js', 'table_utils.js'],
                'prefix'=>'/assets/bootstrap-table'
            ],
            'bootstrap-dialog'=>[
                'depends'=>['bootstrap'],
                'css'=>['bootstrap-dialog.css'],
                'js'=>['bootstrap-dialog.js'],
                'prefix'=>'/assets/bootstrap-dialog'
            ],
//        'bootstrap-select'=>[
//            'css'=>'bootstrap-select.min.css',
//            'js'=>'bootstrap-select.min.js',
//            'prefix'=>'/asserts/bootstrap-select'
//        ],
//        'bootstrap-switch'=>[
//            'css'=>'bootstrap-switch.min.css',
//            'js'=>'bootstrap-switch.min.js',
//            'prefix'=>'/asserts/bootstrap-switch'
//        ],
//        'colors'=>[
//            'css'=>'colors.min.css',
//            'prefix'=>'/asserts/colors'
//        ],
//        'fa'=>[
//            'css'=>'font-awesome.min.css',
//            'prefix'=>'/asserts/fa'
//        ],
//        'lodash'=>[
//            'js'=>'lodash.min.js',
//            'prefix'=>'/asserts/lodash'
//        ],
//        'icheck'=>[
//            'css'=>'skins/all.css',
//            'js'=>'icheck.min.js',
//            'prefix'=>'/asserts/icheck'
//        ],
//        'buttons'=>[
//            'css'=>'buttons.min.css',
//            'js'=>'button.js',
//            'prefix'=>'/asserts/buttons'
//        ],
//        'qtip'=>[
//            'css'=>'jquery.qtip.min.css',
//            'js'=>'jquery.qtip.min.js',
//            'prefix'=>'/asserts/qtip'
//        ],
//        'peity'=>[
//            'js'=>'jquery.peity.min.js',
//            'prefix'=>'/asserts/peity'
//        ],
//        'echarts'=>[
//            'js'=>'echarts.min.js',
//            'prefix'=>'/asserts/echarts'
//        ],
        ]
    ]
];