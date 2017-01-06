<?php
namespace app\common\model\base;

use think\Model;
use think\Db;
use app\common\model\base\CommonModel;
use app\common\model\base\Users;

/*************************************************  
*ClassName:     UserWeixn
*Description:   用户(微信)模型
*Others:        
*************************************************/
class UsersWeixin extends Model
{
 
    /*************************************************  
    * Function:      getOne
    * Description:   获取一条用户信处
                     联表查user表
    * @param:        mix $open_id|$uid
    * Return:        array 用户信息
    *************************************************/
    static public function getOne($open_id)
    {   
        $array = Db::name('users_weixin')->where(['open_id'=>$open_id])->find();
        if ($array)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $array;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '失败';
        }
        return $result;
    }
    
    /**
     * add 添加用户 
     * 包括users表和users_weixins表  user_customers表
     * @karl
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function add($data)
    {    
        $user['create_time']=time();
        $uid = Db::name('users')->insertGetId($user);
        $customer['uid']=$uid;
		Db::name('users_customers')->insert($customer);
        $data['uid']=$uid;
        $row = Db::name('users_weixin')->insert($data);
        if ($row) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['uid'] = $uid;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "添加失败";
        }
        return $result;
    }
	
    /**
    * editUsers 修改用户信息 
    * @xiao
    * @param    array     $data
    * @return   array     [error_code, error_msg, id]
    * @DateTime 2016-11-22T20:46:59+0800
    */
    static public function edit($open_id,$data)
    {   
        $res = Db::name('users_weixin')->where(['open_id'=>$open_id])->update($data);
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = "";
            $result['data'] = $res;
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = "修改失败";
        }
        return $result;
    }

    /*************************************************  
    * Function:      getOneinfo
    * Description:   获取一条用户信处
                     联表查user表
    * @param:        mix $open_id|$uid
    * Return:        array 用户信息
    *************************************************/
    static public function getOneinfo($uid)
    {   
        $array = Db::name('users_weixin')->where(['uid'=>$uid])->find();
        if ($array)
        {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['data'] = $array;
        }
        else
        {
            $result['error_code'] = 1;
            $result['error_msg'] = '失败';
        }
        return $result;
    }
}

?>