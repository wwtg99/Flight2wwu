<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/21
 * Time: 16:37
 */

namespace Flight2wwu\Component\File;

interface Downloadable
{

    /**
     * @param string $filename
     * @return void
     */
    public function download($filename = '');
} 