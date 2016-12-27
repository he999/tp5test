<?php
namespace app\manager\controller;
use think\Controller;
use think\Validate;
use app\common\model\User;
use app\common\model\base\Auth;
use app\common\model\base\Users;
use think\Session;
use think\Cookie;

/*************************************************  
*ClassName:     Login
*Description:   登录控制器类
*************************************************/
class Login extends Controller
{

    public function index()
    { 
        /******************* 验证信息 ********************/
        $rule = [
            'username'  => 'require|max:32',
            'password' =>  'require|max:32',
        ];
        $message = [
            'username.require'  =>  '用户名必须填写',
            'password.require'  =>  '密码必须填写',
            'username.max'      =>  '用户名最长为32位',
            'password.max'      =>  '用户名最长为32位',
        ];
        /******************* 检查是否提交表单 ************/
        if ($_POST)
        {
            /******************* 输入验证 *****************/        
            $validate = new Validate($rule, $message);
            $result   = $validate->check($_POST);
            if (!$result)
            {
                $this->jsAlert($validate->getError());
            }
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user_info = Users::login($username,$password);
            if ($user_info['error_code'] == 0)
            {   
                if($user_info['data']['role'] == 'manager'){
                  session("uid",$user_info['data']['uid']);
                  $info = Auth::getGroupInfo($user_info['data']['uid']);
                  session('userinfo',$info['data']);
                  session("username",$user_info['data']['username']);
                  $group = Auth::getAuthRole($user_info['data']['uid']);
                    if ($group['error_code'] == 0) {
                        foreach ($group['data'] as $k => $v) {
                            $data[$v['id']] = $v['rules'];
                        }
                        session('authdata',$data);
                    }
                  $this->jsAlert('登陆成功','/index.php/manager/member/memberlst');
                }
                else
                {
                  $this->jsAlert('您不是管理员帐号'); 
                }
            } 
            else
            {
              $this->jsAlert('用户名密码不匹配');
            }
        }
        else
        {
            session(null);
            return $this->fetch();
        }
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