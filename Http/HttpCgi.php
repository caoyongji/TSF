<?php
require_once(dirname(__FILE__).'/HttpBase.php');
class HttpCgi extends HttpBase
{
    public function __construct()
    {
        foreach($_SERVER as $k=>$v)
        {
            if(strpos($k,'HTTP_')===0)
            {
                $key_name = substr($k,5);
                $this->header[$key_name] = $v;
            }
        }
        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
        $this->raw_content = file_get_contents("php://input");
        $_GET = null;
        $_POST = null;
        $_FILES = null;
    }

    public function sessionStart()
    {
        session_start();
        $this->session = $_SESSION;
    }

    public function setSession($key,$value)
    {
        $_SESSION[$key] = $value;
        $this->session = $_SESSION;
    }

    public function delSession($key)
    {
        unset($_SESSION[$key]);
        unset($this->session[$key]);
    }


    /**
     * @param $key
     * @param $value
     */
    public function header($key, $value)
    {
        header($key.': '.$value);
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
        setcookie($key, $value, $expire, $path, $domain, $secure, $http_only);
    }


    public function setSessionCookieParams($lifetime, $path = null, $domain = null, $secure = false, $httponly = false)
    {
        session_set_cookie_params($lifetime,$path,$domain,$secure,$httponly);
    }

    /**
     * @param $http_status_code
     */
    public function setStatus($http_status_code)
    {
        $msg = '';
        header("HTTP/1.1 $http_status_code $msg");
    }

    /**
     * @param int $level
     */
    public function gzip($level=1)
    {

    }



    /**
     * @param $file_name
     */
    public function sendfile($file_name)
    {
        $file_content = file_get_contents($file_name);
        echo $file_content;
    }


    /**
     * @param $content
     */
    public function end()
    {
        echo $this->resp_data;
        $this->resp_data = '';
    }

}