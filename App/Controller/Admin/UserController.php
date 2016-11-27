<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/20
 * Time: 9:18
 */

namespace Wwtg99\App\Controller\Admin;


use Wwtg99\App\Model\Message;
use Wwtg99\DataPool\Mappers\ArrayMapper;
use Wwtg99\DataPool\Utils\FieldFormatter;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;
use Wwtg99\PgAuth\Auth\IUser;

class UserController extends AdminAPIController
{

    protected $defaultListFields = ['user_id', 'name', 'label', 'email', 'department', 'roles', 'superuser', 'created_at', 'updated_at', 'deleted_at'];

    protected $defaultShowFields = ['user_id', 'name', 'label', 'email', 'department_id', 'department', 'department_descr', 'roles', 'superuser', 'params', 'descr', 'created_at', 'updated_at', 'deleted_at'];

    protected $filterFields = ['name', 'label', 'email', 'department_id', 'descr'];

    protected $createFields = ['name', 'label', 'password', 'email', 'descr', 'department_id', 'superuser', 'params'];

    protected $updateFields = ['label', 'password', 'email', 'descr', 'department_id', 'superuser', 'params', 'roles', 'deleted_at'];

    protected $viewList = 'admin/user_index';

    protected $viewShow = 'admin/user_show';

    protected $viewCreate = 'admin/user_edit';

    protected $viewEdit = 'admin/user_edit';

    protected $title = 'User Management';

    protected $route = 'admin/users';

    /**
     * List resources.
     *
     * @param array $fields
     * @param array $filter
     * @param array $sort
     * @param array $paging
     * @return array
     */
    protected function listResources($fields = null, $filter = [], $sort = [], $paging = [])
    {
        if (!$fields) {
            $fields = $this->defaultListFields;
        }
        $mapper = $this->getMapper();
        $context = [];
        if ($paging) {
            $context = $paging;
        }
        if ($sort) {
            $context[ArrayMapper::CONTEXT_ORDER] = $sort;
        }
        if ($context) {
            $mapper->setContext($context);
        }
        $data = $mapper->view($fields, $filter);
        $head = FormatUtils::formatHead($fields);
        return ['data'=>FieldFormatter::formatDateTime($data), 'head'=>$head, 'route'=>U($this->route)];
    }

    /**
     * Get one resource.
     *
     * @param $id
     * @param array $fields
     * @return array
     */
    protected function getResource($id, $fields = [])
    {
        if (!$fields) {
            $fields = $this->defaultShowFields;
        }
        $mapper = $this->getMapper();
        $data = $mapper->view($fields, [IUser::FIELD_USER_ID=>$id]);
        if ($data) {
            $data = $data[0];
            $data = FieldFormatter::formatDateTime($data);
        }
        return ['data'=>$data, 'route'=>U($this->route), 'id'=>$id];
    }

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
        $roles = isset($data['roles']) ? $data['roles'] : null;
        unset($data['roles']);
        $re = parent::createResource($data);
        if ($re instanceof Message) {
            return $re;
        }
        if (!is_null($roles)) {
            $rs = explode(',', $roles);
            $roles = [];
            foreach ($rs as $r) {
                array_push($roles, ['role_name'=>$r]);
            }
            $model = $this->getMapper();
            $re2 = $model->changeRoles($re, $roles);
            if (!$re2) {
                $re = Message::messageList(13);
            }
        }
        return $re;
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
        if (isset($data['password']) && $data['password']) {
            $data['password'] = password_hash($data['password'], CRYPT_BLOWFISH);
        } else {
            unset($data['password']);
        }
        if (isset($data['deleted_at']) && !$data['deleted_at']) {
            $data['deleted_at'] = null;
        }
        $roles = isset($data['roles']) ? $data['roles'] : null;
        unset($data['roles']);
        $re = parent::updateResource($id, $data);
        if ($re instanceof Message) {
            return $re;
        }
        if (!is_null($roles)) {
            $rs = explode(',', $roles);
            $roles = [];
            foreach ($rs as $r) {
                array_push($roles, ['role_name'=>$r]);
            }
            $model = $this->getMapper();
            $re2 = $model->changeRoles($id, $roles);
            if (!$re2) {
                $re = Message::messageList(13);
            }
        }
        return $re;
    }

    /**
     * Delete resource.
     *
     * @param $id
     * @return Message|array
     */
    protected function deleteResource($id)
    {
        $model = $this->getMapper();
        $re = $model->activeUser($id, false);
        return $re;

    }

    /**
     * Create new Item.
     * Method Get
     * @return mixed
     */
    public function create()
    {
        $dep = getDataPool()->getConnection('auth')->getMapper('Department');
        $deps = $dep->select(['department_id', 'name']);
        $rol = getDataPool()->getConnection('auth')->getMapper('Role');
        $roles = $rol->select(['role_id', 'name']);
        getAssets()->addLibrary(['validation', 'bootstrap-dialog', 'select2']);
        $data = ['title'=>$this->title, 'route'=>U($this->route), 'departments'=>$deps, 'roles'=>$roles];
        return self::getResponse()->setResType('view')
            ->setView($this->viewCreate)
            ->setData($data)
            ->send();
    }

    /**
     * Edit specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $dep = getDataPool()->getConnection('auth')->getMapper('Department');
        $deps = $dep->select(['department_id', 'name']);
        $rol = getDataPool()->getConnection('auth')->getMapper('Role');
        $roles = $rol->select(['role_id', 'name']);
        $dep = $this->getMapper();
        $re = $dep->view('*', ['user_id'=>$id]);
        if (isset($re[0])) {
            $re = $re[0];
        } else {
            $re = [];
        }
        $user_role = [];
        if (isset($re['roles'])) {
            $user_role = explode(',', $re['roles']);
        }
        $re = FieldFormatter::formatDateTime($re);
        getAssets()->addLibrary(['validation', 'bootstrap-dialog', 'select2']);
        $data = ['data'=>$re, 'id'=>$id, 'user_role'=>$user_role, 'departments'=>$deps, 'roles'=>$roles, 'title'=>$this->title, 'route'=>U($this->route)];
        return self::getResponse()->setResType('view')
            ->setView($this->viewEdit)
            ->setData($data)
            ->send();
    }

    /**
     * @return \Wwtg99\DataPool\Common\IDataMapper
     */
    protected function getMapper()
    {
        $dep = getDataPool()->getConnection('auth')->getMapper('User');
        return $dep;
    }
}