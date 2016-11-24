<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/3
 * Time: 16:09
 */

namespace Wwtg99\Flight2wwu\Component\Controller;

/**
 * Class RestfulController
 * Restful controller should extend this class and override all these methods.
 * @package Wwtg99\Flight2wwu\Common
 */
abstract class RestfulController extends BaseController
{

    /**
     * List all items.
     * Method: Get
     * @return mixed
     */
    abstract public function index();

    /**
     * Show specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    abstract public function show($id);

    /**
     * Create new Item.
     * Method Get
     * @return mixed
     */
    abstract public function create();

    /**
     * Store new Item.
     * Method Post
     * @return mixed
     */
    abstract public function store();

    /**
     * Edit specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    abstract public function edit($id);

    /**
     * Update specific item.
     * Method Post
     * @param $id
     * @return mixed
     */
    abstract public function update($id);

    /**
     * Destroy item.
     * Method Post
     * @param $id
     * @return mixed
     */
    abstract public function destroy($id);
}