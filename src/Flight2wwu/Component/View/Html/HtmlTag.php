<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/24
 * Time: 14:57
 */

namespace Flight2wwu\Component\View\Html;


class HtmlTag
{
    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $text = '';

    /**
     * @var array
     */
    private $attrs = [];

    /**
     * @var array
     */
    private $children = [];

    /**
     * @var bool
     */
    private $noText = false;

    /**
     * @param string $tag
     * @param string $text
     * @param array $attrs
     * @param bool $noText
     */
    function __construct($tag, $text = '', $attrs = [], $noText = false)
    {
        $this->tag = $tag;
        $this->text = $text;
        foreach ($attrs as $k => $v) {
            $this->addAttr($k, $v);
        }
        $this->noText = $noText;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param HtmlTag $child
     * @return $this
     */
    public function addChild($child)
    {
        array_push($this->children, $child);
        return $this;
    }

    /**
     * @return array
     */
    public function getAttrs()
    {
        return $this->attrs;
    }

    /**
     * @return boolean
     */
    public function isNoText()
    {
        return $this->noText;
    }

    /**
     * @param boolean $noText
     */
    public function setNoText($noText)
    {
        $this->noText = $noText;
    }

    /**
     * @param $attr
     * @param $val
     */
    public function addAttr($attr, $val)
    {
        if ($attr == 'class') {
            $this->addClass($val);
        } else {
            $this->attrs[$attr] = $val;
        }
    }

    /**
     * @param $attr
     * @return string
     */
    public function getAttr($attr)
    {
        if (array_key_exists($attr, $this->attrs)) {
            return $this->attrs[$attr];
        }
        return '';
    }

    /**
     * @param array|string $css
     */
    public function addClass($css)
    {
        if (is_array($css)) {
            foreach ($css as $c) {
                $this->addClass($c);
            }
        } else {
            if (array_key_exists('class', $this->attrs)) {
                if (!in_array($css, $this->attrs['class'])) {
                    array_push($this->attrs['class'], $css);
                }
            } else {
                $this->attrs['class'] = [$css];
            }
        }
    }

    /**
     * @param array|string $css
     */
    public function removeClass($css)
    {
        if (is_array($css)) {
            foreach ($css as $c) {
                $this->removeClass($c);
            }
        } else {
            if (array_key_exists('class', $this->attrs)) {
                $index = array_search($css, $this->attrs['class']);
                if ($index !== false) {
                    array_splice($this->attrs['class'], $index, 1);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function render()
    {
        if ($this->noText) {
            $attrs = $this->renderAttrs();
            return "<$this->tag${attrs} />";
        } else {
            $attrs = $this->renderAttrs();
            $html = "<$this->tag${attrs}>$this->text";
            foreach ($this->children as $c) {
                if ($c instanceof HtmlTag) {
                    $html .= $c->render();
                }
            }
            $html .= "</$this->tag>";
            return $html;
        }
    }

    /**
     * @return string
     */
    private function renderAttrs()
    {
        $attr = [];
        foreach ($this->attrs as $k => $v) {
            if ($k == 'class') {
                $css = implode(' ', $v);
                if (!is_null($css) && $v != '') {
                    array_push($attr, "class='$css'");
                }
            } else {
                if (!is_null($v)) {
                    array_push($attr, "$k='$v'");
                }
            }
        }
        $a = implode(' ', $attr);
        if ($a) {
            return " $a";
        }
        return '';
    }

} 