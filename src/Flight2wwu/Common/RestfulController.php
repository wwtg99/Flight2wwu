<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/3
 * Time: 16:09
 */

namespace Wwtg99\Flight2wwu\Common;

/**
 * Class RestfulController
 * Restful controller should extend this class and override all these methods.
 * @package Wwtg99\Flight2wwu\Common
 */
abstract class RestfulController extends BaseController
{

    /**
     * @var string
     */
    protected static $conn = '';

    /**
     * @var string
     */
    protected static $name = '';

    /**
     * List all items.
     * Method: Get
     * @return mixed
     */
    public static function index()
    {

    }

    /**
     * Show specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public static function show($id)
    {

    }

    /**
     * Create new Item.
     * Method Get
     * @return mixed
     */
    public static function create()
    {

    }

    /**
     * Store new Item.
     * Method Post
     * @return mixed
     */
    public static function store()
    {

    }

    /**
     * Edit specific item.
     * Method Get
     * @param $id
     * @return mixed
     */
    public static function edit($id)
    {

    }

    /**
     * Update specific item.
     * Method Post
     * @param $id
     * @return mixed
     */
    public static function update($id)
    {

    }

    /**
     * Destroy item.
     * Method Post
     * @param $id
     * @return mixed
     */
    public static function destroy($id)
    {

    }
}