<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2015/12/17 0017
 * Time: 下午 9:41
 */

namespace Flight2wwu\Component\View\Html;


abstract class HtmlComponent
{

    /**
     * @param array $data
     * @return string
     */
    abstract public function render(array $data = []);

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @return array|string
     */
    abstract public function getLibrary();
}