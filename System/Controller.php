<?php

class Controller
{
    public $request;
    public $con;
    public $act;
    public $post_body;
    public $get;

    /**
     * @var \HttpBase
     */
    public $http;

    public function __construct($con,$act,$req,$http_obj=null)
    {
        $this->request = $req;
        $this->con = $con;
        $this->act = $act;
        $this->http = $http_obj;
    }

    public function _init()
    {
        return true;
    }
}