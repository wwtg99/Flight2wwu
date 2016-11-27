<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/19
 * Time: 18:25
 */

namespace Wwtg99\App\Controller\Admin;


use Wwtg99\App\Model\Message;

class RoleController extends AdminAPIController
{

    protected $defaultListFields = ['role_id', 'name', 'descr', 'created_at', 'updated_at'];

    protected $defaultShowFields = ['role_id', 'name', 'descr', 'params', 'created_at', 'updated_at'];

    protected $filterFields = ['role_id', 'name', 'descr'];

    protected $createFields = ['name', 'descr', 'params'];

    protected $updateFields = ['name', 'descr', 'params'];

    protected $viewList = 'admin/role_index';

    protected $viewShow = 'admin/role_show';

    protected $viewCreate = 'admin/role_edit';

    protected $viewEdit = 'admin/role_edit';

    protected $title = 'Role Management';

    protected $route = 'admin/roles';


    /**
     * Create resource.
     *
     * @param array $data
     * @return Message|array
     */
    protected function createResource($data)
    {
        if (!isset($data['name'])) {
            return new Message(11, 'invalid name');
        }
        if (isset($data['params']) && !$data['params']) {
            $data['params'] = null;
        }
        return parent::createResource($data);
    }

    /**
     * Update resource.
     *
     * @param $id
     * @param array $data
     * @return Message|array
     */
    protected function updateResource($id, $data)
    {
        if (isset($data['params']) && !$data['params']) {
            $data['params'] = null;
        }
        return parent::updateResource($id, $data);
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