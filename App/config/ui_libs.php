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
                'js'=>['jquery-2.2.3.min.js'],
                'prefix'=>'/assets/jquery'
            ],
            'bootstrap'=>[
                'depends'=>['jquery'],
                'css'=>['bootstrap.min.css'],
                'js'=>['bootstrap.min.js'],
                'prefix'=>'/assets/bootstrap'
            ],
            'fa'=>[
                'css'=>['font-awesome.min.css'],
                'prefix'=>'/assets/fa'
            ],
        ]
    ]
];