<?php
namespace app\manager\controller;
use app\common\model\User;
use app\common\model\Auth;
use app\common\model\Area;
use think\Session;
use think\Cookie;
use think\Controller;
use think\Validate;
use think\Response;
use think\Request;
use think\Db;

/*************************************************  
*ClassName:     Login
*Description:   登录控制器类
*************************************************/
class Index extends Manager
{
    /*************************************************  
    * Function:      change_password
    * Description:   修改密码
    *************************************************/
    public function change_password ()
    {
		$input_data = Request::instance()->param();
		if($_POST)
		{
			/******************* 验证信息 ********************/
			$rule = [
				'old_pwd'  => 'require|max:50',
				'new_pwd'  => 'require|max:50',
				'confirm_pwd'  => 'require|max:50', 
			];
			$msg = [
				'old_pwd.max'      =>  '旧密码最长为50位',
				'old_pwd.require'  =>  '旧密码必须填写',
				'new_pwd.require'  =>  '新密码必须填写',
				'confirm_pwd.require'  => '确认密码必须填写',    
			];
			$validate = new Validate($rule, $msg);
			$result   = $validate->check($input_data);
			if (!$result)
			{
				$this->jsAlert($validate->getError());die;
			} 
			/******************* 验证密码 ********************/
			if ($_POST['new_pwd']!=$_POST['confirm_pwd'])
			{
				$this->jsAlert('新密码不一致','/index.php/manager/index/change_password');
			} 
			if (User::login($input_data['name'],$input_data['old_pwd'])==false)
			{
				$this->jsAlert('旧密码错误，请重新修改！','/index.php/manager/index/change_password');
			}

			/******************* 添加数据 ********************/
			$data = [
				'uid' => $input_data['uid'],
				'password'=>md5($input_data['new_pwd']),     
			];
			if(User::updata($data))
			{
				$this->jsAlert('修改成功！','/index.php/Manager/login/index');
			}
			else
			{
				$this->jsAlert('修改失败！','/index.php/manager/index/change_password');
			}
        }  
        $user_info=User::getOne(session('uid'));
        $this->assign('username', $user_info['username']);
        $this->assign('uid', $user_info['uid']);
        return  $this->fetch();
    }
}