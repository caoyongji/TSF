<?php
class HttpBase
{
    public $header;
    public $server;

    public $get;
    public $post;
    public $cookie;
    public $files;
    public $raw_content;
    public $session;
    public $resp_data;
    protected $display_data;


    public function sessionStart()
    {
        
    }


    public function setSession($key,$value)
    {

    }


    public function delSession($key)
    {

    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function header($key, $value)
    {

    }

    /**
     * @param $key
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $http_only
     */
    public function setCookie($key, $value = '', $expire = 0 , $path = '/', $domain  = '', $secure = false ,$http_only = false)
    {

    }
    
    public function setSessionCookieParams($lifetime, $path = null, $domain = null, $secure = false, $httponly = false)
    {

    }


    /**
     * @param $http_status_code
     */
    public function setStatus($http_status_code)
    {

    }

    /**
     * @param int $level
     */
    public function gzip($level=1)
    {

    }

    /**
     * @param $data
     */
    public function write($data)
    {
        $this->resp_data .= $data;
    }


    /**
     * @param $file_name
     */
    public function sendfile($file_name)
    {

    }


    /**
     * @param $content
     */
    public function end()
    {

    }

    public function assign($name,$val)
    {
        $this->display_data[$name] = $val;
    }

    public function display($file_name)
    {
        if(!defined('TPL_PATH'))
        {
            return false;
        }
        $data = $this->display_data;

        $full_file_name = TPL_PATH .'/'.$file_name;
        ob_start();
        include($full_file_name);
        $resp_content = ob_get_contents();
        ob_end_clean();
        $this->display_data = null;
        unset($data);
        $this->write($resp_content);
    }

    public function redirect($url)
    {
        $this->header('Location',$url);
        return true;
    }

    public function respJson($data)
    {
        $this->header('Content-Type','application/json; charset=utf-8');
        $this->write(json_encode($data,JSON_PRETTY_PRINT |
                JSON_UNESCAPED_UNICODE ));
        return true;
    }
}