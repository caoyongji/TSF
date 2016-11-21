框架说明
每个App目录结构如下
Config/        //配置文件所在目录
Controller/    //控制器文件所在目录
Log/           //日志目录
Public/        //网站跟目录,一般放入口文件以及静态资源
Tpl/            //模板文件目录



配置:
公共配置:  Common/Config/Config.php  公共配置是各个app共享的配置,一般用于配置数据库等公共数据.
工程配置:  每个App路径下必须要有一个Config/Config.php文件,这个配置一般作为app单独需要的数据,如果运行模式等.

框架会加载 工程配置和公共配置进行合并,在单例 S::config() 里可以获取, S::setConfig() 进行动态设置.
TSF是框架工程,单独的git维护.


模型 Model.
可用 S::M('table_name')  进行动态设置模型.
规范作法是,在 Common/Model/ 下对每个表定义一个模型文件, 采用单例模式, 以便对接口按不同数据表进行分类定义.

控制器 Controller
获取请求参数 $this->request.  如需准确获取 http的 get,post,header等数据,统一采用 $this->http 对象属性.

输出响应. 以 $this->http 对象的方法作为输出.
输出http头部 $this->http->header();
输出http响应码 $this->http->setStatus();
输出纯文本    $this->http->write();
输出json     $this->http->respJson();
重定向       $this->http->redirect();
设置模板参数   $this->http->assign();
输出模板内容   $this->http->display();
代码需严格按照以上模式进行响应,以便切换swoole引擎时保持兼容性.

记日志常用类:
Logger::log('file','content');