<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/23
 * Time: 16:20
 */

namespace Flight2wwu\Component\View\Html;


class StepComp extends StyledComponent
{
    /**
     * @var int
     */
    private $current;

    /**
     * @var array
     */
    private $steps;

    /**
     * @param array $data
     * @return string
     */
    public function render(array $data = [])
    {
        $this->current = array_key_exists('current', $data) ? $data['current'] : 0;
        $style = array_key_exists('style', $data) ? $data['style'] : 1;
        switch ($style) {
            case 1: $html = $this->renderSquare();break;
            case 2: $html = $this->renderSemantic(); break;
            default: $html = $this->renderSquare(); break;
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->current;
    }

    /**
     * @return array|string
     */
    public function getLibrary()
    {
        return 'semantic';
    }

    /**
     * @param array $steps: format [['title'=>'', 'descr'=>''], ...], field descr is optional
     * @param int $current
     * @param array $css: provide css class ['step_div'=>'', 'step'=>'', 'content'=>'', 'title'=>'', 'descr'=>'', 'current'=>'', 'completed'=>'']
     */
    function __construct(array $steps, $current = 0, array $css = [])
    {
        $this->steps = $steps;
        $this->current = $current;
        $this->cssClass = ['step_div' => 'ui ordered steps', 'step' => 'step', 'content'=>'content', 'title' =>'title', 'descr' =>'descr', 'current' => 'active', 'completed'=>'completed'];
        $this->styles = [
            'step_div'=>['margin'=>'15px 0 0 0'],
            'step'=>['float'=>'left', 'width'=>'278px', 'height'=>'80px', 'margin'=>'0 10px 0 10px', 'padding'=>'10px', 'border'=>'solid 1px rgba(34, 36, 38, 0.15)', 'border-radius'=>'0.28rem'],
            'content'=>['display'=>'block'],
            'title'=>['font-weight'=>'700', 'font-size'=>'1.14em'],
            'descr'=>['font-weight'=>'400'],
            'current'=>['background'=>'#F3F4F5'],
            'completed'=>['color'=>'rgba(40, 40, 40, 0.3)']
        ];
        foreach ($css as $k => $v) {
            if (array_key_exists($k, $this->cssClass)) {
                $this->cssClass[$k] = $v;
                $this->styles[$k] = '';
            }
        }
    }

    /**
     * @return string
     */
    private function renderSemantic()
    {
        $css_step_div = $this->cssClass['step_div'];
        $style_step_div = $this->getStyle('step_div');
        $css_content = $this->cssClass['content'];
        $style_content = $this->getStyle('content');
        $css_title = $this->cssClass['title'];
        $style_title = $this->getStyle('title');
        $css_descr = $this->cssClass['descr'];
        $style_descr = $this->getStyle('descr');
        $html = new HtmlTag('div', '', ['class'=>$css_step_div, 'style'=>$style_step_div]);
        $i = 1;
        foreach ($this->steps as $step) {
            $css_step = $this->cssClass['step'];
            $style_step = $this->getStyle('step');
            $title = TI(array_key_exists('title', $step) ? $step['title'] : '');
            $descr = TI(array_key_exists('descr', $step) ? $step['descr'] : '');
            if ($i == $this->current) {
                $css_step = [$css_step, $this->cssClass['current']];
                $style_step = $this->getStyle(['step', 'current']);
            } elseif ($i < $this->current) {
                $css_step = [$css_step, $this->cssClass['completed']];
                $style_step = $this->getStyle(['step', 'completed']);
            }
            $sdiv = new HtmlTag('div', '', ['class'=>$css_step, 'style'=>$style_step]);
            $cdiv = new HtmlTag('div', '', ['class'=>$css_content, 'style'=>$style_content]);
            $tdiv = new HtmlTag('div', $title, ['class'=>$css_title, 'style'=>$style_title]);
            $ddiv = new HtmlTag('div', $descr, ['class'=>$css_descr, 'style'=>$style_descr]);
            $cdiv->addChild($tdiv)->addChild($ddiv);
            $sdiv->addChild($cdiv);
            $html->addChild($sdiv);
            $i++;
        }
        return $html->render();
    }


    /**
     * @return string
     */
    private function renderSquare()
    {
        $css_step_div = $this->cssClass['step_div'];
        $style_step_div = $this->getStyle('step_div');
        $css_content = $this->cssClass['content'];
        $style_content = $this->getStyle('content');
        $css_title = $this->cssClass['title'];
        $style_title = $this->getStyle('title');
        $css_descr = $this->cssClass['descr'];
        $style_descr = $this->getStyle('descr');
        $html = new HtmlTag('div', '', ['style'=>$style_step_div]);
        $i = 1;
        foreach ($this->steps as $step) {
            $css_step = $this->cssClass['step'];
            $style_step = $this->getStyle('step');
            $title = TI(array_key_exists('title', $step) ? $step['title'] : '');
            $descr = TI(array_key_exists('descr', $step) ? $step['descr'] : '');
            if ($i == $this->current) {
                $css_step = [$css_step, $this->cssClass['current']];
                $style_step = $this->getStyle(['step', 'current']);
            } elseif ($i < $this->current) {
                $css_step = [$css_step, $this->cssClass['completed']];
                $style_step = $this->getStyle(['step', 'completed']);
            }
            $sdiv = new HtmlTag('div', '', ['style'=>$style_step]);
            $cdiv = new HtmlTag('div', '', ['style'=>$style_content]);
            $tdiv = new HtmlTag('div', $title, ['style'=>$style_title]);
            $ddiv = new HtmlTag('div', $descr, ['style'=>$style_descr]);
            $cdiv->addChild($tdiv)->addChild($ddiv);
            $sdiv->addChild($cdiv);
            $html->addChild($sdiv);
            $i++;
        }
        return $html->render();
    }
} 