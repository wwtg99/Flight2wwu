<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2015/12/20 0020
 * Time: 下午 2:14
 */

namespace Flight2wwu\Component\View\Html;


class InputComp extends HtmlComponent
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $label = '';

    /**
     * @var array
     */
    private $cssClass = [];

    /**
     * @var array
     */
    private $attrs = [];

    /**
     * @param string $id
     * @param string $type
     * @param string $label
     * @param string|array $cssClass
     * @param array $attrs
     */
    function __construct($id, $type = 'text', $label = '', $cssClass = null, array $attrs = [])
    {
        $this->id = $id;
        $this->type = $type;
        $this->label = $label;
        if ($cssClass) {
            if (is_array($cssClass)) {
                $this->cssClass = $cssClass;
            } else {
                $this->cssClass = [$cssClass];
            }
        }
        if ($attrs) {
            $this->attrs = $attrs;
        }
    }

    /**
     * @param array $data
     * @return string
     */
    public function render(array $data = [])
    {
        switch ($this->type) {
            case 'button':
            case 'submit':
            case 'reset':
                return $this->renderButton();
            case 'radio':
            case 'checkbox':
                return $this->renderAfterText();
            default:
                return $this->renderBeforeText();
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->type;
    }

    /**
     * @return array|string
     */
    public function getLibrary()
    {
        return '';
    }

    /**
     * @param array|string $css
     */
    public function addCssClass($css)
    {
        if (is_array($css)) {
            foreach ($css as $c) {
                $this->addCssClass($c);
            }
        } else {
            if (!in_array($css, $this->cssClass)) {
                array_push($this->cssClass, $css);
            }
        }
    }

    /**
     * @param array $attrs
     */
    public function addAttrs(array $attrs)
    {
        if (is_array($attrs))
        {
            foreach ($attrs as $k => $v) {
                $this->attrs[$k] = $v;
            }
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return array
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    /**
     * @param array $cssClass
     */
    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;
    }

    /**
     * @return array
     */
    public function getAttrs()
    {
        return $this->attrs;
    }

    /**
     * @param array $attrs
     */
    public function setAttrs($attrs)
    {
        $this->attrs = $attrs;
    }

    /**
     * @return string
     */
    private function renderAttrs()
    {
        $prop = [];
        foreach ($this->attrs as $a => $v) {
            $prop[$a] = $v;
        }
        $prop['id'] = $this->id;
        $prop['type'] = $this->type;
        $attr = [];
        foreach ($prop as $k => $v) {
            array_push($attr, "$k='$v'");
        }
        return implode(' ', $attr);
    }

    /**
     * @return string
     */
    private function renderClass()
    {
        if (!$this->cssClass) {
            return '';
        }
        $css = implode(' ', $this->cssClass);
        return "class='$css'";
    }

    /**
     * @return string
     */
    private function renderButton()
    {
        $attr = $this->renderAttrs();
        $css = $this->renderClass();
        return "<button $css $attr>$this->label</button>";
    }

    /**
     * @return string
     */
    private function renderBeforeText()
    {
        $attr = $this->renderAttrs();
        $css = $this->renderClass();
        if ($this->label) {
            return "<label for='$this->id'>$this->label <input $attr $css /></label>";
        } else {
            return "<input $attr $css />";
        }
    }

    /**
     * @return string
     */
    private function renderAfterText()
    {
        $attr = $this->renderAttrs();
        $css = $this->renderClass();
        if ($this->label) {
            return "<label for='$this->id'><input $attr $css /> $this->label</label>";
        } else {
            return "<input $attr $css />";
        }
    }

}