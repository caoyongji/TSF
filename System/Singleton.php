<?php

class S
{
    protected static $_singletons;
    public static $_models;
    protected static $swoole;
    protected static $config;
    public static $data;

    /**
     * @var \HttpBase
     */
    protected static $http;

    public static function setHttp($http)
    {
        self::$http = $http;
    }

    public static function http()
    {
        return self::$http;
    }

    public static function swoole()
    {
        return self::$swoole;
    }

    public static function setSwoole($swoole)
    {
        self::$swoole = $swoole;
    }

    public static function initReqObjs($http=null,$swoole=null)
    {
        self::$http = $http;
        self::$swoole = $swoole;
        self::$data = null;
    }

    public static function cleanReqObjs()
    {
        self::$http = null;
        self::$swoole = null;
        self::$data = null;
    }




    /**
     * @param $key
     * @return Mysql
     */
    public static function getMysql($key)
    {
        if(empty(self::$_singletons['mysql'][$key]))
        {
            return false;
        }
        return self::$_singletons['mysql'][$key];
    }


    public static function setMysql($key,$config)
    {
        if(!empty(self::$_singletons['mysql'][$key]))
        {
            return true;
        }
        $mysql = new Mysql($config);
        self::$_singletons['mysql'][$key] = $mysql;
        return true;
    }

    public static function setPdo($key,$config)
    {
        if(!empty(self::$_singletons['pdo'][$key]))
        {
            return true;
        }
        $pdo = new \TPdo($config);
        self::$_singletons['pdo'][$key] = $pdo;
        return true;
    }

    /**
     * @param $key
     * @return \TPdo
     */
    public static function getPdo($key)
    {
        if(empty(self::$_singletons['pdo'][$key]))
        {
            return false;
        }
        return self::$_singletons['pdo'][$key];
    }


    /**
     * @param $key
     * @return \RedisCache
     */
    public static function getRedisCache($key)
    {
        if(empty(self::$_singletons['redis'][$key]))
        {
            return false;
        }
        return self::$_singletons['redis'][$key];
    }


    public static function setRedisCache($key,$config)
    {
        if(!empty(self::$_singletons['redis'][$key]))
        {
            return true;
        }
        $obj = new RedisCache($config);
        self::$_singletons['redis'][$key] = $obj;
        return true;
    }

    public static function delRedisCache($key)
    {
        if(empty(self::$_singletons['redis'][$key]))
        {
            return true;
        }

        self::$_singletons['redis'][$key]->__destruct();
        return true;
    }


    public static function setOther($key,$class_name,$config=array())
    {
        if(empty($config))
        {
            $obj = new $class_name();
        }
        else
        {
            $obj = new $class_name($config);
        }
        self::$_singletons['other'][$key] = $obj;
        return true;
    }

    public static function getOther($key)
    {
        return self::$_singletons['other'][$key];
    }

    public static function setExist($key,$class)
    {
        self::$_singletons['exist'][$key] = $class;
    }

    public static function getExist($key)
    {
        return self::$_singletons['exist'][$key];
    }


    /**
     * @param $table_name
     * @param string $db_key
     * @return \Model
     */
    public static function M($table_name,$db_key='default')
    {
        /*
        $model_key = $table_name.$db_key;
        if(!empty(self::$_models[$model_key]))
        {
            return self::$_models[$model_key];
        }
        */

        $model_obj = new Model($table_name,$db_key);
        //self::$_models[$model_key] = $model_obj;
        return $model_obj;
    }


    public static function config($keys='')
    {
        if(empty($keys))
        {
            return self::$config;
        }
        $key_arr = explode('.',$keys);
        $tmp = self::$config;
        foreach ($key_arr as $k)
        {
            if(isset($tmp[$k]))
            {
                $tmp = $tmp[$k];
            }
            else{
                return null;
            }
        }
        return $tmp;
    }

    public static function setConfig($config)
    {
        self::$config = $config;
    }

    public static function mergeConfig($config)
    {
        self::$config = array_merge(self::$config,$config);
    }

    public static function code($key)
    {
        return isset(self::$config['ERR_CODE'][$key]['status']) ?
            self::$config['ERR_CODE'][$key]['status'] : -999999;
    }

    public static function codeMsg($key)
    {
        return isset(self::$config['ERR_CODE'][$key]['info']) ?
            self::$config['ERR_CODE'][$key]['info'] : '系统错误';
    }
}
