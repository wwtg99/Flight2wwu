<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/19
 * Time: 16:28
 */

namespace Wwtg99\App\Controller\Admin;


use Wwtg99\App\Model\Message;


class DepartmentController extends AdminAPIController
{

    protected $defaultListFields = ['department_id', 'name', 'descr', 'created_at', 'updated_at'];

    protected $defaultShowFields = ['department_id', 'name', 'descr', 'params', 'created_at', 'updated_at'];

    protected $filterFields = ['department_id', 'name', 'descr'];

    protected $createFields = ['department_id', 'name', 'descr', 'params'];

    protected $updateFields = ['department_id', 'name', 'descr', 'params'];

    protected $viewList = 'admin/department_index';

    protected $viewShow = 'admin/department_show';

    protected $viewCreate = 'admin/department_edit';

    protected $viewEdit = 'admin/department_edit';

    protected $title = 'Department Management';

    protected $route = 'admin/departments';

    /**
     * Create resource.
     *
     * @param array $data
     * @return Message|array
     */
    protected function createResource($data)
    {
        if (!isset($data['department_id'])) {
            return new Message(11, 'invalid department_id');
        }
        if (!isset($data['name'])) {
            return new Message(11, 'invalid name');
        }
        $model = $this->getMapper();
        $d = $model->get(null, null, ['OR'=>['department_id'=>$data['department_id'], 'name'=>$data['name']]]);
        if ($d) {
            return Message::messageList(34);
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
        $dep = getDataPool()->getConnection('auth')->getMapper('Department');
        return $dep;
    }

}