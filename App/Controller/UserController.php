<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/3
 * Time: 17:02
 */

namespace Wwtg99\App\Controller;


use Wwtg99\App\Model\Message;
use Wwtg99\DataPool\Utils\FieldFormatter;
use Wwtg99\Flight2wwu\Component\Controller\BaseController;
use Wwtg99\Flight2wwu\Component\Utils\CSRFCode;
use Wwtg99\PgAuth\Auth\IUser;

class UserController extends BaseController
{

    /**
     * User center
     *
     * @return bool
     */
    public static function center()
    {
        $u = getUser();
        self::getResponse()->setResType('view')
            ->setView('auth/user_info')
            ->setData(['title'=>'User Center', 'user'=>$u])
            ->send();
        return false;
    }

    /**
     * Edit user info, should not available for oauth login
     *
     * @return bool
     */
    public static function edit()
    {
        if (self::getRequest()->checkMethod('POST')) {
            self::postEdit();
        } else {
            self::getEdit();
        }
        return false;
    }

    private static function getEdit()
    {
        $state = getCSRF()->generateCSRFCode();
        $u = getUser();
        self::getResponse()->setResType('view')
            ->setView('auth/user_edit')
            ->setData(['user'=>FieldFormatter::formatDateTime($u), CSRFCode::$key=>$state])
            ->send();
    }

    private static function postEdit()
    {
        $label = self::getRequest()->getInput('label');
        $email = self::getRequest()->getInput('email');
        $des = self::getRequest()->getInput('descr');
        $user = getAuth()->getUserObject();
        $d = [IUser::FIELD_LABEL=>$label, IUser::FIELD_EMAIL=>$email, 'descr'=>$des];
        //check email
        $u = getDataPool()->getConnection('auth')->getMapper('User');
        $emailnum = $u->count(null, ['AND'=>[IUser::FIELD_EMAIL=>$email]]);
        if (!CSRFCode::check()) {
            $msg = Message::getMessage(25);
        } elseif ($emailnum > 1) {
            $msg = Message::getMessage(29);
        } elseif ($user && $user->changeInfo($d)) {
            $msg = Message::getMessage(0, 'update successfully', 'success');
            getAuth()->refreshSession();
        } else {
            $msg = Message::getMessage(13);
        }
        getOValue()->addOldOnce('msg', $msg);
        $rpath = U(getConfig()->get('defined_routes.user_edit'));
        \Flight::redirect($rpath);
    }

}