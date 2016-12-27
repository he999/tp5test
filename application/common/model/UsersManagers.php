<?php
namespace app\common\model;

use think\Model;
use think\Db;
use think\Session;

/*************************************************  
*ClassName:     UsersManagers
*Description:   商户管理用户类
*Others:        
*************************************************/

class UsersManagers extends CommonModel
{
    /**
     * init 初始化
     * @karl
     * @DateTime 2016-07-31T23:23:02+0800
     */
    static public function init()
    {
        parent::$table_name = "users_managers";
    }

     /**
     * addManager 添加微信用户 
     * 包括users表和users_managers表
     * @karl
     * @param    array     $data
     * @return   array     [error_code, error_msg, id]
     * @DateTime 2016-11-22T20:46:59+0800
     */
    static public function addManager($data)
    {

    }   

}

?>