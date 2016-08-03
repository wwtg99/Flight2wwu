<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/3
 * Time: 17:02
 */

namespace Wwtg99\App\Controller;


use Wwtg99\Flight2wwu\Common\RestfulController;

class UserController extends RestfulController
{

    /**
     * List all items.
     * Method: Get
     * @return mixed
     */
    public static function index()
    {
        echo "Index users";
        echo '<div><a href="/user/create">Create user</a></div>';
        echo '<div><a href="/user/1">User id 1</a></div>';
        echo '<div><a href="/user/2">User id 2</a></div>';
    }

    /**
     * Show specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public static function show($id)
    {
        echo "Show user id $id";
        echo '<div><a href="/user/edit/' . $id . '">Edit</a></div>';
        echo '<form method="post" action="/user/destroy/' . $id . '"><input type="hidden" name="id"><button type="submit">Destroy</button></form>';
    }

    /**
     * Create new Item.
     * Method Get
     * @return mixed
     */
    public static function create()
    {
        echo 'Create user';
        echo '<form method="post" action="/user"><label>id: </label><input type="text" name="id"><label>name: </label><input type="text" name="name"><button type="submit">Submit</button></form>';
    }

    /**
     * Store new Item.
     * Method Post
     * @return mixed
     */
    public static function store()
    {
        $id = self::getInput('id');
        $name = self::getInput('name');
        echo "Store user id $id name $name";
    }

    /**
     * Edit specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public static function edit($id)
    {
        echo "Edit user id $id";
        echo '<form method="post" action="/user/' . $id . '"><label>id: </label><input type="text" name="id" value="' . $id . '" disabled><label>name: </label><input type="text" name="name"><button type="submit">Submit</button></form>';
    }

    /**
     * Update specific item.
     * Method Post
     * @param $id
     * @return mixed
     */
    public static function update($id)
    {
        $name = self::getInput('name');
        echo "update user id $id name to $name";
    }

    /**
     * Destroy item.
     * Method Post
     * @param $id
     * @return mixed
     */
    public static function destroy($id)
    {
        echo "Destroy user id $id";
    }


}