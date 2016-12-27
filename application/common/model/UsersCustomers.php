<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\Session;

/*************************************************  
*ClassName:     UsersCustomers
*Description:   顾客用户类
*Others:        
*************************************************/

class UsersCustomers extends Model
{

     /**
     * edit 修改顾客 
     * 包括users表和users_customers表
     * @karl
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function edit($uid,$data)
    {
        $where['uid'] = $uid;
        $res = Db::name('users_customers')->where($where)->update($data);
        if ($res)
        {
           $result['error_code'] = 0;
           $result['error_msg'] = "";
        }
        else 
        {
           $result['error_code'] = 1;
           $result['error_msg'] = "修改失败";
        } 
        return $result;
        
        
    }
}

?>