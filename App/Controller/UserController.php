<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/3
 * Time: 17:02
 */

namespace Wwtg99\App\Controller;


use Wwtg99\Flight2wwu\Common\BaseController;

class UserController extends BaseController
{

    public static function center()
    {
        $u = getUser();
        getView()->render('auth/user_info', ['title'=>'User Center', 'user'=>$u]);
        return false;
    }

}