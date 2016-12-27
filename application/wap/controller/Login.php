<?php
namespace app\wap\controller;

use app\common\model\Goods;
use think\Controller;
use think\View;
use think\Request;
use think\Session;
use think\Validate;

/*************************************************
 * @ClassName:      login
 * @Description:   前端界面控制器
 * @author:        pk
 * @DateTime      2016-10-10 11:40:36+0800
 *************************************************/
class Login extends WapBase
{
     /*************************************************
     * Function:      login
     * Description:   登录界面
     * @param:        void
     * Return:        void
     *************************************************/
    public function login()
    {
        return $this->fetch();
    }

    /*************************************************
     * Function:      register
     * Description:   注册界面
     * @param:        void
     * Return:        void
     *************************************************/
    public function register()
    {
        return $this->fetch();
    }

    /*************************************************
     * Function:      forgetPassword
     * Description:   找回密码
     * @param:        void
     * Return:        void
     *************************************************/
    public function forgetPassword()
    {
        return $this->fetch();
    }

}