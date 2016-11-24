<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/19
 * Time: 16:28
 */

namespace Wwtg99\App\Controller\Admin;


use Wwtg99\App\Model\Message;


class DepartmentController extends ResourceAdminController
{

    protected $indexFields = ['department_id', 'name', 'descr', 'created_at', 'updated_at'];

    protected $showFields = ['department_id', 'name', 'descr', 'params', 'created_at', 'updated_at'];

    protected $storeFields = ['department_id', 'name', 'descr', 'params'];

    protected $updateFields = ['department_id', 'name', 'descr', 'params'];

    protected $title = 'Department Management';

    protected $baseRoute = '/admin/department';

    protected $templatePrefix = 'admin/department_';

    /**
     * @return array|bool
     */
    protected function storeParse()
    {
        $id = self::getRequest()->checkInput('department_id');
        if ($id instanceof Message) {
            $msg = $id->toArray();
            getOValue()->addOldOnce('msg', $msg);
            \Flight::redirect($this->baseRoute . '/create');
            return false;
        }
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
        $did = self::getRequest()->checkInput('department_id');
        if ($did instanceof Message) {
            $msg = $did->toArray();
            getOValue()->addOldOnce('msg', $msg);
            \Flight::redirect($this->baseRoute . "/$id/edit");
            return false;
        }
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
     * Update specific item.
     * Method Post
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $d = $this->updateParse($id);
        if ($d === false) {
            return false;
        }
        $model = $this->getMapper();
        $re = $model->update($d, null, $id);
        if ($re) {
            $msg = Message::getMessage(0, 'update successfully', 'success');
            $id = $d['department_id'];
        } else {
            $msg = Message::getMessage(13);
        }
        getOValue()->addOldOnce('msg', $msg);
        \Flight::redirect($this->baseRoute . "/$id/edit");
        return false;
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