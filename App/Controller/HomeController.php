<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 18:04
 */

namespace Wwtg99\App\Controller;


use Wwtg99\Flight2wwu\Common\BaseController;

class HomeController extends BaseController
{
    /**
     * Home page
     */
    public static function home()
    {
        getView()->render('home');
        return false;
    }

    /**
     * Change log
     */
    public static function changelog()
    {
        self::defaultHeader();
        $f = ROOT . DIRECTORY_SEPARATOR . 'CHANGELOG.md';
        if (file_exists($f)) {
            $md = new \Parsedown();
            $f = file_get_contents($f);
            echo $md->text($f);
        }
        return false;
    }
} 