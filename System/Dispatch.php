<?php

class Dispatch
{
    /*
    const SUCCESS = 0;

    const ERR_CHECK_SIGN = 100;     //签名错误
    const ERR_USER_PASSWORD = 101;     //用户名或密码错误
    const ERR_NO_LOGIN = 200;       //未登录
    const ERR_TOKEN_EMPTY = 200;    //token为空
    const ERR_PARAMS = 300;         //参数错误
    const ERR_SYSTEM = 500;         //系统繁忙
    const ERR_FORBIDDEN = 403;         //禁止访问



    protected static $err_msg = [
        self::ERR_CHECK_SIGN=>'签名错误',
        self::ERR_USER_PASSWORD=>'用户名或密码错误',
        self::ERR_NO_LOGIN=>'未登录',
        self::ERR_TOKEN_EMPTY =>'未获取登录态',
        self::ERR_PARAMS=>'请求参数错误',
        self::ERR_SYSTEM=>'系统繁忙',
        self::ERR_FORBIDDEN => '无权访问',
    ];
    */


    public static function runByJson($data)
    {
        $con = $data['con'];
        $act = $data['act'];
        return self::route($con,$act,$data);
    }

    public static function filter($req_data)
    {
        //对请求参数进行安全过滤
        foreach($req_data as $k=>$v)
        {
            if(is_array($v))
            {
                $req_data[$k] = self::filter($v);
            }
            else
            {
                $req_data[$k] = TString::filterContent($v);
            }
        }
        return $req_data;
    }


    public static function  runByHttpRewriteJson()
    {
        $path_args = '';
        $_COOKIE = self::filter($_COOKIE);
        $cont_act_param = $_SERVER['PATH_INFO'];
        if(empty($cont_act_param))
        {
            $con = 'Index';
            $act = 'index';
        }
        else
        {
            $param_explode = explode('/',$cont_act_param);

            if(empty($param_explode[1]) || empty($param_explode[2]))
            {
                //非模板输出则默认使用json
                if(View::$is_display == false)
                {
                    return self::jsonOut(Dispatch::formatError(Dispatch::ERR_PARAMS,'params err'));
                }
                else{
                    die('403');
                }
            }
            $con = $param_explode[1];
            $act = $param_explode[2];
            if(!empty($param_explode[3]))
            {
                $path_args = $param_explode[3];
            }
        }



        $post_body = null;
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $req_data = $_GET;
        }
        else
        {
            $req_data = $_POST; //$_POST隐含的使用条件是post部分要使用x=y&z=k的格式
            //$req_data = array_merge($_GET,$_POST);
            $post_body = file_get_contents("php://input");//获取http的整个body部分
        }
        $req_data['con'] = $con;
        $req_data['act'] = $act;
        if(!empty($path_args))
        {
            $req_data['path_args'] = $path_args;
        }

