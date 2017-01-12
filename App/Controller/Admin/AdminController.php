<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/19
 * Time: 18:23
 */

namespace Wwtg99\App\Controller\Admin;


use Wwtg99\App\Controller\DefaultController;
use Wwtg99\Flight2wwu\Component\Controller\BaseController;

class AdminController extends BaseController
{

    public static function home()
    {
        $db = getDataPool()->getConnection('auth');
        $dep_num = $db->getMapper('Department')->count();
        $role_num = $db->getMapper('Role')->count();
        $user_num = $db->getMapper('User')->count();
        $app_num = $db->getMapper('App')->count();
        self::getResponse()->setResType('view')
            ->setHeader(DefaultController::$defaultViewHeaders)
            ->setView('admin/admin_home')
            ->setData(['department_num'=>$dep_num, 'role_num'=>$role_num, 'user_num'=>$user_num, 'app_num'=>$app_num, 'title'=>'Admin'])
            ->send();
    }
}