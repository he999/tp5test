<?php
namespace app\manager\controller;
use think\Controller;
use think\Validate;
use think\Session;
use think\Cookie;
use think\Request;
use app\common\model\Com;
use app\common\model\Message;


/*************************************************  
*ClassName:     Manager
*Description:   后台控制器基类
*Others:        
*************************************************/
class Manager extends Controller
{
    /*************************************************  
    *Function:      _initialize
    *Description:   构造方法      
    *************************************************/
    public function _initialize()
    {
        /*************** 检查登陆 ***************/
        if (!session('uid') )
        { 
            $this->error('登录超时，请重新登录！','/index.php/manager/login/index');
        }
        // elseif(session('authdata')){
            // $request = Request::instance();
            // $mc = $request->module().'/'. $request->controller();
            // $mc = strtolower($mc);
            // if (!in_array($mc,session('authdata'))) {
                // $this->error('你没有权限！');die;
            // }
        // }
        $this->assign('username',session('username'));
        $this->assign('role',session('userinfo')['title']);
    }

    /**
     * jsAlert JS弹出框
     * @karl
     * @DateTime 2016-08-12T09:29:04+0800
     * @param    string                   $info [description]
     * @param    string                   $url  [description]
     */
    public function jsAlert($info, $url="")
    {
        $this->assign('info', $info);
        $this->assign('url', $url);
        echo $this->fetch(APP_PATH.request()->module().'/view/common/alert.html');
        die;
    }
     

          /*************************************************  
    * Function:      logout
    * Description:   退出系统
    *************************************************/
      public function logout()
      {
       session(null);
       $this->jsAlert('退出成功！','/index.php/manager/login/index');
       }  

}