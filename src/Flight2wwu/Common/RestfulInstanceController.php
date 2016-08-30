<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/30
 * Time: 9:40
 */

namespace Wwtg99\Flight2wwu\Common;

/**
 * Class RestfulInstanceController
 * Restful controller should extend this class and override all these methods.
 * @package Wwtg99\Flight2wwu\Common
 */
abstract class RestfulInstanceController extends InstanceController
{

    /**
     * List all items.
     * Method: Get
     * @return mixed
     */
    public function index()
    {

    }

    /**
     * Show specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public function show($id)
    {

    }

    /**
     * Create new Item.
     * Method Get
     * @return mixed
     */
    public function create()
    {

    }

    /**
     * Store new Item.
     * Method Post
     * @return mixed
     */
    public function store()
    {

    }

    /**
     * Edit specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {

    }

    /**
     * Update specific item.
     * Method Post
     * @param $id
     * @return mixed
     */
    public function update($id)
    {

    }

    /**
     * Destroy item.
     * Method Post
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {

    }
}