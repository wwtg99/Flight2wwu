<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/19
 * Time: 18:25
 */

namespace Wwtg99\App\Controller\Admin;


use Wwtg99\App\Model\Message;

class RoleController extends ResourceAdminController
{

    protected $indexFields = ['role_id', 'name', 'descr', 'created_at', 'updated_at'];

    protected $showFields = ['role_id', 'name', 'descr', 'params', 'created_at', 'updated_at'];

    protected $storeFields = ['name', 'descr', 'params'];

    protected $updateFields = ['name', 'descr', 'params'];

    protected $title = 'Role Management';

    protected $baseRoute = '/admin/role';

    protected $templatePrefix = 'admin/role_';

    /**
     * @return array|bool
     */
    protected function storeParse()
    {
        $name = self::getRequest()->checkInput('name');
        if ($name instanceof Message) {
            $msg = $name->toArray();
            getOValue()->addOldOnce('msg', $msg);
            \Flight::redirect($this->baseRoute . '/create');
            return false;
        }
        return parent::storeParse();
    }

    /**
     * @param $id
     * @return array|bool
     */
    protected function updateParse($id)
    {
        $name = self::getRequest()->checkInput('name');
        if ($name instanceof Message) {
            $msg = $name->toArray();
            getOValue()->addOldOnce('msg', $msg);
            \Flight::redirect($this->baseRoute . "/$id/edit");
            return false;
        }
        $d = parent::updateParse($id);
        if (isset($d['params']) && !$d['params']) {
            $d['params'] = null;
        }
        return $d;
    }

    /**
     * @return \Wwtg99\DataPool\Common\IDataMapper
     */
    protected function getMapper()
    {
        $dep = getDataPool()->getConnection('auth')->getMapper('Role');
        return $dep;
    }
}