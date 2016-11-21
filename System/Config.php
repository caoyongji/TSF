<?php
return [
    'TSF' => [

        'RETURN_FORMAT' => [
            'STATUS' =>'status',
            'INFO' => 'info',
            'DATA' => 'data'
        ],

        'Singleton' => [
            'format'=>'FormatBase',
            'http'=> 'HttpCgi',
        ],

        'Format' => 'FormatBase',
    ],


    'ERR_CODE' => [
        'SUCCESS' => ['status'=>0,'info'=>'成功'],
        'ERR_PARAMS' => ['status'=>300,'info'=>'参数错误'],
        'ERR_NO_LOGIN' => ['status'=>400,'info'=>'未登录'],
        'ERR_FORBIDDEN' => ['status'=>403,'info'=>'禁止访问'],
        'ERR_SYSTEM' => ['status'=>500,'info'=>'系统繁忙'],
    ],




];