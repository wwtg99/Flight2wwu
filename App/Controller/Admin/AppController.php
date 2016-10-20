<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/20
 * Time: 11:48
 */

namespace Wwtg99\App\Controller\Admin;


use Wwtg99\App\Model\Message;

class AppController extends ResourceAdminController
{

    protected $indexFields = ['app_id', 'app_name', 'redirect_uri', 'created_at', 'updated_at'];

    protected $showFields = ['app_id', 'app_name', 'descr', 'app_secret', 'redirect_uri', 'created_at', 'updated_at'];

    protected $storeFields = ['app_name', 'descr', 'redirect_uri'];

    protected $updateFields = ['app_name', 'app_secret', 'descr', 'redirect_uri'];

    protected $title = 'App Management';

    protected $baseRoute = '/admin/app';

    protected $templatePrefix = 'admin/app_';

    /**
     * Show specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        getAssets()->addLibrary(['bootstrap-dialog']);
        return parent::show($id);
    }

    /**
     * @return array|bool
     */
    protected function storeParse()
    {
        $name = self::checkInput('app_name');
        if ($name instanceof Message) {
            $msg = $name->toArray();
            getOValue()->addOldOnce('msg', $msg);
            \Flight::redirect($this->baseRoute . '/create');
            return false;
        }
        $uri = self::checkInput('redirect_uri');
        if ($uri instanceof Message) {
            $msg = $uri->toArray();
            getOValue()->addOldOnce('msg', $msg);
            \Flight::redirect($this->baseRoute . '/create');
            return false;
        }
        return parent::storeParse();
    }

    /**
     * @return \Wwtg99\DataPool\Common\IDataMapper
     */
    protected function getMapper()
    {
        $dep = getDataPool()->getConnection('auth')->getMapper('App');
        return $dep;
    }
}