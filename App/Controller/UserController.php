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
            ->setHeader(DefaultController::$defaultViewHeaders)
            ->setView('auth/user_info')
            ->setData(['title'=>'User Center', 'user'=>$u])
            ->send();
        return false;
    }

    /**
     * Get user info
     *
     * @return bool
     */
    public static function info()
    {
        $u = getUser();
        if (!$u) {
            $u = [];
        }
        return self::getResponse()->setResType('json')->setHeader(DefaultController::$defaultApiHeaders)->setData($u)->send();
    }

    /**
     * Edit user info, should not available for oauth login
     *
     * @return bool
     */
    public static function edit()
    {
        session_start();
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
        getAssets()->addLibrary(['bootstrap-dialog']);
        self::getResponse()->setResType('view')
            ->setHeader(DefaultController::$defaultViewHeaders)
            ->setView('auth/user_edit')
            ->setData(['user'=>FieldFormatter::formatDateTime($u), CSRFCode::$key=>$state])
            ->send();
    }

    private static function postEdit()
    {
        $label = self::getRequest()->getInput('label');
        $email = self::getRequest()->getInput('email');
        $des = self::getRequest()->getInput('descr');
        $d = [IUser::FIELD_LABEL=>$label, IUser::FIELD_EMAIL=>$email, 'descr'=>$des];
        //check email
        $u = getDataPool()->getConnection('auth')->getMapper('User');
        $emailnum = $u->count(null, ['AND'=>[IUser::FIELD_EMAIL=>$email]]);
        if (!CSRFCode::check()) {
            $msg = Message::messageList(25);
        } elseif ($emailnum > 1) {
            $msg = Message::messageList(29);
        } elseif (getAuth()->getUser() && getAuth()->getUser()->changeInfo($d)) {
            getAuth()->getAuth()->saveCache();
            $msg = new Message(0, 'update successfully', 'success');
        } else {
            $msg = Message::messageList(13);
        }
        return self::getResponse()->setResType('json')->setData(TA($msg->toApiArray()))->send();
    }

}