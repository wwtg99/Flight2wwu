<#php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/10/20
 * Time: 9:18
 */

namespace Wwtg99\App\Controller\Admin;


use Wwtg99\App\Model\Message;
use Wwtg99\DataPool\Utils\FieldFormatter;
use Wwtg99\Flight2wwu\Component\Utils\FormatUtils;

class UserController extends ResourceAdminController
{

    protected $indexFields = ['user_id', 'name', 'label', 'email', 'department', 'roles', 'superuser', 'created_at', 'updated_at', 'deleted_at'];

    protected $showFields = ['user_id', 'name', 'label', 'email', 'department_id', 'department', 'department_descr', 'roles', 'superuser', 'params', 'descr', 'created_at', 'updated_at', 'deleted_at'];

    protected $storeFields = ['name', 'label', 'password', 'email', 'descr', 'department_id', 'superuser', 'params'];

    protected $updateFields = ['label', 'password', 'email', 'descr', 'department_id', 'superuser', 'params'];

    protected $title = 'User Management';

    protected $baseRoute = '/admin/user';

    protected $templatePrefix = 'admin/user_';

    /**
     * List all items.
     * Method: Get
     * @return mixed
     */
    public function index()
    {
        $model = $this->getMapper();
        $re = $model->view($this->indexFields);
        $re = FieldFormatter::formatDateTime($re);
        getAssets()->addLibrary(['bootstrap-table', 'bootstrap-dialog']);
        getView()->render($this->templatePrefix . 'index', ['data'=>$re, 'head'=>FormatUtils::formatHead($this->indexFields), 'title'=>$this->title, 'route'=>$this->baseRoute]);
        return false;
    }

    /**
     * Show specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $model = $this->getMapper();
        $re = $model->view($this->showFields, ['user_id'=>$id]);
        if (isset($re[0])) {
            $re = $re[0];
        } else {
            $re = [];
        }
        $re = FieldFormatter::formatDateTime($re);
        getView()->render($this->templatePrefix . 'show', ['data'=>$re, 'title'=>$this->title, 'route'=>$this->baseRoute]);
        return false;
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
        getAssets()->addLibrary(['select2']);
        getView()->render('admin/user_edit', ['departments'=>$deps, 'roles'=>$roles, 'title'=>$this->title, 'route'=>$this->baseRoute]);
    }

    /**
     * Store new Item.
     * Method Post
     * @return mixed
     */
    public function store()
    {
        $d = $this->storeParse();
        if ($d === false) {
            return false;
        }
        $model = $this->getMapper();
        $re = $model->insert($d);
        //roles
        $roles = self::getInput('roles');
        $re2 = true;
        if ($roles) {
            $rs = explode(',', $roles);
            $roles = [];
            foreach ($rs as $r) {
                array_push($roles, ['role_name'=>$r]);
            }
            $re2 = $model->changeRoles($re, $roles);
        }
        if ($re && $re2) {
            $msg = Message::getMessage(0, 'create successfully', 'success');
        } else {
            $msg = Message::getMessage(12);
        }
        getOValue()->addOldOnce('msg', $msg);
        \Flight::redirect($this->baseRoute);
        return false;
    }

    /**
     * @return array|bool
     */
    protected function storeParse()
    {
        $name = self::checkInput('name');
        if ($name instanceof Message) {
            $msg = $name->toArray();
            getOValue()->addOldOnce('msg', $msg);
            \Flight::redirect($this->baseRoute . "/create");
            return false;
        }
        $d = parent::storeParse();
        if (isset($d['password'])) {
            $d['password'] = password_hash($d['password'], PASSWORD_BCRYPT);
        }
        return $d;
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
        getAssets()->addLibrary(['select2']);
        getView()->render('admin/user_edit', ['data'=>$re, 'user_role'=>$user_role, 'departments'=>$deps, 'roles'=>$roles, 'title'=>$this->title, 'route'=>$this->baseRoute]);
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
        $model = $this->getMapper();
        $re = $model->update($d, null, $id);
        //roles
        $roles = self::getInput('roles');
        $re2 = true;
        if ($roles) {
            $rs = explode(',', $roles);
            $roles = [];
            foreach ($rs as $r) {
                array_push($roles, ['role_name'=>$r]);
            }
            $re2 = $model->changeRoles($id, $roles);
        }
        if ($re && $re2) {
            $msg = Message::getMessage(0, 'update successfully', 'success');
        } else {
            $msg = Message::getMessage(13);
        }
        getOValue()->addOldOnce('msg', $msg);
        \Flight::redirect($this->baseRoute . "/$id/edit");
        return false;
    }

    /**
     * @param $id
     * @return array|bool
     */
    protected function updateParse($id)
    {
        $d = parent::updateParse($id);
        if (isset($d['password']) && $d['password']) {
            $d['password'] = password_hash($d['password'], PASSWORD_BCRYPT);
        } else {
            unset($d['password']);
        }
        if (isset($d['department_id']) && !$d['department_id']) {
            $d['department_id'] = null;
        }
        if (isset($d['params']) && !$d['params']) {
            $d['params'] = null;
        }
        if (isset($d['superuser']) && $d['superuser']) {
            $d['superuser'] = 'true';
        } else {
            $d['superuser'] = 'false';
        }
        return $d;
    }

    /**
     * Destroy item.
     * Method Post
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $active = self::getInput('active', false);
        $model = $this->getMapper();
        $re = $model->activeUser($id, $active);
        if ($re) {
            $msg = Message::getMessage(0, 'delete successfully', 'success');
        } else {
            $msg = Message::getMessage(14);
        }
        \Flight::json(TA($msg));
        return false;
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