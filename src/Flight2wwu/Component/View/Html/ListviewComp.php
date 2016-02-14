<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/24
 * Time: 13:52
 */

namespace Flight2wwu\Component\View\Html;


class ListviewComp extends StyledComponent
{
    /**
     * @var int
     */
    private $column;

    /**
     * @var string
     */
    private $align;

    /**
     * @param array $data
     * @return string
     */
    public function render(array $data = [])
    {
        $style = array_key_exists('style', $data) ? $data['style'] : 1;
        $ldata = array_key_exists('data', $data) ? $data['data'] : [];
        switch ($style) {
            case 1: $html = $this->renderColumn($ldata);break;
            default: $html = $this->renderColumn($ldata); break;
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'listview';
    }

    /**
     * @return array|string
     */
    public function getLibrary()
    {
        return '';
    }

    /**
     * @param int $column
     * @param string $align
     * @param array $css
     */
    function __construct($column = 1, $align = 'left', array $css = [])
    {
        $this->column = $column;
        $this->align = $align;
        $this->cssClass = ['list_div' => 'list_div', 'list_item' => 'list_item', 'list_label' =>'list_label', 'list_text' =>'list_text'];
        $this->styles = [
            'list_div' => ['width' => '100%'],
            'list_item' => ['float' => 'left', 'padding' => '5px'],
            'list_label' => ['font-weight' => 'bold', 'width' => '50%', 'display' => 'inline-block'],
            'list_text' => ['width' => '50%', 'display' => 'inline-block']
        ];
        foreach ($css as $k => $v) {
            if (array_key_exists($k, $this->cssClass)) {
                $this->cssClass[$k] = $v;
                $this->styles[$k] = '';
            }
        }
    }

    /**
     * @param array $data
     * @return string
     */
    private function renderColumn(array $data)
    {
        switch ($this->align) {
            case 'center': $a = ['text-align'=>'center']; break;
            case 'right': $a = ['text-align'=>'right']; break;
            case 'left':
            default: $a = ['text-align'=>'left'];
        }
        $this->styles['list_div'] = array_merge($this->styles['list_div'], $a);
        $wid = 100 / $this->column;
        $this->styles['list_item'] = array_merge($this->styles['list_item'], ['width'=>"$wid%"]);
        $css_list_div = $this->cssClass['list_div'];
        $style_list_div = $this->getStyle('list_div');
        $css_li = $this->cssClass['list_item'];
        $style_li = $this->getStyle('list_item');
        $css_label = $this->cssClass['list_label'];
        $style_label = $this->getStyle('list_label');
        $css_text = $this->cssClass['list_text'];
        $style_text = $this->getStyle('list_text');
        $html = new HtmlTag('div', '', ['class'=>$css_list_div, 'style'=>$style_list_div]);
        foreach ($data as $k => $v) {
            $label = T($k);
            $li = new HtmlTag('div', '', ['class'=>$css_li, 'style'=>$style_li]);
            $li->addChild(new HtmlTag('span', $label, ['class'=>$css_label, 'style'=>$style_label]))
                ->addChild(new HtmlTag('span', T($v), ['class'=>$css_text, 'style'=>$style_text]));
            $html->addChild($li);
        }
        return $html->render();
    }

} 