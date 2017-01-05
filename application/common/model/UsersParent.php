<?php
namespace app\common\model;

use think\Model;
use think\Db;

use app\common\model\base\CommonModel;
use app\common\model\base\Users;
/*************************************************  
*ClassName:     UsersParent
*Description:   用户父级模型
*Others:        
*************************************************/
class UsersParent extends Model
{

    /**
     * getParent 得到父级uid
     * @xiao
     * @DateTime 2016-11-27T21:47:40+0800
     * @param    int                   $uid
     * @return   array                 [error_code, error_msg, pid]
     */
    static public function getParent($uid)
    {   
        $where['uid'] = $uid;
        $res = Db::name('users_parent')->where($where)->find();
        if ($res) {
            $result['error_code'] = 0;
            $result['error_msg'] = '';
            $result['pid'] = $res['parent'];
        }else{
            $result['error_code'] = 1;
            $result['error_msg'] = '查询失败';
        }
        return $result;
    }

    /**
     * getChildren 得到指定级子uid
     * @xiao
     * @DateTime 2016-11-27T21:49:55+0800
     * @param    integer                  $uid[] = ['uid'=> ''];   
     * @param    integer                  $level 第几级
     * @return   array                    [error_code, error_msg, data]                 
     */
    static public function getChildren($uid, $level = 1)
    {
        static $count=0;
        static $result=array();
        if ($uid) {
            foreach ($uid as $v) {
                $where['parent'] = $v['uid'];
                $res = Db::name('users_parent')->where($where)->select();
                if ($res) {
                    foreach ($res as $key => $v) {
                        $arr[] = ['uid' => $v['uid']];
                        $str[] = $v['uid'];
                    }
                    $result[$count] = implode(',',$str);
                }else{
                    $arr = '';
                }
            }
            $count++;
            if ($count < $level ) {
                self::getChildren($arr,$level);
            }
        }
        return $result;
    }
}

?>