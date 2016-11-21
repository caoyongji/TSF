<?php

class TSF
{
    protected static $config;
    /**
     * 递归包含文件
     * @param $dir
     * @return bool
     */
    public static function import($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        //打开目录
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {

            if ($file == "." || $file == "..") {
                continue;
            }
            $file = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($file))
            {
                $pathinfo = pathinfo($file );
                //包含php文件
                if(!empty($pathinfo['extension']) && $pathinfo['extension'] == 'php')
                {
                    require_once($file);
                }
            }
            elseif (is_dir($file))
            {
                self::import($file);
            }
        }
    }

    
    public static function initTSF()
    {
        if(!defined('TSF_PATH'))
        {
            return false;
        }

        self::import(TSF_PATH);
        $default_config_file = TSF_PATH . '/System/Config.php';
        $default_config = include($default_config_file);
        S::setConfig($default_config);
    }


    public static function initApp($app_config)
    {
        if(defined('COMMON_PATH'))
        {
            self::import(COMMON_PATH . '/Lib/');
            self::import(COMMON_PATH . '/Model/');
            if(file_exists(COMMON_PATH . 'Config/Config.php'))
            {
                $config = include(COMMON_PATH . 'Config/Config.php');
                S::mergeConfig($config);
            }
        }

        if(!defined('APP_PATH'))
        {
            return false;
        }

        self::import(APP_PATH . 'Lib/');
        self::import(APP_PATH . 'Model/');
        self::import(APP_PATH . 'Controller/');
        \Logger::setLogPath(APP_PATH . 'Log/');
        S::mergeConfig($app_config);
    }

    public static function getConfig()
    {
        return self::$config;
    }

}