        session_set_cookie_params(3600*8,"/",null,false,true);
        session_start();
        //setcookie(session_name(), session_id(),3600*8,"/",null,false, true);
        $data = self::route($con,$act,$req_data,$post_body,$_GET);
        if(View::$is_display == false)
        {
            self::jsonOut($data);
        }
    }


    /**
     * @param \HttpBase $http_obj
     * @return mixed
     */
    public static function runBySwHttpRewriteJson($http_obj)
    {
        $path_args = '';
        $_COOKIE = $http_obj->cookie;
        $_COOKIE = self::filter($_COOKIE);
        $_SERVER = $http_obj->server;
        $_GET = $http_obj->get;
        $_POST = $http_obj->post;
        $cont_act_param = substr($_SERVER['PATH_INFO'],1);

        if(empty($cont_act_param))
        {
            $con = 'Index';
            $act = 'index';
        }
        else
        {
            $param_explode = explode('/',$cont_act_param);

            if(empty($param_explode[1]) || empty($param_explode[2]))
            {
                //非模板输出则默认使用json
                if(View::$is_display == false)
                {
                    return self::swJsonOut($http_obj,Dispatch::formatError(Dispatch::ERR_PARAMS,'params err'));
                }
                else{
                    return $http_obj->setStatus(403);
                }
            }
            $con = $param_explode[1];
            $act = $param_explode[2];
            if(!empty($param_explode[3]))
            {
                $path_args = $param_explode[3];
            }
        }



        $post_body = null;
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $req_data = $_GET;
        }
        else
        {
            $req_data = $_POST; //$_POST隐含的使用条件是post部分要使用x=y&z=k的格式
            //$req_data = array_merge($_GET,$_POST);
            $post_body = $http_obj->raw_content;//获取http的整个body部分
        }
        $req_data['con'] = $con;
        $req_data['act'] = $act;
        if(!empty($path_args))
        {
            $req_data['path_args'] = $path_args;
        }

        //session_set_cookie_params(3600*8,"/",null,false,true);
        //session_start();
        //setcookie(session_name(), session_id(),3600*8,"/",null,false, true);
        $data = self::route($con,$act,$req_data,$post_body,$_GET,$http_obj);
        if(View::$is_display == false)
        {
            return self::swJsonOut($http_obj,$data);
        }
    }


    public static function runByUriJson($session=true,$session_timeout=24800)
    {
        $_COOKIE = self::filter($_COOKIE);

        $con = isset($_GET['con']) ? $_GET['con'] : 'Index';
        $act = isset($_GET['act']) ? $_GET['act'] : 'index';

        $post_body = null;
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $req_data = $_GET;
        }
        else
        {
            //$req_data = $_POST;
            $req_data = array_merge($_GET,$_POST);
            $post_body = file_get_contents("php://input");
        }
        $req_data['con'] = $con;
        $req_data['act'] = $act;

        if($session)
        {
            session_set_cookie_params($session_timeout,"/",null,false,true);
            session_start();
            //setcookie(session_name(), session_id(),3600*8,"/",null,false, true);
        }

        $data = self::route($con,$act,$req_data,$post_body,$_GET);
        if(View::$is_display == false)
        {
            self::jsonOut($data);
        }
    }


    public static function jsonOut($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode($data,JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE );

        return true;
    }

    /**
     * @param \HttpBase $http_obj
     * @return mixed
     */
    public static function swJsonOut($http_obj,$data)
    {
        $http_obj->setHeader('Content-Type','application/json; charset=utf-8');
        return $http_obj->send(json_encode($data,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ));
    }

    public static function run($stream)
    {
        $unpack_data = Soaproto::unpackJson($stream);

        switch($unpack_data['head']['type'])
        {
            case Soaproto::PROTO_TYPE_JSON:
                $result = self::runByJson($unpack_data['body']);
                $pack_stream = Soaproto::packJson($result,$unpack_data['head']['version']);
                return $pack_stream;
                break;
        }
    }

    public static function runByProtobuf()
    {

    }


    public static function format($code=self::SUCCESS,$msg='',$data=[])
    {
        return array(
            'status'=>$code,
            'info'=>$msg,
            'data'=>$data,
        );
    }


    public static function formatError($code,$msg='',$data=[])
    {
        if(empty($msg))
        {
            if(isset(self::$err_msg[$code]))
            {
                $msg = self::$err_msg[$code];
            }
        }
        return self::format($code,$msg,$data);
    }

    public static function formatSuccess($data=[],$msg='success')
    {
        return self::format(self::SUCCESS,$msg,$data);
    }


    public static function isSuccess($data)
    {
        if($data['status'] == self::SUCCESS)
        {
            return true;
        }
        return false;
    }


    public static function getDefaltValue($conf,$data)
    {
        $ret_data = [];
        foreach($conf as $k=>$v)
        {
            $ret_data[$k] = isset($data[$k]) ? $data[$k] : $conf[$k];
        }
        return $ret_data;
    }




    public static function runHttp($config)
    {
        $http_engine = $config['http_engine'];
        switch ($http_engine)
        {
            case 'cgi':
                $http_obj = new \HttpCgi();
                break;
            default:
                $http_obj = new \HttpCgi();
                break;
        }

        $route_type = $config['route_type'];
        switch ($route_type)
        {
            case 'uri_json':

                $con = isset($http_obj->get['con']) ? $http_obj->get['con'] : 'Index';
                $act = isset($http_obj->get['act']) ? $http_obj->get['act'] : 'index';

                if($http_obj->server['REQUEST_METHOD'] == 'GET')
                {
                    $req_data = $http_obj->get;
                }
                else
                {
                    //$req_data = $_POST;
                    $req_data = array_merge($http_obj->get,$http_obj->post);
                }
                $req_data['con'] = $con;
                $req_data['act'] = $act;

            break;

            case 'uri_route':
                $url_key = isset($config['route']['url_key']) ? $config['route']['url_key'] : '_url';
                if(!empty($http_obj->get[$url_key]))
                {
                    $url = $http_obj->get[$url_key];
                    $url_params = explode('/',$url);
                }
                else
                {
                    $url_params = [];
                }
                foreach($url_params as $k=>$p)
                {
                    if(empty($p))
                    {
                        unset($url_params[$k]);
                    }
                }

                $url_params = array_values($url_params);

                if(empty($url_params) || empty($url_params[1]))
                {
                    $con = 'Index';
                    $act = 'index';
                }
                else
                {
                    $con = $url_params[0];
                    $act = $url_params[1];
                }

                /*
                $url_format = '/'.implode('/',$url_params).'/';
                $route_format = isset($config['route']['format']) ? $config['route']['format'] : [];
                if(!empty($route_format))
                {
                    foreach($route_format as $r=>$c)
                    {
                        if(strpos($url_format,$r)===0)
                        {
                            if(is_int($c['con']))
                            {
                                $con = $url_params[$c['con']];
                            }
                            else{
                                $con = $c['con'];
                            }

                            if(is_int($c['act']))
                            {
                                $act = $url_params[$c['act']];
                            }
                            else
                            {
                                $act = $c['act'];
                            }

                            break;
                        }
                    }
                }
                */


                $req_data = array_merge($url_params,$http_obj->get,$http_obj->post);
                //$req_data['con'] = $con;
                //$req_data['act'] = $act;

                break;
        }
        if(isset($config['session']) && $config['session']==true)
        {
            $http_obj->setSessionCookieParams($config['session_timeout'],"/",null,false,true);
            $http_obj->sessionStart();
        }

        S::initReqObjs($http_obj);
        self::route($con,$act,$req_data,$http_obj);
        $http_obj->end();
        S::cleanReqObjs();
        unset($http_obj);
        return;
    }


    /**
     * @param $con
     * @param $act
     * @param $req_data
     * @param  \HttpBase $http_obj
     * @return bool
     */
    public static function route($con,$act,$req_data,$http_obj=null)
    {
        $con_class_name = 'Controller\\'.$con;

        if(!class_exists($con_class_name))
        {
            $http_obj->setStatus(400);
            return false;
        }

        $req_data = TString::filter($req_data);
        $http_obj->get = TString::filter($http_obj->get);
        $http_obj->post = TString::filter($http_obj->post);
        $http_obj->cookie = TString::filter($http_obj->cookie);

        $controller_class = new $con_class_name($con,$act,$req_data,$http_obj);
        if(!method_exists($controller_class,$act) || !is_callable(array($controller_class,$act),true))
        {
            unset($controller_class);
            $http_obj->setStatus(400);
            return false;
        }


        $init_ret = $controller_class->_init();

        //初始化验证失败
        if(empty($init_ret))
        {
            unset($controller_class);
            return false;
        }

        //执行act
        $controller_class->$act();

        if(method_exists($controller_class,'_finish'))
        {
            $controller_class->_finish();
        }

        unset($controller_class);
        return true;
    }

}