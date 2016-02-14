<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/24
 * Time: 14:04
 */

namespace Flight2wwu\Component\View\Html;


abstract class StyledComponent extends HtmlComponent
{
    /**
     * [key => ['name' => 'style', ...], ...]
     *
     * @var array
     */
    protected $styles = [];

    /**
     * [key => 'class', ...] or [key => ['class', ...], ...]
     *
     * @var array
     */
    protected $cssClass = [];

    /**
     * @param array|string $name
     * @return string
     */
    protected function getStyle($name)
    {
        if (is_array($name)) {
            $styles = [];
            foreach ($name as $n) {
                if (array_key_exists($n, $this->styles)) {
                    $styles = array_merge($styles, $this->styles[$n]);
                }
            }
            if ($styles) {
                return $this->formatStyle($styles);
            }
        } else {
            if (array_key_exists($name, $this->styles)) {
                if ($this->styles[$name]) {
                    return $this->formatStyle($this->styles[$name]);
                }
            }
        }
        return '';
    }

    /**
     * @param array $styles
     * @return string
     */
    protected function formatStyle(array $styles)
    {
        if (!$styles) {
            return '';
        }
        $s = [];
        foreach ($styles as $k => $v) {
            array_push($s, "$k: $v");
        }
        return implode(';', $s);
    }
} 