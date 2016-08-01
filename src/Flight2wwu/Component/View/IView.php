<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/1
 * Time: 11:24
 */

namespace Wwtg99\Flight2wwu\Component\View;


interface IView
{

    /**
     * @param $template
     * @param array $data
     * @return mixed
     */
    public function render($template, array $data = null);
}