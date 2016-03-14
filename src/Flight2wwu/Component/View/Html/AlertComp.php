<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/14
 * Time: 14:28
 */

namespace Flight2wwu\Component\View\Html;


class AlertComp extends StyledComponent
{

    /**
     * @var string
     */
    private $status;

    /**
     * @param array $data
     * @return string
     */
    public function render(array $data = [])
    {
        $msg = $data['data'];
        $style = array_key_exists('style', $data) ? $data['style'] : 1;
        switch($style) {
            case 1:
            default: $html = $this->renderAlert($msg); break;
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Alert';
    }

    /**
     * @return array|string
     */
    public function getLibrary()
    {
        return 'bootstrap';
    }

    /**
     * AlertComp constructor.
     * @param string $status
     */
    public function __construct($status = 'info')
    {
        $this->status = $status;
    }

    /**
     * @param $msg
     * @return string
     */
    private function renderAlert($msg)
    {
        switch($this->status) {
            case 'success': $css = 'alert-success'; break;
            case 'info': $css = 'alert-info'; break;
            case 'warning': $css = 'alert-warning'; break;
            case 'danger': $css = 'alert-danger'; break;
            default: $css = 'alert-info'; break;
        }
        $div = new HtmlTag('div', $msg, ['class'=>['text-center', 'alert', $css], 'role'=>'alert']);
        return $div->render();
    }

}