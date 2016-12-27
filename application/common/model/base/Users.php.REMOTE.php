<?php
namespace app\common\model\base;

use think\Model;
use think\Db;
use think\Session;

/*************************************************  
*ClassName:     UsersManagers
*Description:   商户管理用户类
*Others:        
*************************************************/

class Users extends Model
{
    /**
     * init 初始化
     * @karl
     * @DateTime 2016-07-31T23:23:02+0800
     */
    // static public function init()
    // {
    //     parent::$table_name = "users";
    // }
    
    /*************************************************  
    * Function:      login
    * Description:   得到登陆信息
    * @param: string $username
    * @param：string $password     
    * Return:        fix 用户信息,包括access token,失败返回false
    *************************************************/
    static public function login($username, $password)
    {
        $password = md5($password);
        $row = self::get(['username' => $username, 'password' => $password]);
        $return = [];
        if ($row)
        {
            $return = $row->data;
            $access = UserAccess::open($row['uid']);
            $return['access_token'] = $access['access_token'];
            return $return;
        }
        else
        {
            return false;
        }
    }
    /**
     * add 添加用户 
     * 包括users表和users_weixins表
     * @karl
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function add($data)
    {
        self::init();
        $res = CommonModel::add($data);
        if ($res['error_code'] == 0) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['id'] = $res['id'];
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "添加失败";
        }
        
    }

    /**
     * info 用户信息 
     * @xiao
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function info($uid)
    {   
        $where['uid'] = $uid;
        $res = Db::name('users_customers')->where($where)->find();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * ranking 用户排行
     * @xiao
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function rankingList()
    {   
        $where = '';
        $res = Db::name('users_customers')->where($where)->order("points desc")->select();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * integral 用户积分
     * @xiao
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function integralList($uid)
    {   
        $where['uid'] = $uid;
        $res = Db::name('users_points')->where($where)->select();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }

    /**
     * money 用户钱包
     * @xiao
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function moneyList($uid,$where)
    {   
        $where['uid'] = $uid;
        $res = Db::name('users_money')->where($where)->select();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "查询失败";
        }
        return $result;
    }
}

?>